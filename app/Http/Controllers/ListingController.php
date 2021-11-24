<?php

namespace App\Http\Controllers;
use App\Models\post;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\ListingPostsRequest;
use App\Services\DataBaseConnectionService;


class ListingController extends Controller
{
    public function listPosts(ListingPostsRequest $req)
    {
        $key=$req->token;
        $coll = new DataBaseConnectionService();
        $data=$coll->connection('users')->findOne(['remember_token' => $key ]);
        $uid=$data->_id;
        // $friends=DB::table('friends')->select('userid_2')
        //             ->where('userid_1',$uid)->get();
        // $friends=$coll->connection('users')->findOne(['friends.fid' => $key ]);
        // $friend=$friends[0]->userid_2;

        $friends=$coll->connection('posts')->find();
        $posts=$friends->toArray();
        // $allposts = DB::table('posts')
        //             ->whereIn('user_id', [$friend] )
        //             ->orWhere('user_id',$uid)
        //             ->orWhere('accessors','public')
        //             ->get();
        dd($posts);
        //return response()->json($allposts);
    }
}
