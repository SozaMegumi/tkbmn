<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    // We explicitly tell Laravel our primary key is 'admin_id', not 'id'
    protected $primaryKey = 'admin_id';

    // Matches the columns in your database
    protected $fillable = [
        'name', 'email', 'password', 'phone_number'
    ];

    // Hide the password when retrieving user data
    protected $hidden = [
        'password', 'remember_token',
    ];
}