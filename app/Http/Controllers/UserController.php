<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use JWTFactory;
use Mail;
use Carbon\Carbon;
use DB;
use Exception;

class UserController extends AppController
{
    public function TestJWT(Request $request)
    {
        $u_data = [
            "test" => "good"
        ];
        $u_data["iss"] = "WarpVMS";
        $u_data["exp"] = Carbon::now()->addHours(1)->timestamp;

        $factory = JWTFactory::customClaims($u_data);
        $token = JWTAuth::encode($factory->make());

        return $token;
    }
}
