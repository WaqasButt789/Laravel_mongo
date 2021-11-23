<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\DataBaseConnectionService;

class UserCommentController extends Controller
{
    /**
     * creating a comment against a post
     */
    public function createComment(Request $req)
    {
        // $table='users';
        // $conn=get_mongo_connection($table);
        //dd($conn);
        $key=$req->token;
        $pid=new \MongoDB\BSON\ObjectId($req->pid);
        $coll = new DataBaseConnectionService();
        $uid=$this->return_uid($key);
        // $utable ='posts';
        // $coll3 = $coll->connection($utable);
        // $id = new \MongoDB\BSON\ObjectId($pid);
        // $data=$coll3->findOne(['_id' => $id ]);
        if($req->comment !=NULL)
        {
            $comment=$req->comment;
        }
        else{
            $comment=NULL;
        }
        if( $req->file('file')!=NULL)
        {
            $path = $req->file('file')->store('post');
        }
        else{
            $path=NULL;
        }
        $comment = array(
            "_id" => new \MongoDB\BSON\ObjectId(),
            "uid" => $data["_id"],
            "file" => $path,
            "comment" => $comment,
        );
        $coll->connection('posts')->updateOne(["_id" => $pid],['$push'=>["comments" => $comment]]);
        return response()->json(['message'=>'commented successfuly']);
    }

    /**
     * updating an existing comment
     */
    public function updateComment(Request $req)
    {
        $key=$req->token;
        $cid=new \MongoDB\BSON\ObjectId($req->cid);
        $pid=new \MongoDB\BSON\ObjectId($req->pid);
        $coll = new DataBaseConnectionService();
        $uid=$this->return_uid($key);
        if($req->comment !=NULL)
        {
            $comment=$req->comment;
        }
        else{
            $comment=NULL;
        }
        if( $req->file('file')!=NULL)
        {
            $path = $req->file('file')->store('post');
            $file=$path;
        }
        else{
            $file=NULL;
        }
        $ptable = 'posts';
        $collection = $coll->connection($ptable);
        $data=(array)$coll->connection('posts')->findOne(['_id' => $pid,'comments'=>['$elemMatch'=>['uid'=>$uid]]]);
        if($data!=NULL){
            $collection->updateOne(['_id' => $pid,'comments._id'=>$cid],
            ['$set' => ['comments.$.comment'=>$comment ,'comments.$.file'=>$file]]);
            return response()->json(["messsage" => "Comment updated successfuly"]);
        }
        else{
            return response()->json(["messsage" => "You are not allowed to update this comment"]);
        }
    }



     /**
     * deleting an existing comment
     */
    public function deleteComment(Request $req)
    {
        $coll = new DataBaseConnectionService();
        $key=$req->token;
        $pid=new \MongoDB\BSON\ObjectId($req->pid);
        $cid=new \MongoDB\BSON\ObjectId($req->cid);
        $uid=$this->return_uid($key);
        $data=(array)$coll->connection('posts')->findOne(['_id' => $pid,'comments'=>['$elemMatch'=>['uid'=>$uid]]]);
        if($data!=NULL){
            $coll->connection('posts')->updateOne(['_id' => $pid, 'comments._id'=>$cid],
             ['$pull'=>['comments'=>['_id'=>$cid]]]);
            return response()->json(["messsage" => "Comment Deleted successfuly"]);
        }
        else{
            return response()->json(["messsage" => "You are not allowed to delete this comment"]);
            }
    }

    public function return_uid($key)
    {
        $coll = new DataBaseConnectionService();
        $data=$coll->connection('users')->findOne(['remember_token' => $key ]);
        $uid=$data->_id;
        return $uid;

    }
}
