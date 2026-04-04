<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    // 1. Define Primary Key (Matches your Database)
    protected $primaryKey = 'class_id';

    // 2. Define Fillable Columns
    protected $fillable = [
        'class_name', 
        'capacity'
    ];
    
    // IMPORTANT: We do NOT use "SoftDeletes" here because 
    // your database table does not have a 'deleted_at' column.

    // 3. Relationships (Optional but good to have)
    public function teacher() {
        return $this->hasOne(Teacher::class, 'assigned_class_id', 'class_id');
    }

    public function students() {
        return $this->hasMany(Student::class, 'class_id', 'class_id');
    }
}