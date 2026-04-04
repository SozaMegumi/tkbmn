<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Teacher extends Authenticatable
{
    use Notifiable;

    // 1. Define the Primary Key correctly
    protected $primaryKey = 'teacher_id';

    // 2. Define the columns that can be filled
    protected $fillable = [
        'full_name', 
        'username', 
        'email', 
        'password', 
        'phone_number', 
        'gender', 
        'address', 
        'join_date', 
        'assigned_class_id'
    ];

    // 3. Hide the password for security
    protected $hidden = [
        'password', 'remember_token',
    ];
}