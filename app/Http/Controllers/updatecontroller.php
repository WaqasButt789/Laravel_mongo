<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Services\DataBaseConnectionService;

use Illuminate\Http\Request;

class updatecontroller extends Controller
{
    /**
     *
     * updating Email_verified_at field
     */
    public function updateData($email,$token)
    {
            $coll = new DataBaseConnectionService();
            $table = 'users';
            $coll2 = $coll->connection($table);
            $insert = $coll2->findOne([
                'email' => $email,
                'token' => (int)$token
             ]);
             //$objects = json_decode(json_encode($insert->toArray(),true));
             dd($insert);
            if($insert)
                {
                     $update = $coll2->updateMany(array("email"=>$email),
                    array('$set'=>array('email_verified' => true,
                    'updated_at' =>new \MongoDB\BSON\UTCDateTime(new \DateTime('now')))));
                    return response(['Message'=>'Your Email has been Verified']);
                }
                else
                {
                    return response(['Message' => 'your email is not verified..!!!']);
                }
    }

}

