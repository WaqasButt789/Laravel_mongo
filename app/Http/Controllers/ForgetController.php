<?php

namespace App\Http\Controllers;

use App\Models\user;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Mail\TestMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use App\Services\DataBaseConnectionService;

class ForgetController extends Controller
{
    public function forgetPassword(ForgetPasswordRequest $req)
    {
        $email=$req->email;
        $coll = new DataBaseConnectionService();
        $check2=(array)$coll->connection('users')
                ->findOne(['email' => $email]);

        if($check2!=NULL)
        {
            $key = rand(100,1000);
            $details=[
                'title' => 'Please Use This OTP : '. $key,
                'body' => ' '
            ];
            $coll->connection('users')->updateOne(
                [ 'email'=> $email ],
                [ '$set' => ['token'=>$key]]
            );
            Mail::to($email)->send(new TestMail($details));
            return response(["message"=>"We have sent an OTP to your registered email Please verify yourself"]);
        }
        else
        {
            return response(['message' => 'Provided Email is Not Valid']);
        }
    }

    public function changePassword(ChangePasswordRequest $req)
    {
        $email=$req->email;
        $newpassword=$req->newpassword;
        $token=(int)$req->token;
        $coll = new DataBaseConnectionService();
        $check2=(array)$coll->connection('users')
                ->findOne(['email' => $email]);

        if($check2!=NULL)
        {
            $check3=(array)$coll->connection('users')
                ->findOne(['email' => $email , 'token' => $token]);
          
            if($check3!=NULL)
            {
                $pass=Hash::make($newpassword);
                $coll->connection('users')->updateOne(
                    [ 'email'=> $email ],
                    [ '$set' => ['password' => $pass]]
                );
                return response(['message' => 'Password changed successfuly']);
            }
            else
            {
                return response(['message' => 'Please Provide a Valid OTP']);
            }
        }
        else
        {
            return response(['message' => 'Provided Email is Not Valid']);
        }
    }
}
