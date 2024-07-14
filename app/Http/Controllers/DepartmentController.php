<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use JWTFactory;
use Mail;
use Carbon\Carbon;
use DB;
use Exception;

class DepartmentController extends AppController
{
    public function DepartmentPage(Request $request)
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
        return response()->view("department", $check["u_data"])->withCookie($cookie);
    }

    public function CreateDepartment(Request $request)
    {
        try {
            $max_dept_id = DB::table('PkDepartments')->max("DeptID");

            $dept = [
                "DeptID" => $max_dept_id + 1,
                "SupDepID" => "0",
                "Fullname" => $request->Fullname,
                "DeptName" => $request->DeptName,
                "Zdesc" => $request->DeptName,
                "Floor" => $request->Floor,
                "Tel1" => $request->Tel1,
                "Tel2" => $request->Tel2,
                "Level1" => "1",
                "IsActive" => "1",
            ];

            DB::table('PkDepartments')->insert($dept);
            return ["success" => true];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }

    }

    public function UpdateDepartment(Request $request)
    {
        $state = $request->state;
        $dept_id = $request->dept_id;

        if($state === 2) {
            $set_status = $request->set_status;
            DB::table('PkDepartments')->where("DeptID", $dept_id)->update(["IsActive" => $set_status]);
            return ["result" => "T"];
        } else {
            return ["result" => "F"];
        }

    }

    public function GetDepts(Request $request)
    {
        $check = $this->CheckAdmin($request);
        if(!$check["is_ok"]) {
            return ["status" => "I"];
        }

        $admin_level_id = $check["u_data"]["admin_level_id"];
        $dept_id = $check["u_data"]["dept_id"];

        //0 = disable, 1 = enable, 2 = all
        $status = $request->status;

        $query = DB::table("PkDepartments As d1");
        if($status != "2") {
            $query->where('d1.IsActive', '=', $status);
        }

        if($dept_id == "0") {
            $query->where('d1.DeptID', '<>', 0);
        } else {
            $query->where(function ($q) use ($dept_id) {
                $q->where('d1.DeptID', $dept_id);
                $q->orWhere('d1.SupDepID', $dept_id);
            });
        }

        $query->leftJoin('PkDepartments as d2', 'd2.DeptID', '=', 'd1.SupDepID')
        ->select(
            'd1.DeptID as dept_id',
            'd1.DeptName as short_name',
            'd1.Fullname as full_name',
            'd1.Floor as floor',
            'd1.Tel1 as phone1',
            'd1.Tel2 as phone2',
            'd2.DeptID as sup_dept_id',
            'd2.DeptName as sup_dept_name'
        )
        ->orderByDesc('d1.DeptID');

        $depts = $query->get();

        //Return response and refresh cookie
        return $this->MakeResponse(["status" => "T", "data_list" => $depts], $check);
    }

    //Get parent departments
    //This function is for Tenants and Tenant Operators
    //No need for guarding conditions
    public function GetSupDepts(Request $request)
    {
        $check = $this->CheckAdmin($request);
        if(!$check["is_ok"]) {
            return ["status" => "I"];
        }

        $admin_level_id = $check["u_data"]["admin_level_id"];
        $dept_id = $check["u_data"]["dept_id"];

        $depts = DB::table("PkDepartments")
            ->where('Level1', '=', 1)
            ->where('DeptID', $dept_id)
            ->select('DeptID', 'Fullname')
            ->orderBy('DeptID')
            ->get();

        return ["status" => "T", "data_list" => $depts];
    }

}
