<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use JWTFactory;
use QrCode;
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

    public function SendInviteEmail(Request $request)
    {
        $contacts = $request->contacts;
        $invite_id = $request->invite_id;

        $hexString = dechex($invite_id);
        $code = "VMS" . str_pad($hexString, 8, "0", STR_PAD_LEFT);
        $qr_image = QrCode::format("png")->size(512)->generate($code);
        $data = array("qr_image" => $qr_image);

        $emails = [];
        foreach ($contacts as $contact) {
            array_push($emails, $contact["email"]);
        }

        Mail::send("emails.invite", $data, function ($message) use ($emails) {
            $message->to($emails)
                    ->subject("QR Code Access");
            $message->from("VMS_Admin@gmail.com", "VMS Admin");
        });
        return ["status" => "F"];

    }

    public function TestSendEmail(Request $request)
    {

        $code = "VMS" . str_pad("1", 8, "0", STR_PAD_LEFT);
        $qr_image = QrCode::format("png")->size(512)->generate($code);
        $data = array("qr_image" => $qr_image);

        $emails = ["tonchanin@hotmail.com"];

        Mail::send("emails.invite", $data, function ($message) use ($emails) {
            $message->to($emails)
                    ->subject("QR Code Access");
            $message->from("VMS_Admin@gmail.com", "VMS Admin");
        });
        return ["status" => "T"];

    }
    public function SendAdminEmail(Request $request)
    {
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
        return ["status" => "T", "message" => $tenant["adminname"]];
    }
}
