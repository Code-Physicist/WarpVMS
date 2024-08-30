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
        ->select('id', 'email', 'first_name', 'last_name')
        ->first();

        return ["status" => "T", "contact" => $contact];
    }

    //For visitor page
    public function GetVisitorContactByEmail(Request $request)
    {
        $v_dept_id = $request->v_dept_id;
        $email = $request->email;

        $contact = DB::table('PkContacts')
        ->where('email', '=', $email)
        ->where('dept_id', '=', $v_dept_id)
        ->select('id', 'email', 'first_name', 'last_name', 'id_card', 'phone')
        ->first();

        return ["contact" => $contact];
    }

    public function AddVisitor(Request $request)
    {
        $invite_id = $request->invite_id;
        $v_dept_id = $request->v_dept_id;
        $contact = $request->contact;

        $visitor = [
            "email" => $contact["email"],
            "first_name" => $contact["first_name"],
            "last_name" => $contact["last_name"],
            "id_card" => $contact["id_card"],
            "phone" => $contact["phone"],
            "dept_id" => $v_dept_id,
        ];

        $id = $contact["id"];

        DB::beginTransaction();
        try {
            if($id == "0") {
                $id = DB::table('PkContacts')->insertGetId($visitor);
            } else {
                DB::table('PkContacts')->where("id", $contact["id"])->update($visitor);
            }

            DB::table("VC_invite_visitor")
            ->where('invite_id', '=', $invite_id)
            ->insert([
                "invite_id" => $invite_id,
                "contact_id" => $id,
                "pdpa_accept" => 1
            ]);

            DB::commit();
            return ["status" => "T"];

        } catch (Exception $e) {
            DB::rollback();
            return ["status" => "F", "err_message" => $e->getMessage()];
        }
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

            $id = DB::table('PkContacts')->insertGetId($contact);
        } else {
            DB::table('PkContacts')->where("id", $id)->update($contact);
        }

        return ["status" => "T", "contact_id" => $id];
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

    public function GetInvitationById(Request $request)
    {
        $id = $request->id;
        $invitation = DB::table("VC_invite")
            ->where('id', '=', $id)
            ->first();

        $visitors = DB::table("VC_invite_visitor as iv")
            ->where('iv.invite_id', '=', $id)
            ->leftJoin('PkContacts as c', 'c.id', '=', 'iv.contact_id')
            ->select('iv.pdpa_accept', 'c.id', 'c.first_name', 'c.last_name', 'c.email', 'c.id_card', 'c.phone')->get();

        $dept = DB::table("PkDepartments")
            ->where('DeptID', '=', $invitation->to_dept_id)
            ->first();

        $v_dept_id = $dept->DeptID;
        if($dept->Level1 == 2) {
            $v_dept_id = $dept->SupDepID;
        }

        return ["status" => "T", "invitation" => $invitation, "visitors" => $visitors, "dept" => $dept, "v_dept_id" => $v_dept_id];
    }

    public function EditVisitor(Request $request)
    {
        try {
            JWTAuth::setToken($request->cookie("WarpVisitor"));
            $payload = JWTAuth::getPayload();
            $code = $payload["code"];

            $id = substr($code, 0, strlen($code) - 32);
            $c = substr($code, strlen($id), strlen($code));

            $invitation = DB::table("VC_invite")->where("id", $id)->first();
            if(is_null($invitation)) {
                return ["status" => "F", "err_message" => "Invalid Operation"];
            }

            $update_time = Carbon::parse($invitation->update_time);
            $date_code = md5($update_time->format('YmdHis'));
            if($c !== $date_code) {
                return ["status" => "F", "err_message" => "Invalid Operation"];
            }

        } catch (Exception $e) {
            return ["status" => "F", "err_message" => "Token Expired"];
        }

        $invite_id = $request->invite_id;
        $contact = $request->contact;

        $invite_visitor = DB::table("VC_invite_visitor")
            ->where('invite_id', '=', $invite_id)
            ->where('contact_id', '=', $contact["id"])
            ->first();

        $send_email = false;
        if($invite_visitor->pdpa_accept == 0) {
            $send_email = true;
        }

        DB::beginTransaction();
        try {
            DB::table('PkContacts')
            ->where("id", $contact["id"])
            ->update([
                "first_name" => $contact["first_name"],
                "last_name" => $contact["last_name"],
                "phone" => $contact["phone"],
                "id_card" => $contact["id_card"],
            ]);

            DB::table('VC_invite_visitor')
            ->where('invite_id', '=', $invite_id)
            ->where('contact_id', '=', $contact["id"])
            ->update([
                "pdpa_accept" => 1
            ]);

            DB::commit();
            return ["status" => "T", "send_email" => $send_email];

        } catch (Exception $e) {
            DB::rollback();
            return ["status" => "F", "err_message" => $e->getMessage()];
        }
    }

    public function CreateInvitation(Request $request)
    {
        $chk = $this->CheckAdmin($request);
        if(!$chk["is_ok"]) {
            return ["status" => "I"];
        }

        $invite = [
            "to_dept_id" => $request->to_dept_id,
            "title" => $request->title,
            "agenda" => $request->agenda,
            "start_date" => $request->start_date,
            "end_date" => $request->end_date,
            "start_time" => $request->start_time,
            "end_time" => $request->end_time,
            "update_time" => Carbon::now(),
        ];

        $visitors = $request->visitors;
        DB::beginTransaction();
        try {
            $invite_id = DB::table('VC_invite')->insertGetId($invite);
            foreach ($visitors as $visitor) {
                DB::table('VC_invite_visitor')->insert([
                    "invite_id" => $invite_id,
                    "contact_id" => $visitor["id"],
                    "pdpa_accept" => 0
                ]);
            }
            DB::commit();
            return ["status" => "T", "title" => $invite["title"], "agenda" => $invite["agenda"], "invite_id" => $invite_id, "visitors" => $visitors];
        } catch (Exception $e) {
            DB::rollback();
            return ["status" => "F", "err_message" => $e->getMessage()];
        }

    }

    public function EditInvitation(Request $request)
    {
        $chk = $this->CheckAdmin($request);
        if(!$chk["is_ok"]) {
            return ["status" => "I"];
        }

        $id = $request->id;
        $invite = [
            "to_dept_id" => $request->to_dept_id,
            "title" => $request->title,
            "agenda" => $request->agenda,
            "start_date" => $request->start_date,
            "end_date" => $request->end_date,
            "start_time" => $request->start_time,
            "end_time" => $request->end_time,
            "update_time" => Carbon::now(),
        ];

        $visitors = $request->visitors;

        DB::beginTransaction();
        try {
            DB::table('VC_invite')->where("id", $id)->update($invite);

            DB::table('VC_invite_visitor')->where("invite_id", $id)->delete();
            foreach ($visitors as $visitor) {
                DB::table('VC_invite_visitor')->insert([
                    "invite_id" => $id,
                    "contact_id" => $visitor["id"],
                    "pdpa_accept" => 0
                ]);
            }

            DB::commit();
            return ["status" => "T", "title" => $invite["title"], "agenda" => $invite["agenda"], "invite_id" => $id, "visitors" => $visitors];
        } catch (Exception $e) {
            DB::rollback();
            return ["status" => "F", "err_message" => $e->getMessage()];
        }

    }

    public function GetContactsByInviteId(Request $request)
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

    public function SendQRCode(Request $request)
    {
        $email = $request->email;
        $invite_id = $request->invite_id;

        $code = "VMS" . str_pad(strval($invite_id), 8, "0", STR_PAD_LEFT);
        $qr_image = QrCode::format("png")->size(512)->generate($code);
        $data = array("qr_image" => $qr_image);

        $emails = [$email];

        Mail::send("emails.invite", $data, function ($message) use ($emails) {
            $message->to($emails)
                    ->subject("QR Code Access");
            $message->from("VMS_Admin@gmail.com", "VMS Admin");
        });
        return ["status" => "F"];
    }

    public function SendInviteEmail(Request $request)
    {
        $invite_id = $request->invite_id;
        $base_url = $request->base_url;

        $invitation = DB::table("VC_invite")->where("id", $invite_id)->first();
        $dept = DB::table("PkDepartments")
            ->where("DeptID", $invitation->to_dept_id)
            ->select('Fullname as full_name')
            ->first();

        $visitors = DB::table("VC_invite_visitor as iv")
            ->where('iv.invite_id', '=', $invite_id)
            ->leftJoin('PkContacts as c', 'c.id', '=', 'iv.contact_id')
            ->select('c.email')->get();

        $update_time = Carbon::parse($invitation->update_time);
        $date_code = md5($update_time->format('YmdHis'));

        $data = [
            "iss" => "WarpVMS",
            "code" => "$invite_id$date_code",
            "exp" => Carbon::now()->addDays(90)->timestamp
        ];

        $factory = JWTFactory::customClaims($data);
        $token = JWTAuth::encode($factory->make());

        $url = "$base_url/visitors/invitation/$token";

        $data = array(
            "dept_name" => $dept->full_name,
            "title" => $invitation->title,
            "agenda" => $invitation->agenda,
            "url" => $url
        );

        $emails = [];
        foreach ($visitors as $visitor) {
            array_push($emails, $visitor->email);
        }

        Mail::send("emails.visitor", $data, function ($message) use ($emails) {
            $message->to($emails)
                    ->subject("VMS Invitation");
            $message->from("VMS_Admin@gmail.com", "VMS Admin");
        });
        return ["status" => "T"];
    }

    public function VisitorInvitation(Request $request)
    {
        try {
            JWTAuth::setToken($request->code);
            $payload = JWTAuth::getPayload();
            $code = $payload["code"];

            $id = substr($code, 0, strlen($code) - 32);
            $c = substr($code, strlen($id), strlen($code));

            $invitation = DB::table("VC_invite")->where("id", $id)->first();
            if(is_null($invitation)) {
                return "ID is null";
            }
        } catch (Exception $e) {
            return "Token expired";
        }

        $update_time = Carbon::parse($invitation->update_time);
        $date_code = md5($update_time->format('YmdHis'));
        if($c !== $date_code) {
            return "Invalid page";
        }

        return response(view("visitor", ["id" => $id]))->withCookie(Cookie("WarpVisitor", $request->code));
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
