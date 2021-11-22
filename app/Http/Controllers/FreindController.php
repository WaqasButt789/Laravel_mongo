<?php

namespace App\Http\Controllers;
use Illuminate\Http\Requests;
use App\Http\Requests\AddFriendRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Friend;
use Illuminate\Http\Request;
use App\Services\DataBaseConnectionService;


class FreindController extends Controller
{
    public function addFriend(AddFriendRequest $req)
    {
        $key=$req->token;
        $coll = new DataBaseConnectionService();
        $utable ='users';
        $coll2 = $coll->connection($utable);
        $data=$coll2->findOne(['remember_token' => $key ]);
        $uid=$data->_id;
        $id=$req->fid;
        $fid = new \MongoDB\BSON\ObjectId($id);
        if($uid != $fid)
        {
            $check1=$coll2->findOne(['_id' =>$fid]);
            if($check1!=NULL)
            {
                $coll2 = $coll->connection("friends");
                 $check2=$coll2->findOne(['$or' =>[['$and'=>[['user_id' => $uid],['Friend_id' => $fid]]]
                ,['$and'=>[['user_id' => $fid],['Friend_id' => $uid]]]]]);
                if($check2 == NULL)
                {
                  $userid=$uid;
                  $friendid=$fid;
                  $status="pending";
                  $coll2->insertOne([
                    'user_id'=>$userid,
                    'Friend_id'=>$friendid]);
                  return response()->json(["messsage" => "now you are friend of".$fid]);
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
        $id=$req->fid;
        $coll = new DataBaseConnectionService();
        $utable ='users';
        $coll2 = $coll->connection($utable);
        $data=$coll2->findOne(['remember_token' => $key ]);
        $uid=$data->_id;
        $fid = new \MongoDB\BSON\ObjectId($id);
        $check2=$coll->connection("friends")->findOne(['$or' =>[['$and'=>[['user_id' => $uid],['Friend_id' => $fid]]]
       ,['$and'=>[['user_id' => $fid],['Friend_id' => $uid]]]]]);
        if($check2 != NULL)
        {
            $data=$coll->connection("friends")->deleteOne(['$or' =>[['$and'=>[['user_id' => $uid],['Friend_id' => $fid]]]
            ,['$and'=>[['user_id' => $fid],['Friend_id' => $uid]]]]]);
            return response()->json(["messsage" => "Unfriend Successfuly"]);
        }
        else{

            return response(['Message' => 'You are not the friend of '.$fid]);
        }
    }
}
