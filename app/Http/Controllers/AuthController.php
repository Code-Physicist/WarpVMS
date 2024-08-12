<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use JWTFactory;
use Mail;
use Carbon\Carbon;
use DB;
use Exception;

class AuthController extends AppController
{
    public function LoginPage(Request $request)
    {
        $check = $this->CheckAdmin($request);
        if(!$check["is_ok"]) {
            return response()->view("login");
        }
        if($check["u_data"]["pw_change"]) {
            return redirect("/admin/pass_reset");
        }

        //No need to refresh the cookie here. Let do it after redirect
        return redirect("/admin/dashboard");
    }

    public function Login(Request $request)
    {
        $user = $request->email;
        $pass = strtoupper(md5($request->pass));

        $admin = DB::table('PkAdminweb')
                  ->where('adminname', '=', $user)
                  ->first();

        //L => Account Lock, I => Invalid User, T => Success
        if(!$admin) {
            return ["status" => "I"];
        }

        if($admin->xtime > Carbon::now()) {
            return ["status" => "L"];

        } elseif($admin->password1 !== $pass) {
            $xtimeflag = $admin->xtimeflag;
            if($xtimeflag < 5) {
                DB::table('PkAdminweb')
                ->where("adminname", $user)
                ->update(["xtimeflag" => $xtimeflag + 1]);
            } else {
                DB::table('PkAdminweb')
                ->where("adminname", $user)
                ->update(["xtimeflag" => 0, "xtime" => Carbon::now()->addMinutes(10)]);
            }

            return ["status" => "I"];

        } else {
            //Reset flag after successful login
            DB::table('PkAdminweb')
              ->where("adminname", $user)
              ->update(["xtimeflag" => 0]);

            //Get dept
            $dept = $this->GetDeptByID($admin->Ternsubcode);
            $admin_level = $this->GetAdminLevelByID($admin->admin_level_id);

            //Get password reset status
            $pw_change = false;
            if($admin->exptime < Carbon::now() || $admin->ChangeFlag === '1') {
                $pw_change = true;
            }

            $dept_id = $dept->DeptID;
            $dept_name = $dept->Fullname;
            /*if($dept_id == "0") {
                $dept_name = "BTS Building";
            }*/

            $cookie = $this->CreateVMSCookie([
                "admin_id" => $admin->admin_ID,
                "admin_level_id" => $admin->admin_level_id,
                "admin_level_name" => $admin_level->Zdesc,
                "admin_name" => $admin->adminname,
                "name" => $admin->name,
                "dept_id" => $dept_id,
                "sup_dept_id" => $dept->SupDepID,
                "dept_name" => $dept_name,
                "dept_level" => $dept->Level1,
                "pw_change" => $pw_change,
            ]);

            return response(["status" => "T"])->withCookie($cookie);
        }
    }

    public function Reset(Request $request)
    {
        $user = $request->email;
        $admin = DB::table('PkAdminweb')
                    ->where('adminname', '=', $user)
                    ->first();

        if(!$admin) {
            return ["status" => "I"];
        }

        $acct = DB::table('VC_reset_account')
                    ->where('adminname', '=', $user)
                    ->first();

        if($acct) {
            $time_left = 60 - Carbon::now()->diffInSeconds($acct->create_time);

            if($acct && $time_left > 0) {
                return ["status" => "W", "time_left" => $time_left];
            }

            DB::table('VC_reset_account')
            ->where('adminname', '=', $user)
            ->delete();
        }

        $reset_code = rand(1000000, 9999999);

        DB::table('VC_reset_account')->insert([
            "adminname" => $user,
            "reset_code" => $reset_code,
            "create_time" => Carbon::now(),
        ]);

        return ["status" => "T", "email" => $user, "reset_code" => $reset_code];
    }

    public function SendResetEmail(Request $request)
    {
        $email = $request->email;
        $data = array(
            "reset_url" => $request->reset_url,
        );

        try {
            Mail::send("emails.reset", $data, function ($message) use ($email) {
                $message->to($email)
                        ->subject("Reset Password for VMS");
                $message->from("VMS_Admin@gmail.com", "VMS Admin");
            });
        } catch (Exception $e) {
            return ["status" => $e->getMessage()];
        }


    }

    public function ResetPasswordPage(Request $request)
    {
        $email = $request->email;
        $rst_code = $request->rst_code;
        $acct = DB::table('VC_reset_account')
                ->where('adminname', '=', $email)
                ->where('reset_code', '=', $rst_code)
                ->first();

        if(!$acct) {
            return "Invalid request";
        }

        if($acct->create_time < Carbon::now()->addMinutes(-20)) {
            return "Page Expired";
        }

        return response()->view("reset", ["email" => $email, "rst_code" => $rst_code]);
    }

    public function ResetPassword(Request $request)
    {
        $email = $request->email;
        $rst_code = $request->rst_code;
        $pass1 = $request->pass1;
        $pass2 = $request->pass2;

        if($pass1 === "" || $pass2 === "" || $pass1 !== $pass2) {
            return ["status" => "M"];
        }

        $acct = DB::table('VC_reset_account')
                ->where('adminname', '=', $email)
                ->where('reset_code', '=', $rst_code)
                ->first();

        if(!$acct || $acct->create_time < Carbon::now()->addMinutes(-20)) {
            return ["status" => "I"];
        }

        $pass1 = strtoupper(md5($pass1));

        DB::table('PkAdminweb')
            ->where("adminname", $email)
            ->update(
                [
                    "password1" => $pass1,
                    "xtimeflag" => 0,
                    "xtime" => Carbon::now()->addMinutes(-10),
                    "exptime" => Carbon::now()->addDays(90)
                ]
            );

        return ["status" => "T"];
    }

    public function Logout(Request $request)
    {
        $cookie = Cookie($this->TokenName, "");
        return redirect('/admin/login')->withCookie($cookie);
    }
}
