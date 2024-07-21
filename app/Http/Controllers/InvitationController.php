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

        $dept_id = $chk["u_data"]["dept_id"];

        $query = DB::table("PkDepartments")
            ->where('IsActive', '=', 1)
            ->where('DeptID', '<>', 0);

        $query->where(function ($q) use ($dept_id) {
            $q->where('DeptID', $dept_id);
            $q->orWhere('SupDepID', $dept_id);
        });

        $depts = $query->select('DeptID as dept_id', 'Fullname as full_name')
            ->orderByDesc('DeptID')
            ->get();

        return ["status" => "T", "data_list" => $depts];
    }

    public function GetContactByEmail(Request $request)
    {
        $chk = $this->CheckAdmin($request);
        if(!$chk["is_ok"]) {
            return ["status" => "I"];
        }

        $email = $request->email;
        $dept_level = $chk["u_data"]["dept_level"];
        $sup_dept_id = $chk["u_data"]["sup_dept_id"];
        $dept_id = $chk["u_data"]["dept_id"];

        if($dept_level > 1) {
            $dept_id = $sup_dept_id;
        }

        $contact = DB::table('PkContacts')
        ->where('email', '=', $email)
        ->where('dept_id', '=', $dept_id)
        ->first();

        return ["status" => "T", "contact" => $contact];
    }

    public function UpsertContact(Request $request)
    {
        $chk = $this->CheckAdmin($request);
        if(!$chk["is_ok"]) {
            return ["status" => "I"];
        }

        $id = $request->id;
        $contact = [
            "email" => $request->email,
            "first_name" => $request->first_name,
            "last_name" => $request->last_name,
            "id_card" => $request->id_card,
            "phone" => $request->phone,
        ];

        if($id == "0") {
            if($chk["u_data"]["dept_level"] > 1) {
                $contact["dept_id"] = $chk["u_data"]["sup_dept_id"];
            } else {
                $contact["dept_id"] = $chk["u_data"]["dept_id"];
            }

            DB::table('PkContacts')->insert($contact);
        } else {
            DB::table('PkContacts')->where("id", $id)->update($contact);
        }

        return ["status" => "T", "contact" => $contact];
    }

    public function GetInvitations(Request $request)
    {
        $chk = $this->CheckAdmin($request);
        if(!$chk["is_ok"]) {
            return ["status" => "I"];
        }

        $dept_id = $chk["u_data"]["dept_id"];

        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $invitations = DB::table("VC_invite as i")
            ->where('start_date', '<=', $end_date)
            ->where('end_date', '>=', $start_date)
            ->leftJoin('PkDepartments as d', 'd.DeptID', '=', 'i.to_dept_id')
            ->where(function ($q) use ($dept_id) {
                $q->where('d.DeptID', $dept_id);
                $q->orWhere('d.SupDepID', $dept_id);
            })
            ->select('i.*', 'd.DeptName as dept_name')->get();

        return ["status" => "T", "data_list" => $invitations];
    }

    public function CreateInvitation(Request $request)
    {
        $chk = $this->CheckAdmin($request);
        if(!$chk["is_ok"]) {
            return ["status" => "I"];
        }

        $invite = [
            "to_dept_id" => $request->to_dept_id,
            "start_date" => $request->start_date,
            "end_date" => $request->end_date,
            "start_time" => $request->start_time,
            "end_time" => $request->end_time,
            "email_time" => Carbon::now()->addMinutes(5),
        ];

        $visitors = $request->visitors;
        DB::beginTransaction();
        try {
            $invite_id = DB::table('VC_invite')->insertGetId($invite);
            foreach ($visitors as $visitor) {
                DB::table('VC_invite_visitor')->insert([
                    "invite_id" => $invite_id,
                    "contact_id" => $visitor["id"],
                ]);
            }
            DB::commit();
            return ["status" => "T"];
        } catch (Exception $e) {
            DB::rollback();
            return ["status" => "F", "err_message" => $e->getMessage()];
        }

    }

    public function GetContactByInviteId(Request $request)
    {
        $chk = $this->CheckAdmin($request);
        if(!$chk["is_ok"]) {
            return ["status" => "I"];
        }

        $invite_id = $request->invite_id;

        $contacts = DB::table("VC_invite_visitor as iv")
            ->where('iv.invite_id', '=', $invite_id)
            ->leftJoin('PkContacts as c', 'c.id', '=', 'iv.contact_id')
            ->select('c.*')->get();

        return ["status" => "T", "data_list" => $contacts];
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
