<?php

namespace App\Models;
  
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;


class post extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    
        /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    
    protected $fillable = [
        'user_id',
        'file',
        'accessors'

    ];

     /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at'
    ];

    

}