<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $primaryKey = 'student_id'; // Matches SQL

    protected $fillable = [
        'student_name', 'mykid', 'dob', 'gender', 
        'race', 'religion', 'nationality', 'status', 
        'parent_id', 'class_id'
    ];

   // A Student belongs to one Guardian
    public function parent() {
        return $this->belongsTo(Guardian::class, 'parent_id', 'parent_id');
    }

    // While we are here, link the student to their class!
    public function classroom() {
        return $this->belongsTo(Classroom::class, 'class_id', 'class_id');
    }
    
    // Link to Attendance (One Student -> Many Days of Attendance)
    public function attendances() {
        return $this->hasMany(Attendance::class, 'student_id', 'student_id');
    }

    // ==========================================
    // THE FIX: Added Payments Relationship for Finance Dashboard
    // ==========================================
    public function payments() {
        return $this->hasMany(Payment::class, 'student_id', 'student_id');
    }
}