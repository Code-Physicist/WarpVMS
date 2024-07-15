<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use JWTFactory;
use Mail;
use Carbon\Carbon;
use DB;
use Exception;

class InvitationController extends AppController
{
    public function InvitationPage(Request $request)
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
        return response()->view("invitation", $check["u_data"])->withCookie($cookie);
    }

    public function GetInvitationDepts(Request $request)
    {
        $chk = $this->CheckAdmin($request);
        if(!$chk["is_ok"]) {
            return ["status" => "I"];
        }

        $dept_level = $chk["u_data"]["dept_level"];
        $dept_id = $chk["u_data"]["dept_id"];

        $query = DB::table("PkDepartments")
            ->where('IsActive', '=', 1)
            ->where('DeptID', '<>', 0);

        if($dept_level == "1") {
            $query->where(function ($q) use ($dept_id) {
                $q->where('DeptID', $dept_id);
                $q->orWhere('SupDepID', $dept_id);
            });
        } elseif($dept_level == "2") {
            $query->where('DeptID', $dept_id);
        } else {
            //Invalid. Will prevent later
        }



        $depts = $query->select('DeptID as dept_id', 'Fullname as full_name')
            ->orderByDesc('DeptID')
            ->get();

        return ["status" => "T", "data_list" => $depts];
    }
}
