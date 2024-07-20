<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use JWTFactory;
use Mail;
use Carbon\Carbon;
use DB;
use Exception;

class TenantController extends AppController
{
    public function TenantPage(Request $request)
    {
        $check = $this->CheckAdmin($request);
        if(!$check["is_ok"]) {
            return redirect("/admin/login");
        }
        if($check["u_data"]["pw_change"]) {
            return redirect("/admin/pass_change");
        }

        //Refresh cookie
        $cookie = $this->CreateVMSCookie($check["u_data"]);
        return response()->view("tenant", $check["u_data"])->withCookie($cookie);
    }

    public function GetTenants(Request $request)
    {
        $chk = $this->CheckAdmin($request);
        if(!$chk["is_ok"]) {
            return ["status" => "I"];
        }

        //0 = disable, 1 = enable, 2 = all
        $status = $request->status;

        //0 = all, otherwise DeptID
        $dept_id = $request->dept_id;

        $query = DB::table("PkAdminweb as a");
        if($status != 2) {
            $query->where('a.active', '=', $status);
        }
        if($dept_id != 0) {
            $query->where('a.DeptID', '=', $dept_id);
        }

        $tenants = $query->where("a.admin_level_id", 4)
        ->leftJoin('PkDepartments as d', 'd.DeptID', '=', 'a.Ternsubcode')
        ->select('a.admin_ID as id', 'a.adminname as email', 'a.name as name', 'a.active as is_active', 'd.DeptID as dept_id', 'd.Fullname as full_name')
        ->orderByDesc('d.DeptID')
        ->get();

        return $this->MakeResponse(["result" => "T", "data_list" => $tenants], $chk);
    }

    public function CreateTenant(Request $request)
    {
        $chk = $this->CheckAdmin($request);
        if(!$chk["is_ok"]) {
            return ["status" => "I"];
        }

        //6 digit random number
        $pass = strval(random_int(100000, 999999));

        $tenant = [
            "adminname" => $request->email,
            "name" => $request->name,
            "Ternsubcode" => $request->dept_id,
            "admin_level_id" => 4, //Tenant
            "password1" => strtoupper(md5($pass)),
            "xtimeflag" => 0,
            "ChangeFlag" => 0,
            "xtime" => Carbon::now(),
            "exptime" => Carbon::now()->addDays(30),
            "active" => 1,
        ];

        DB::table('PkAdminweb')->insert($tenant);
        return $this->MakeResponse(["status" => "T", "pass" => $pass], $chk);
    }

    public function UpdateTenant(Request $request)
    {
        $chk = $this->CheckAdmin($request);
        if(!$chk["is_ok"]) {
            return ["status" => "I"];
        }

        $admin_id = $request->id;

        DB::table('PkAdminweb')->where("admin_ID", $admin_id)
                ->update(
                    [
                        "adminname" => $request->email,
                        "name" => $request->name,
                        "Ternsubcode" => $request->dept_id,
                    ]
                );
        return ["status" => "T"];

    }

    public function UpdateTenantEDB(Request $request)
    {
        $chk = $this->CheckAdmin($request);
        if(!$chk["is_ok"]) {
            return ["status" => "I"];
        }

        $admin_id = $request->id;
        $status = $request->status;
        DB::table('PkAdminweb')->where("admin_ID", $admin_id)->update(["active" => $status]);
        return ["status" => "T"];

    }

    public function SendTenantEmail(Request $request)
    {
        try {
            $admin_url = $request->admin_url;
            $tenant = $request->tenant;
            $email = $tenant["adminname"];
            $dept = DB::table('PkDepartments')->where('DeptID', $tenant["Ternsubcode"])->first();

            $data = array(
                'admin_url' => $admin_url,
                'admin_name' => $tenant["name"],
                'admin_dept' => $dept->Fullname,
                'username' => $tenant["adminname"],
                'password' => $tenant["pass"],
            );

            Mail::send("emails.tenant", $data, function ($message) use ($email) {
                $message->to($email)
                        ->subject("Your Tenant Account");
                $message->from("VMS_Admin@gmail.com", "VMS Admin");
            });
            return ["result" => "T", "message" => $tenant["adminname"]];
        } catch (Exception $e) {
            return ["result" => "F", "message" => $e->getMessage()];
        }
    }

    public function GetLv1Depts(Request $request)
    {
        $check = $this->CheckAdmin($request);
        if(!$check["is_ok"]) {
            return ["status" => "I"];
        }

        $depts = DB::table("PkDepartments as d")
            ->where('Level1', '=', 1)
            ->select('d.DeptID as dept_id', 'd.Fullname as full_name')
            ->get();

        return ["status" => "T", "data_list" => $depts];
    }

}
