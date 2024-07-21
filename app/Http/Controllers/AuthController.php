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

    public function Logout(Request $request)
    {
        $cookie = Cookie($this->TokenName, "");
        return redirect('/admin/login')->withCookie($cookie);
    }
}
