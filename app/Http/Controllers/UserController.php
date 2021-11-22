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
use App\Services\DataBaseConnectionService;
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
        $coll=new DataBaseConnectionService();
        $table='users';
        $coll2=$coll->connection($table);
        $req->validated();
        $name=$req->name;
        $email=$req->email;
        $password=Hash::make($req->password);
        $gender=$req->gender;
        $status=0;
        $token =$token = rand(100,1000);
        $mail=$req->email;
        $insert=$coll2->insertOne([
            'name'=>$name,
            'email'=>$email,
            'password'=>$password,
            'gender'=>$gender,
            'status'=>$status,
            'token'=>$token,
            'email_verified'=>FALSE
        ]);
        $this->sendmail($mail,$token);
        return response()->json(["message"=>"plese verify your email to proceed further"]);
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
        $coll = new DataBaseConnectionService();
        $table = 'users';
        $coll2 = $coll->connection($table);
        // $data=$coll2->findOne([
        //     'email' => $email
        // ],['projection' => ['email_verified' => 1]]);

        $data=$coll2->findOne([
                'email' => $email
         ]);

        if($data->email_verified == true){
            $dpsw = $data->password;
            if (Hash::check($password, $dpsw)) {
                $key = "waqas-123";
                $payload = array(
                    "iss" => "localhost",
                    "aud" => "users",
                    "iat" => time(),
                    "nbf" => 1357000000
                );
                $token = JWT::encode($payload, $key, 'HS256');
                $coll2->updateOne(
                    [ 'email' => $email ],
                    [ '$set' => [ 'status' => 1 ,'remember_token' => $token]]
                 );
                return response()->json(['access_token'=>$token , 'message'=> 'successfuly login']);
              }
            else{
                return "your credentials are not valid";
            }
        }
        else
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
        $coll = new DataBaseConnectionService();
        $table = 'users';
        $coll2 = $coll->connection($table);
        $data=$coll2->findOne(['remember_token' => $key ]);
        if($data!=NULL)
        {
            $uid=$data->_id;
            $name=$req->name;
            $password=Hash::make($req->password);
            $gender=$req->gender;
            $coll2->updateOne(
                [ '_id' => $uid ],
                [ '$set' => [ 'name' => $name ,'password' => $password , 'gender'=> $gender]]
            );
            return response()->json(["messsage" => "user data updated successfuly"]);
        }
        else
        {
            return response()->json(["messsage" => "you are not login"]);
        }
    }

    /**
     * user logout function
     */

    public function logOut(Request $req)
    {
        $key=$req->token;
        $coll = new DataBaseConnectionService();
        $table = 'users';
        $coll2 = $coll->connection($table);
        $data=$coll2->findOne(['remember_token' => $key ]);
        if($data!=NULL)
        {
            $coll2->updateOne(
                [ 'remember_token' => $key ],
                [ '$set' => [ 'status' => 0 ,'remember_token' => NULL]]
            );
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
        $coll = new DataBaseConnectionService();
        $table = 'users';
        $coll2 = $coll->connection($table);
        $data=$coll2->findOne(['remember_token' => $key ]);
        $uid=$data->_id;
        $userdata=$coll2->findOne([
            '_id' => $uid
        ],['projection' => ['name' => 1 ,'email' => 1 ,'gender' => 1 ,]]);
        return response(['message'=>$userdata]);
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
