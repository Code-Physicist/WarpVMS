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
        if (!$check["is_ok"]) {
            return redirect("/admin/login");
        }
        if ($check["u_data"]["pw_change"]) {
            return redirect("/admin/pass_change");
        }

        //Refresh cookie
        $cookie = $this->CreateVMSCookie($check["u_data"]);
        return response()->view("operator", $check["u_data"])->withCookie($cookie);
    }

    public function GetOperators(Request $request)
    {
        $chk = $this->CheckAdmin($request);
        if (!$chk["is_ok"]) {
            return response(["status" => "I"], 401);
        }

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

        $admin_level_id = $chk["u_data"]["admin_level_id"];
        $dept_id = $chk["u_data"]["dept_id"];

        $query = DB::table("PkAdminweb as a");
        switch ($admin_level_id) {
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

        if ($status != 2) { //0 => disable, 1 => enable, 2 => all
            $query->where('a.active', '=', $status);
        }

        $count = $query->count();
        $f_count = $count;

        $query->leftJoin('PkDepartments as d', 'd.DeptID', '=', 'a.Ternsubcode')
            ->where(function ($q) use ($dept_id) {
                $q->where('d.DeptID', $dept_id);
                $q->orWhere('d.SupDepID', $dept_id);
            });

        if ($search != "") {
            $query->where(function ($q) use ($search) {
                $q->where('a.name', 'like', '%'. $search .'%');
                $q->orWhere('a.adminname', 'like', '%'. $search .'%');
                $q->orWhere('d.Fullname', 'like', '%'. $search .'%');
            });
            $f_count = $query->count();
        }

        $query->select(
            'a.admin_ID as id',
            'a.admin_level_id as admin_level_id',
            'a.adminname as email',
            'a.name as name',
            'a.active as is_active',
            'd.DeptID as dept_id',
            'd.Fullname as full_name'
        );

        $query->orderBy($orderColumnName, $orderDirection);
        $operators = $query->offset($start)->limit($length)->get();

        $response = [
            "draw" => $draw,
            "iTotalRecords" => $count,
            "iTotalDisplayRecords" => $f_count,
            "aaData" => $operators
        ];

        //Return response and refresh cookie
        return $this->MakeResponse($response, $chk);
    }

    public function CreateOperator(Request $request)
    {
        $chk = $this->CheckAdmin($request);
        if (!$chk["is_ok"]) {
            return ["status" => "I"];
        }

        $dept_id = $request->dept_id;

        if ($chk["u_data"]["dept_id"] == 0) {
            $dept_id = 0;
        }

        //6 digit random number
        $pass = strval(random_int(100000, 999999));

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
        $operator["pass"] = $pass;
        return ["status" => "T", "admin" => $operator];
    }

    public function UpdateOperator(Request $request)
    {
        $chk = $this->CheckAdmin($request);
        if (!$chk["is_ok"]) {
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
        if (!$chk["is_ok"]) {
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
        if (!$chk["is_ok"]) {
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
