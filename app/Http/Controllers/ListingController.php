<?php

namespace App\Http\Controllers;
use App\Models\post;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\ListingPostsRequest;


class ListingController extends Controller
{
    public function listPosts(ListingPostsRequest $req)
    {
        $key=$req->token;
        $data=DB::table('users')
                    ->where('remember_token',$key)->get();
        $uid=$data[0]->uid;
        $friends=DB::table('friends')->select('userid_2')
                    ->where('userid_1',$uid)->get();
        $friend=$friends[0]->userid_2;
        $allposts = DB::table('posts')
                    ->whereIn('user_id', [$friend] )
                    ->orWhere('user_id',$uid)
                    ->orWhere('accessors','public')
                    ->get();
        return response()->json($allposts);
    }
}