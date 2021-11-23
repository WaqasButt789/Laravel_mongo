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
        $data=$coll->connection('users')->findOne(['remember_token' => $key ]);
        $uid=$data->_id;  // user id from users table
        $fid = new \MongoDB\BSON\ObjectId($req->fid); // friend_id from post man parameters
        if($uid != $fid)
        {
            $check1=$coll->connection('users')->findOne(['_id' =>$fid]);
            if($check1!=NULL)   // checking whether user is registered on our application or not
            {
                $check2=(array)$coll->connection('users')->findOne(['_id' => $uid,'friends'=>['$elemMatch'=>['fid'=>$fid]]]);
                if($check2 == NULL)
                {
                    $friend = array(
                        "_id" => new \MongoDB\BSON\ObjectId(),
                        "fid" => $fid
                    );
                    $coll->connection('users')->updateOne(["_id" => $uid],['$push'=>["friends" => $friend]]);
                    $friend = array(
                        "_id" => new \MongoDB\BSON\ObjectId(),
                        "fid" => $uid
                    );
                    $coll->connection('users')->updateOne(["_id" => $fid],['$push'=>["friends" => $friend]]);
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

    // public function check_friends_or_not()
    // {

    // }

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
