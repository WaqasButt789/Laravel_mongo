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

class ForgetController extends Controller
{
    public function forgetPassword(ForgetPasswordRequest $req)
    {
        $email=$req->email;
        if(DB::table('users')->where('email',$email)->exists())
        {
            $key = rand(100,1000);
            $details=[
                'title' => 'Please Use This OTP : '. $key,
                'body' => ' '   
            ];
            DB::table('users')->where('email',$email)->update(['token'=>$key]);
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
        $token=$req->token;
        if(DB::table('users')->where('email',$email)->exists())
        {
            if(DB::table('users')->where(['email'=> $email , 'token' => $token])->exists())
            {
                $pass=Hash::make($newpassword);
                DB::table('users')->where('email',$email)->update(['password'=>$pass]);
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
