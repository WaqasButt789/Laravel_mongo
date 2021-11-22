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
        $pid=$req->pid;
        $coll = new DataBaseConnectionService();
        $utable ='users';
        $coll2 = $coll->connection($utable);
        $data=$coll2->findOne(['remember_token' => $key ]);

        $uid=$data->_id;
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
            $file=$path;
        }
        else{
            $file=NULL;
        }
        $ptable = 'comments';
        $coll2 = $coll->connection($ptable);
        $insert=$coll2->insertOne([
            'uid'=>$uid,
            'pid'=>$pid,
            'file'=>$file,
            'comment'=>$comment
        ]);
        return response()->json(['message'=>'commented successfuly']);
    }

    /**
     * updating an existing comment
     */
    public function updateComment(Request $req)
    {
        $key=$req->token;
        $cid=$req->cid;
        $coll = new DataBaseConnectionService();
        $utable ='users';
        $coll2 = $coll->connection($utable);
        $data=$coll2->findOne(['remember_token' => $key ]);
        $uid=$data->_id;
        $utable ='comments';
        $coll2 = $coll->connection($utable);
        $ccid = new \MongoDB\BSON\ObjectId($cid);
        $data=$coll2->findOne(['_id' => $ccid ]);
        $uid_from_comments=$data->uid;
        if($uid_from_comments == $uid )
        {
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
            $ptable = 'comments';
            $collection = $coll->connection($ptable);
            $collection->updateOne(
                [ '_id' => $ccid ],
                [ '$set' => ['user_id' => $uid,'file' => $file,'comment'=> $comment]]
            );
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
        $key=$req->token;
        $cid=$req->cid;
        $coll = new DataBaseConnectionService();
        $utable ='users';
        $coll2 = $coll->connection($utable);
        $data=$coll2->findOne(['remember_token' => $key ]);
        $uid=$data->_id;
        $utable ='comments';
        $coll2 = $coll->connection($utable);
        $ccid = new \MongoDB\BSON\ObjectId($cid);
        $data=$coll2->findOne(['_id' => $ccid ]);
        $uid_from_comments=$data->uid;
        if($uid_from_comments == $uid )
        {
            $coll2->deleteOne(['_id' => $ccid]);
            return response()->json(["messsage" => "Comment Deleted successfuly"]);
        }
        else{
            return response()->json(["messsage" => "You are not allowed to delete this comment"]);
        }
    }
}
