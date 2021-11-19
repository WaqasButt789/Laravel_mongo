<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\controllers\PostController;
use App\Http\Controllers\UserCommentController;
use App\Http\Controllers\updatecontroller;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FreindController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ForgetController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * group middleware 
 */
Route::group(['middleware'=>'cauth'],function(){


/**
 *  create a new post
 */

Route::post('/createpost',[PostController::class,'createPost']);
/**
 * update post Route
 */
Route::post('/updatepost',[PostController::class,'updatePost']);
/**
 * delete post Route
 */
Route::post('/deletepost',[PostController::class,'deletePost']);
/**
 * get all posts
 */
 Route::post('/getposts',[PostController::class,'readPost']);
/**
 * get all user information
 */
Route::post('/getuser',[UserController::class,'getUserData']);

/**
 * update user
 */
Route::post('/updateuser',[UserController::class,'updateUser']);




/**
 * create comment
 */
Route::post('/comment',[UserCommentController::class,'createComment']);

/**
 * update comment
 */

Route::post('/updatecomment',[UserCommentController::class,'updateComment']);

/**
 * delete post
 */
Route::post('/deletecomment',[UserCommentController::class,'deleteComment']);
// Route::post('/comment',[user_comment_controller::class,'create_comment']);




/**
 * logout route
 */

Route::post('/logout',[UserController::class,'logOut']);


Route::post('/getpost',[UserController::class,'getPostDetails']);

/**
 * add friend route
 */
Route::post('/addfriend',[FreindController::class,'addFriend']);
/**
 * unfriend route
 */

Route::post('/unfriend',[FreindController::class,'removeFriend']);

/**
 * Listing All posts Route
 */
Route::post('/showallposts', [ListingController::class, 'listPosts']);

 });

 /**
 * signup route
 */
Route::post('/signup',[UserController::class,'userSignup']);

/**
 * login route
 */

Route::post('/login',[UserController::class,'userLogin'])->middleware('eauth');


/**
 * update verified email
 */

Route::get('/verify/{email}/{token}',[UpdateController::class,'updateData']);

/**
 * forget password route
 */
Route::post('/forgetpassword',[ForgetController::class,'forgetPassword']);
/**
 * Change Password route
 */
Route::post('/changepassword',[ForgetController::class,'changePassword']);