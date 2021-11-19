<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class emailauth
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
        $email=$req->email;
        $emaildata = DB::table('users')->select('email_verified_at')->where('email',$email)->get();
        
        if($emaildata[0]->email_verified_at!=NULL)
        {
            return $next($req);
        }
    
        else if($emaildata[0]->email_verified_at==NULL)
        {
            return response(["message" =>"your email is not varified"]) ;
        }
    }
}
