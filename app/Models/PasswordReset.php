<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class PasswordReset extends Model
{
    use HasApiTokens, Notifiable;
    
    protected $fillable = ['email', 'token'];
}
