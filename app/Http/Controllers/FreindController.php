<?php

namespace App\Http\Controllers;
use Illuminate\Http\Requests;
use App\Http\Requests\AddFriendRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Friend;
use Illuminate\Http\Request;


class FreindController extends Controller
{
    public function addFriend(AddFriendRequest $req)
    {
        $key=$req->token;
        $fid=$req->fid;
        $data=DB::table('users')
                    ->where('remember_token',$key)->get();
        $uid=$data[0]->uid;
        if($uid != $fid)
        { 
            if(DB::table('users')->where('uid',$fid)->exists())
            {
                if(DB::table('friends')
                        ->where(['userid_1' => $uid , 'userid_2' => $fid])
                        ->orwhere(['userid_1' => $fid , 'userid_2' => $uid])
                        ->doesntExist())
                {
                    $numrows=count($data);
                    if($numrows>0)
                    {
                        $friend=new Friend;
                        $friend->userid_1=$uid;
                        $friend->userid_2=$fid;
                        $friend->save();
                        return response()->json(["messsage" => "now you are friend of".$fid]);
                    }
                    else{
                            return response()->json(["messsage" => "you are not login"]);
                        }
                 }
                 else{
                        return response(["message" => "User with id = ".$fid." is already your friend"]);
                     }
            }
            else{
                    return response(["message" => "User with id = ".$fid." is not registerd on our application"]);
                }
        }
        else{
                return response(["message" => "you cannot be the friend of yourself"]);
             }
    }

    public function removeFriend(Request $req)
    {
        $key=$req->token;
        $fid=$req->fid;
        $data=DB::table('users')->where('remember_token',$key)->get();
        $uid=$data[0]->uid;
        if(DB::table('friends')->where(['userid_1' => $uid , 'userid_2' => $fid])->delete() == 1)
        {
            return response(['Message' => 'Unfriend Successfuly']);
        }
        else{

            return response(['Message' => 'You are not the friend of '.$fid]);
        }
    }
}
