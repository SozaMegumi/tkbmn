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
        'assigned_class_id' // Your foreign key!
    ];

    // 3. Hide the password for security
    protected $hidden = [
        'password', 'remember_token',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    // A Teacher is assigned to ONE Classroom.
    // Because 'assigned_class_id' is on the Teacher's table, we use belongsTo.
    public function classroom()
    {
        // Parameter 1: The Model it connects to
        // Parameter 2: The foreign key column in THIS (Teacher) table
        // Parameter 3: The primary key column in the TARGET (Classroom) table
        return $this->belongsTo(Classroom::class, 'assigned_class_id', 'class_id');
    }
}