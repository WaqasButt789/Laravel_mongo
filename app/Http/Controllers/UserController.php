<?php

namespace App\Http\Controllers;
use App\Models\user;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Http\Requests\SignUpRequest;
use App\Http\Requests\LogInRequest;
use App\Http\Requests\UpdateUserRequest;


class UserController extends Controller
{
     /**
     * user signup function
     */
    public function userSignup(SignUpRequest $req)
    {
        try{
            $req->validated();
            $user=new User;
            $user->name=$req->name;
            $user->email=$req->email;
            $user->password=Hash::make($req->password);
            $user->gender=$req->gender;
            $user->status=$req->status;
            $user->token =$token = rand(100,1000);
            $user->save();    
            $mail=$req->email;
            $this->sendmail($mail,$token);
            return $user;
        }catch(Exception $ex){
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }
      ////sending mail function
     
    public function sendmail($mail,$token)
    {
        $details=[
            'title' => 'Please Verify Your Email',
            'body' => 'http://127.0.0.1:8000/api/verify/'.$mail.'/'.$token
        ];
        Mail::to($mail)->send(new TestMail($details));
        return "Email Sent Succesfully";
    }

    /**
     * user login function
     */
    public function userLogin(LogInRequest $req)
    {
        $email=$req->email;
        $password=$req->password;
        $data = DB::table('users')->where('email',$email)->get();
        $emaildata = DB::table('users')->select('email_verified_at')->where('email',$email)->get();
        
        if($emaildata[0]->email_verified_at!=NULL)
        {
            $dpsw = $data[0]->password;
            $count = count($data);
    
            //checking hash password with simple 

            if (Hash::check($password, $dpsw)) {
    
                $key = "waqas-123";
                $payload = array(
                    "iss" => "localhost",
                    "aud" => "users",
                    "iat" => time(),
                    "nbf" => 1357000000
                );
                $token = JWT::encode($payload, $key, 'HS256');
                DB::table('users')->where('email',$email)->update(['status'=>true]);
                DB::table('users')->where('email',$email)->update(['remember_token'=>$token]);
                return response()->json(['access_token'=>$token , 'message'=> 'successfuly login']); 
              }
            else{
                return "your credentials are not valid";
            } 
        }
        else if($emaildata[0]->email_verified_at==NULL)
        {
            echo "your email is not vreified";
        }
    }
    /**
     * update user function
     */
    public function updateUser(UpdateUserRequest $req)
    {
        $key=$req->token;
        $pid=$req->pid;
        $data=DB::table('users')->where('remember_token',$key)->get();
        $numrows=count($data);
        if($numrows>0)
        {
            $password=Hash::make($req->password);
            $uid=$data[0]->uid;
            $updateDetails = [
                'name' => $req->name,
                'password' => $password
            ];
            DB::table('users')->where('uid',$uid)->update($updateDetails);
            return response()->json(["messsage" => "user data updated successfuly"]);
        }
        else{
            return response()->json(["messsage" => "you are not login"]);
        }
    }

    /**
     * user logout function
     */

    public function logOut(Request $req)
    {
        $key=$req->token;
        $data=DB::table('users')->where('remember_token',$key)->get();
        $numrows=count($data);
        if($numrows>0)
        {
            DB::table('users')->where('remember_token',$key)->update(['status'=>false]);
            DB::table('users')->where('remember_token',$key)->update(['remember_token'=>NULL]);
            return response()->json(['message'=>'logout successfuly']);
        }
        else{
            return response()->json(['message'=>'you are already logout']);
        }
    }
    /**
     * get user data function
     */
    public function getUserData(Request $req)
    {
        $key=$req->token;
        $data=DB::table('users')->where('remember_token',$key)->get();
        $numrows=count($data);
        if($numrows>0)
        {
            $uid=$data[0]->uid;
            $data=DB::table('users')->select('name','email','gender')->where('uid',$uid)->get();
            return response(['message'=>$data]);
        }
        else{
            return response(['message'=>'you are not login or authenticated user']);
        }
     }
     /**
      * get all posts aginst a user
      */
    public function getPostDetails(Request $req)
    {
        $key=$req->token;
        if($key!=NULL)
        {
            $data=DB::table('users')->where('remember_token', $key)->get();
            $uid=$data[0]->uid;
            //$d1=user::with('getPostDetails')->where('uid',$uid)->get();
            $users = User::with(['getPostDetails', 'getPostComments'])->where('uid',$uid)->get();
            return response(["message" => $users]);
        }
        else
        {
            return response(["message"=>"Please provide a token"]);
        }
    }
}
