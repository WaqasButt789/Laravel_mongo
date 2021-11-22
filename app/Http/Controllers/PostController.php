<?php

namespace App\Http\Controllers;
use App\Models\post;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\DeletePostRequest;
use App\Http\Requests\ReadPostRequest;
use Illuminate\Http\Request;
use App\Services\DataBaseConnectionService;
use App\Helpers\helper;
use function App\Helpers\get_mongo_connection;

//use MongoDB\BSON\ObjectId;

class PostController extends Controller
{
    /**
     * create post function
     */
    public function createPost(CreatePostRequest $req)
    {
        $key=$req->token;
        $coll = new DataBaseConnectionService();
        $utable = 'users';
        $coll2 = $coll->connection($utable);
        $data=$coll2->findOne(['remember_token' => $key ]);
        $uid=$data->_id;
        $user_id=$uid;
        if($req->file('file')!=NULL)
        {
            $path = $req->file('file')->store('post');
            $file = $path;
        }
        else{
            return response()->json(['message'=>'Please Provide a file to create post successfuly']);
        }
        $accessors=$req->access;
        $ptable = 'posts';
        $coll2 = $coll->connection($ptable);
        $insert=$coll2->insertOne([
            'user_id'=>$user_id,
            'file'=>$file,
            'accessors'=>$accessors
        ]);
        return response()->json(['message'=>'post created successfuly']);
    }

    /**
     * delete post controller
     */
    public function deletePost(DeletePostRequest $req)
    {
        $key=$req->token;
        $pid=$req->pid;
        $coll = new DataBaseConnectionService();
        $utable ='users';
        $coll2 = $coll->connection($utable);
        // $table='users';
        // $conn=get_mongo_connection($table);
        //dd($conn);
        $data=$coll2->findOne(['remember_token' => $key ]);
        $uid=$data->_id;
        $ptable = 'posts';
        $check_user_against_post = $coll->connection($ptable);
        $id = new \MongoDB\BSON\ObjectId($pid);
        $data=$check_user_against_post->findOne(['_id' => $id],['projection' => ['uid'=> 1]]);
        $uid_from_database = $data->user_id;
        if($uid == $uid_from_database ){
            $ptable = 'posts';
            $collection = $coll->connection($ptable);
            $collection->deleteOne(['_id' => $id]);
            return response()->json(["messsage" => "Post deleted successfuly"]);
        }
        else{
            return response()->json(["messsage" => "You are not allowed to delete this post"]);
        }
    }
    /**
     * update post function
     */

    public function updatePost(Request $req)
    {
        $key=$req->token;
        $pid=$req->pid;
        $coll = new DataBaseConnectionService();
        $utable ='users';
        $coll2 = $coll->connection($utable);
        // $table='users';
        // $conn=get_mongo_connection($table);
        //dd($conn);
        $data=$coll2->findOne(['remember_token' => $key ]);
        $uid=$data->_id;
        $user_id=$uid;
        $ptable = 'posts';
        $check_user_against_post = $coll->connection($ptable);
        $id = new MongoDB\BSON\ObjectId($pid);
        $data=$check_user_against_post->findOne(['_id' => $id],['projection' => ['user_id'=> 1]]);
        $uid_from_database = $data->user_id;
        if($uid == $uid_from_database ){
            $ptable = 'posts';
            $collection = $coll->connection($ptable);
            $collection->updateOne(
                [ '_id' => $id ],
                [ '$set' => [ 'file' => $req->file ,'accessors' => $req->access]]
            );
            return response()->json(["messsage" => "Post updated successfuly"]);
        }
        else{
            return response()->json(["messsage" => "You are not allowed to update this post"]);
        }
    }

    /**
     * read post function
     */
    public function readPost(ReadPostRequest $req)
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
        $utable ='posts';
        $coll2 = $coll->connection($utable);
        $data = $coll2->find(['user_id' => $uid]);
        $objects = json_decode(json_encode($data->toArray(),true));
        $array=json_decode(json_encode($objects),true);
        return response([$array]);
    }
}





