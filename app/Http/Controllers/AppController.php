<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use JWTFactory;
use Mail;
use Carbon\Carbon;
use DB;
use Exception;

class AppController extends Controller
{
    protected $TokenName = "VMSToken";

    public function CreateVMSCookie($u_data)
    {
        $u_data["iss"] = "WarpVMS";
        $u_data["exp"] = Carbon::now()->addHours(1)->timestamp;

        $factory = JWTFactory::customClaims($u_data);
        $token = JWTAuth::encode($factory->make());
        return Cookie($this->TokenName, $token);
    }

    public function CheckAdmin($request)
    {
        try {
            //Set token and check
            JWTAuth::setToken($request->cookie($this->TokenName));
            $payload = JWTAuth::getPayload();

            $u_data = [
                "admin_id" => $payload["admin_id"],
                "admin_level_id" => $payload["admin_level_id"],
                "admin_name" => $payload["admin_name"],
                "dept_id" => $payload["dept_id"],
                "dept_level" => $payload["dept_level"],
                "sup_dept_id" => $payload["sup_dept_id"],
                "dept_name" => $payload["dept_name"],
                "pw_change" => $payload["pw_change"]
            ];

            return ["is_ok" => true, "u_data" => $u_data];

        } catch (Exception $e) {
            return ["is_ok" => false, "error" => $e->getMessage()];
        }
    }

    public function Response($res, $chk)
    {
        return response($res)->withCookie($this->CreateVMSCookie($chk["u_data"]));
    }

}
