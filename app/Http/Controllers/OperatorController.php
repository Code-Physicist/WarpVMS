<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use JWTFactory;
use Mail;
use Carbon\Carbon;
use DB;
use Exception;

class OperatorController extends AppController
{
    public function OperatorPage(Request $request)
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
        return response()->view("operator", $check["u_data"])->withCookie($cookie);
    }

    public function GetOperators(Request $request)
    {
        $chk = $this->CheckAdmin($request);
        if(!$chk["is_ok"]) {
            return ["status" => "I"];
        }

        $admin_level_id = $chk["u_data"]["admin_level_id"];
        $dept_id = $chk["u_data"]["dept_id"];

        //0 = disable, 1 = enable, 2 = all
        $status = $request->status;

        $query = DB::table("PkAdminweb as a");

        switch($admin_level_id) {
            case "2":
                $query->where(function ($q) {
                    $q->where("a.admin_level_id", "2");
                    $q->orWhere("a.admin_level_id", "3");
                });
                break;
            case "4":
                $query->where(function ($q) {
                    $q->where("a.admin_level_id", "4");
                    $q->orWhere("a.admin_level_id", "5");
                });
                break;
            default:
                return ["status" => "F", "message" => "Invalid admin_level_id"];
        }

        if($status != 2) { //0 => disable, 1 => enable, 2 => all
            $query->where('a.active', '=', $status);
        }
        $query->leftJoin('PkDepartments as d', 'd.DeptID', '=', 'a.Ternsubcode')
            ->where(function ($q) use ($dept_id) {
                $q->where('d.DeptID', $dept_id);
                $q->orWhere('d.SupDepID', $dept_id);
            })
            ->select('a.admin_ID as id', 'a.admin_level_id as admin_level_id', 'a.adminname as email', 'a.name as name', 'a.active as is_active', 'd.DeptID as dept_id', 'd.Fullname as full_name');
        $operators = $query->get();

        //Return response and refresh cookie
        return $this->MakeResponse(["status" => "T", "data_list" => $operators], $chk);

    }

    public function CreateOperator(Request $request)
    {
        $chk = $this->CheckAdmin($request);
        if(!$chk["is_ok"]) {
            return ["status" => "I"];
        }

        $dept_id = $request->dept_id;

        //6 digit random number
        $pass = strval(random_int(100000, 999999));

        if($chk["u_data"]["dept_id"] == 0) {
            $dept_id = 0;
        }

        $operator = [
            "adminname" => $request->email,
            "name" => $request->name,
            "Ternsubcode" => $dept_id,
            "admin_level_id" => $request->admin_level_id,
            "password1" => strtoupper(md5($pass)),
            "xtimeflag" => 0,
            "ChangeFlag" => 0,
            "xtime" => Carbon::now(),
            "exptime" => Carbon::now()->addDays(30),
            "active" => 1,
        ];

        DB::table('PkAdminweb')->insert($operator);
        return ["status" => "T", "pass" => $pass];
    }

    public function UpdateOperator(Request $request)
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
                        "admin_level_id" => $request->admin_level_id,
                    ]
                );
        return ["status" => "T"];

    }

    public function UpdateOperatorEDB(Request $request)
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

    public function GetOperatorDepts(Request $request)
    {
        $chk = $this->CheckAdmin($request);
        if(!$chk["is_ok"]) {
            return ["status" => "I"];
        }

        $dept_id = $chk["u_data"]["dept_id"];
        $depts = DB::table("PkDepartments as d")
            ->where('d.IsActive', 1)
            ->where('d.DeptID', '<>', 0)
            ->where(function ($q) use ($dept_id) {
                $q->where('d.DeptID', $dept_id);
                $q->orWhere('d.SupDepID', $dept_id);
            })
            ->orderBy('d.DeptID')
            ->select('d.DeptID as dept_id', 'd.Fullname as full_name')
            ->get();

        return ["status" => "T", "data_list" => $depts];
    }

}
