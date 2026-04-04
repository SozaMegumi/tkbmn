<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Guardian extends Authenticatable
{
    use Notifiable;

    // 1. Link to the correct table in the database
    protected $table = 'parents'; 

    // 2. Define the Primary Key
    protected $primaryKey = 'parent_id';

    // 3. Allow these columns to be filled
    protected $fillable = [
        'parent_name', 
        'username', 
        'email', 
        'password', 
        'phone_number', 
        'gender'
    ];

    // 4. Hide password
    protected $hidden = [
        'password', 'remember_token',
    ];
}