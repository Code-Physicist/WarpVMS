<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use JWTFactory;
use Mail;
use Carbon\Carbon;
use DB;
use Exception;

class DashboardController extends AppController
{
    public function DashboardPage(Request $request)
    {
        $check = $this->CheckAdmin($request);
        if (!$check["is_ok"]) {
            return redirect("/admin/login");
        }
        if ($check["u_data"]["pw_change"]) {
            return redirect("/admin/pass_change");
        }

        //Refresh VMS cookie
        $cookie = $this->CreateVMSCookie($check["u_data"]);
        return response()->view("dashboard", $check["u_data"])->withCookie($cookie);
    }

    public function GetTotals(Request $request)
    {
        $chk = $this->CheckAdmin($request);
        if (!$chk["is_ok"]) {
            return ["status" => "I"];
        }

        $dept_level = $chk["u_data"]["dept_level"];
        $dept_id = $chk["u_data"]["dept_id"];
        $s_depts = DB::table("PkDepartments")->where('SupDepID', '=', $dept_id)->get();

        $last365 = Carbon::now()->addDays(-365);

        $query = DB::table("VC_invite");
        $query->where('end_date', '>=', $last365);
        if ($dept_level == 1) {
            $query->where(function ($q) use ($dept_id, $s_depts) {
                $q->where('to_dept_id', '=', $dept_id);
                if ($s_depts) {
                    foreach ($s_depts as $s_dept) {
                        $q->orWhere('to_dept_id', $s_dept->DeptID);
                    }
                }
            });
        } elseif ($dept_level == 2) {
            $query->where('to_dept_id', '=', $dept_id);
        }
        $total_invitations = $query->count();

        $query = DB::table("PkDepartments");
        if ($dept_level > 0) {
            $query->where('SupDepID', '=', $dept_id)->count();
        }
        $total_departments = $query->count() + 1;

        $query = DB::table("PkAdminweb")->where('admin_level_id', '=', 4);
        if ($dept_level == 1) {
            $query->where(function ($q) use ($dept_id, $s_depts) {
                $q->where('Ternsubcode', '=', $dept_id);
                if ($s_depts) {
                    foreach ($s_depts as $s_dept) {
                        $q->orWhere('Ternsubcode', $s_dept->DeptID);
                    }
                }
            });
        } elseif ($dept_level == 2) {
            $query->where('Ternsubcode', '=', $dept_id);
        }
        $total_tenants = $query->count();

        $query = DB::table("PkAdminweb")->where('admin_level_id', '=', 3);
        if ($dept_level == 1) {
            $query->where(function ($q) use ($dept_id, $s_depts) {
                $q->where('Ternsubcode', '=', $dept_id);
                if ($s_depts) {
                    foreach ($s_depts as $s_dept) {
                        $q->orWhere('Ternsubcode', $s_dept->DeptID);
                    }
                }
            });
        } elseif ($dept_level == 2) {
            $query->where('Ternsubcode', '=', $dept_id);
        }
        $total_operators = $query->count();

        return [
            "status" => "T",
            "total_invitations" => $total_invitations,
            "total_departments" => $total_departments,
            "total_tenants" => $total_tenants,
            "total_operators" => $total_operators
        ];
    }

    public function GetVStats(Request $request)
    {
        $check = $this->CheckAdmin($request);
        if (!$check["is_ok"]) {
            return ["status" => "I"];
        }

        $now = Carbon::now();
        $last7 = (clone $now)->addDays(-7);
        $last7q = clone $last7;

        $expected_v_dict = [];
        $real_v_dict = [];
        while ($last7 <= $now) {
            $expected_v_dict[$last7->format('Y-m-d')] = 0;
            $real_v_dict[$last7->format('Y-m-d')] = 0;
            $last7->addDay();
        }

        $invitations = DB::table("VC_invite")->where('end_date', '>=', $last7q)->get();

        foreach ($invitations as $inv) {
            $count = DB::table("VC_invite_visitor")
                ->where('invite_id', '=', $inv->id)
                ->where('pdpa_accept', '=', 1)
                ->count();
            $start_date = Carbon::parse($inv->start_date);
            $end_date = Carbon::parse($inv->end_date);
            while ($start_date <= $end_date) {
                $start_date_str = $start_date->format('Y-m-d');
                if (array_key_exists($start_date_str, $expected_v_dict)) {
                    $expected_v_dict[$start_date_str] += $count;
                }
                $start_date->addDay();
            }
        }

        $categories = array();
        $expected = array();
        $arrived = array();
        foreach ($expected_v_dict as $key => $value) {
            // your code here
            array_push($categories, $key);
            array_push($expected, $value);
            array_push($arrived, $real_v_dict[$key]);
        }
        return ["status" => "T", "categories" => $categories, "expected" => $expected, "arrived" => $arrived];
    }
}
