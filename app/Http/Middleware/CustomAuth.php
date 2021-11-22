<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\DataBaseConnectionService;



class CustomAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $req, Closure $next)
    {
        $coll=new DataBaseConnectionService();    
        $table='users';   
        $coll2=$coll->connection($table); 
        $key=$req->token;
        $data=$coll2->findOne(['remember_token' => $key ]);
        if($data!=NULL)
        {
            return $next($req);
        }
        else{
            return response(["Message" => "you are not logIn"]);
        }
    }
}
