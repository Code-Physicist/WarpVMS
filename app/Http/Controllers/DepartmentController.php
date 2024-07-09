<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use JWTFactory;
use Mail;
use Carbon\Carbon;
use DB;
use Exception;

class DepartmentController extends UserController
{
    public function Get(Request $request)
    {
        $result = $this->CheckVMSCookie($request);
        if(!$result["is_ok"]) {
            return redirect("/admin");
        }
        if($result["u_data"]["pw_change"]) {
            return redirect("/admin/pass_change");
        }

        //Renew cookie
        $cookie = $this->CreateVMSCookie($result["u_data"]);
        return response()->view("vms.department", $result["u_data"])->withCookie($cookie);
    }

    public function GetDepartments(Request $request)
    {
        try {
            $admin_level_id = $request->admin_level_id;
            $dept_id = $request->dept_id;
            //0 => disable, 1 => enable, 2 => all
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
            ->select('d1.*', 'd2.DeptName as SupDepName')
            ->orderByDesc('d1.DeptID');

            $depts = $query->get();
            return ["is_valid" => true, "depts" => $depts];
        } catch (Exception $e) {
            return ["is_valid" => false, "message" => $e->getMessage()];
        }
    }

    public function GetTenantDepts(Request $request)
    {
        $result = $this->CheckVMSCookie($request);
        if(!$result["is_ok"]) {
            return ["result" => "I"];
        }

        $admin_level_id = $result["u_data"]["admin_level_id"];
        $dept_id = $result["u_data"]["dept_id"];

        try {
            $query = DB::table("PkDepartments")->where('DeptID', '<>', 0);

            //For building admin/operator levels
            if($admin_level_id === '2' ||  $admin_level_id === '3') {
                $query->where('Level1', 1);
            } else {
                $query->where(function ($q) use ($dept_id) {
                    $q->where('DeptID', $dept_id);
                    $q->orWhere('SupDepID', $dept_id);
                });
            }

            $query->select('DeptID', 'Fullname')->orderBy('Fullname');

            $depts = $query->get();

            return ["result" => "T", "depts" => $depts];
        } catch (Exception $e) {
            return ["result" => "F", "message" => $e->getMessage()];
        }
    }

    public function GetOperatorDepts(Request $request)
    {
        $result = $this->CheckVMSCookie($request);
        if(!$result["is_ok"]) {
            return ["result" => "I"];
        }

        $dept_id = $result["u_data"]["dept_id"];

        try {
            $depts = DB::table("PkDepartments")
                    ->where('IsActive', 1)
                    ->where('DeptID', '<>', 0)
                    ->where(function ($q) use ($dept_id) {
                        $q->where('DeptID', $dept_id);
                        $q->orWhere('SupDepID', $dept_id);
                    })
                    ->orderBy('DeptID')
                    ->get();

            return ["result" => "T", "depts" => $depts];
        } catch (Exception $e) {
            return ["result" => "F", "message" => $e->getMessage()];
        }
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

    public function EditDepartment(Request $request)
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

    public function GetTest(Request $request)
    {
        return "Yo";
    }

}
