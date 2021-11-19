<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;

class updatecontroller extends Controller
{
    /**
     * 
     * updating Email_verified_at field 
     */
    public function updateData($email,$token)
    {
       //$em=Crypt::decryptString($email);

       //echo $em;
      

        $data = DB::table('users')->where('email',$email)->where('token',$token)->get();
        $count=count($data);
        if($count>0)
        {
            DB::table('users')->where('token',$token)->update(['email_verified_at'=>now()]);
            return "Your Email is Verified";
        }
        else{
            return "Your Email is not Verified";
        }

    }
}
