<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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
        $chk = $this->CheckAdmin($request);
        if (!$chk["is_ok"]) {
            return redirect("/admin/login");
        }
        if ($chk["u_data"]["pw_change"]) {
            return redirect("/admin/pass_change");
        }

        //Refresh cookie
        $cookie = $this->CreateVMSCookie($chk["u_data"]);
        return response()->view("department", $chk["u_data"])->withCookie($cookie);
    }

    public function CreateDepartment(Request $request)
    {
        $chk = $this->CheckAdmin($request);
        if (!$chk["is_ok"]) {
            return response(["status" => "I"], 401);
        }

        try {
            $dept_level = $chk["u_data"]["dept_level"];
            $new_dept_level = "";

            if ($dept_level == "0") {
                $new_dept_level = "1";
            } elseif ($dept_level == "1") {
                $new_dept_level = "2";
            } else {
                return response(["status" => "error"], 403);
            }

            $max_dept_id = DB::table('PkDepartments')->max("DeptID");
            $dept = [
                    "DeptID" => $max_dept_id + 1,
                    "SupDepID" => $request->sup_dept_id,
                    "Fullname" => $request->full_name,
                    "DeptName" => $request->dept_name,
                    "Zdesc" => $request->dept_name,
                    "Floor" => $request->floor,
                    "Tel1" => $request->phone1,
                    "Tel2" => $request->phone2,
                    "Level1" => $new_dept_level,
                    "IsActive" => "1",
            ];

            DB::table('PkDepartments')->insert($dept);
            return ["status" => "T"];
        } catch (Exception $e) {
            return response(["status" => "F", "error" => $e->getMessage()], 422);
        }
    }

    public function UpdateDepartment(Request $request)
    {
        $chk = $this->CheckAdmin($request);
        if (!$chk["is_ok"]) {
            return ["status" => "I"];
        }

        $dept_id = $request->id;

        DB::table('PkDepartments')->where("DeptID", $dept_id)
        ->update(
            [
                "Fullname" => $request->full_name,
                "DeptName" => $request->dept_name,
                "Zdesc" => $request->dept_name,
                "Floor" => $request->floor,
                "Tel1" => $request->phone1,
                "Tel2" => $request->phone2
            ]
        );
        return ["status" => "T"];

    }

    public function UpdateDepartment2(Request $request)
    {
        $chk = $this->CheckAdmin($request);
        if (!$chk["is_ok"]) {
            return ["status" => "I"];
        }

        $dept_id = $request->dept_id;
        $status = $request->status;
        DB::table('PkDepartments')->where("DeptID", $dept_id)->update(["IsActive" => $status]);
        return ["status" => "T"];
    }

    public function GetDepartments(Request $request)
    {
        $chk = $this->CheckAdmin($request);
        if (!$chk["is_ok"]) {
            return response(["status" => "I"], 401);
        }

        $admin_level_id = $chk["u_data"]["admin_level_id"];
        $dept_id = $chk["u_data"]["dept_id"];

        //0 = disable, 1 = enable, 2 = all
        $status = $request->status;
        $draw = $request->draw;
        $start = $request->start;
        $length = $request->length;
        $search = $request->input('search.value');

        $orderColumnIndex = $request->input('order.0.column'); // Index of the column
        $orderDirection = $request->input('order.0.dir'); // Direction of sorting

        $columns = $request->input('columns'); // All columns data
        $orderColumnName = $columns[$orderColumnIndex]['data']; // Get column name from index

        $query = DB::table("PkDepartments As d1");
        if ($status != "2") {
            $query->where('d1.IsActive', '=', $status);
        }
        $query->where('d1.SupDepID', '=', $dept_id);

        $count = $query->count();
        $f_count = $count;

        $query->leftJoin('PkDepartments as d2', 'd2.DeptID', '=', 'd1.SupDepID');
        if ($search != "") {
            $query->where(function ($q) use ($search) {
                $q->where('d1.DeptName', 'like', '%'. $search .'%');
                $q->orWhere('d1.Tel1', 'like', '%'. $search .'%');
                $q->orWhere('d1.Tel2', 'like', '%'. $search .'%');
            });
            $f_count = $query->count();
        }

        $query->select(
            'd1.DeptID as id',
            'd1.DeptName as dept_name',
            'd1.Fullname as full_name',
            'd1.Floor as floor',
            'd1.Tel1 as phone1',
            'd1.Tel2 as phone2',
            'd1.IsActive as is_active',
            'd2.DeptID as sup_dept_id',
            'd2.DeptName as sup_dept_name'
        );
        //->orderByDesc('d1.DeptID');

        $query->orderBy($orderColumnName, $orderDirection);

        $depts = $query->offset($start)->limit($length)->get();

        $response = [
            "draw" => $draw,
            "iTotalRecords" => $count,
            "iTotalDisplayRecords" => $f_count,
            "aaData" => $depts
        ];

        //Return response and refresh cookie
        return $this->MakeResponse($response, $chk);
    }

}
