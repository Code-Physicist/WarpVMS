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

class PDPAController extends AppController
{
    public function PDPAPage(Request $request)
    {
        $check = $this->CheckAdmin($request);
        if(!$check["is_ok"]) {
            return redirect("/admin/login");
        }
        if($check["u_data"]["pw_change"]) {
            return redirect("/admin/pass_change");
        }

        //Refresh VMS cookie
        $cookie = $this->CreateVMSCookie($check["u_data"]);
        return response()->view("pdpa", $check["u_data"])->withCookie($cookie);
    }

    public function GetPDPAs(Request $request)
    {
        $chk = $this->CheckAdmin($request);
        if(!$chk["is_ok"]) {
            throw ValidationException::withMessages(['I']);
        }

        $draw = $request->draw;
        $start = $request->start;
        $length = $request->length;

        $orderColumnIndex = $request->input('order.0.column'); // Index of the column
        $orderDirection = $request->input('order.0.dir'); // Direction of sorting

        $columns = $request->input('columns'); // All columns data
        $orderColumnName = $columns[$orderColumnIndex]['data']; // Get column name from index
        $query = DB::table("VC_PDPA");
        $count = $query->count();
        $f_count = $count;

        error_log($orderColumnName);

        $query->orderBy($orderColumnName, $orderDirection);
        $pdpas = $query->offset($start)->limit($length)->get();

        $response = [
            "draw" => $draw,
            "iTotalRecords" => $count,
            "iTotalDisplayRecords" => $f_count,
            "aaData" => $pdpas
        ];

        //Return response and refresh cookie
        return $this->MakeResponse($response, $chk);
    }

    public function CreatePDPA(Request $request)
    {
        $chk = $this->CheckAdmin($request);
        if(!$chk["is_ok"]) {
            return ["status" => "I"];
        }

        error_log("1");
        error_log($request->consent);

        $pdpa = [
                "consent" => $request->consent,
                "is_active" => 0,
        ];
        error_log("2");

        DB::table('VC_PDPA')->insert($pdpa);
        return ["status" => "T"];
    }

    public function UpdatePDPA(Request $request)
    {
        $chk = $this->CheckAdmin($request);
        if(!$chk["is_ok"]) {
            return ["status" => "I"];
        }

        $id = $request->id;

        DB::table('VC_PDPA')->where("id", $id)
                ->update(
                    [
                        "consent" => $request->consent,
                    ]
                );
        return ["status" => "T"];
    }

    public function ActivatePDPA(Request $request)
    {
        $chk = $this->CheckAdmin($request);
        if(!$chk["is_ok"]) {
            return ["status" => "I"];
        }

        $id = $request->id;

        DB::beginTransaction();
        try {
            DB::table('VC_PDPA')->update(
                [
                    "is_active" => 0,
                ]
            );

            DB::table('VC_PDPA')->where("id", $id)->update(
                [
                    "is_active" => 1,
                ]
            );

            DB::commit();
            return ["status" => "T"];
        } catch (Exception $e) {
            DB::rollback();
            return ["status" => "F", "message" => $e->getMessage()];
        }
    }

}
