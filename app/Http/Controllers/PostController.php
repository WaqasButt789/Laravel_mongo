<?php

namespace App\Http\Controllers;

use App\Models\post;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\DeletePostRequest;
use App\Http\Requests\ReadPostRequest;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * create post function
     */
    public function createPost(CreatePostRequest $req)
    {
        $key=$req->token;
        $data=DB::table('users')->where('remember_token',$key)->get();
        $numrows=count($data);
        if($numrows == 1)
        {
            $uid=$data[0]->uid;
            $post = new post;
            $post->user_id=$uid;
            $path = $req->file('file')->store('post');
            $post->file = $path;
            $post->accessors=$req->access;
            $post->save();
            return response()->json(['message'=>'post created successfuly']);
         }
        else{
            return response()->json(['message'=>'you are not authenticated user']);
        }
    }

    /**
     * delete post controller
     */
    public function deletePost(DeletePostRequest $req)
    {
        $key=$req->token;
        $pid=$req->pid;
        $data=DB::table('users')->where('remember_token',$key)->get();
        $numrows=count($data);
        if($numrows>0)
        {
            $uid=$data[0]->uid;
            DB::table('comments')->where('p_id',$pid)->delete();
            if(DB::table('posts')->where('pid',$pid)->delete()==1)
            {
                return response()->json(["messsage" => "Post deleted successfuly"]);
            }
            else{
                return response()->json(["messsage" => "You are not allowed to delete this post"]);
            }
        }
        else{
            return response()->json(["messsage" => "you are not login so you cannot delete post"]);
        }
    }
    /**
     * update post function
     */

    public function updatePost(Request $req)
    {

        $key=$req->token;
        $pid=$req->pid;
        $data=DB::table('users')->where('remember_token',$key)->get();
        $numrows=count($data);
        if($numrows>0)
        {
            $uid=$data[0]->uid;
            $path = $req->file('file')->store('post');
            $updateDetails = [
                'user_id' => $uid,
                'file' => $path,
                'accessors' => $req->access
            ];
            DB::table('posts')->where('pid',$pid)->update($updateDetails);
            return response()->json(["messsage" => "Post updated successfuly"]);
        }
        else{
            return response()->json(["messsage" => "you are not login"]);
        }
    }

    /**
     * read post function
     */
    public function readPost(ReadPostRequest $req)

        {
        $key=$req->token;
        $pid=$req->pid;
        $data=DB::table('users')->where('remember_token',$key)->get();
        $numrows=count($data);
        if($numrows>0)
        {
            $uid=$data[0]->uid;
            $data=DB::table('posts')->where('user_id',$uid)->get();
            return response(['message'=>$data]);
        }
        else{
            return response(['message'=>'you are not login or authenticated user']);
        }
    }
}





