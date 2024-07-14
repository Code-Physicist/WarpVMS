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

        $query = DB::table("PkAdminweb");

        switch($admin_level_id) {
            case "2":
                $query->where('PkAdminweb.admin_level_id', '=', "3");
                break;
            case "4":
                $query->where('PkAdminweb.admin_level_id', '=', "5");
                break;
            default:
                return ["status" => "F", "message" => "Invalid admin_level_id"];
        }

        if($status != 2) { //0 => disable, 1 => enable, 2 => all
            $query->where('PkAdminweb.active', '=', $status);
        }
        $operators = $query->get();

        //Return response and refresh cookie
        return $this->MakeResponse(["status" => "T", "operators" => $operators], $chk);

    }

    public function CreateOperator(Request $request)
    {
        $chk = $this->CheckAdmin($request);
        if(!$chk["is_ok"]) {
            return ["status" => "I"];
        }

        $admin_level_id = $chk["u_data"]["admin_level_id"];
        $dept_id = $chk["u_data"]["dept_id"];

        //6 digit random number
        $pass = strval(random_int(100000, 999999));

        if($result["u_data"]["dept_id"] == 0) {
            $dept_id = 0;
        }

        $operator = [
            "adminname" => $request->email,
            "name" => $request->name,
            "Ternsubcode" => $dept_id,
            "admin_level_id" => $admin_level_id,
            "password1" => strtoupper(md5($pass0)),
            "xtimeflag" => 0,
            "ChangeFlag" => 0,
            "xtime" => Carbon::now(),
            "exptime" => Carbon::now()->addDays(30),
            "active" => 1,
        ];

        DB::table('PkAdminweb')->insert($operator);
        return ["result" => "T", "data_list" => $operator, "pass" => $pass];
    }

}
