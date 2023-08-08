<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Merchant extends Authenticatable
{
    use HasFactory;
    protected $guard = 'merchants';
    // protected $table = 'merchants';
    // protected $fillable = ['name', 'email', 'password'];
}


?>