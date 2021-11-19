<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\post;
use App\Models\User;
use App\Models\comment;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; 

    /**
     * The attributes that are mass assignable.
     *
     * @var string[] 
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'gender',
        'status',
        'remember_token'

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** 
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



     public function getPostDetails()
     {
         return $this->hasMany(post::class,'user_id','uid');
    }

    public function getPostComments()
    {

        return $this->hasManyThrough(comment::class,post::class,'user_id','p_id','uid','pid');
    }

   
}
  