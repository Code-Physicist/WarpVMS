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
        if(!$check["is_ok"]) {
            return response()->view("login");
        }
        if($check["u_data"]["pw_change"]) {
            return redirect("/admin/pass_change");
        }

        //Refresh VMS cookie
        $cookie = $this->CreateVMSCookie($check["u_data"]);
        return response()->view("dashboard", $check["u_data"])->withCookie($cookie);
    }



}
