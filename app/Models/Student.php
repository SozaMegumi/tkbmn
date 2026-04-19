<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $primaryKey = 'student_id'; // Matches SQL

    protected $fillable = [
        'student_name', 'mykid', 'dob', 'gender', 
        'race', 'religion', 'nationality', 'status', 
        'parent_id', 'class_id'
    ];

    // Link to Parent (For Table Display)
    public function parent() {
        return $this->belongsTo(Guardian::class, 'parent_id', 'parent_id');
    }

    // Link to Class (For Table Display)
    public function classroom() {
        return $this->belongsTo(Classroom::class, 'class_id', 'class_id');
    }
    
    // ==========================================
    // THE FIX: Changed to hasMany (One Student -> Many Days of Attendance)
    // ==========================================
    public function attendances() {
        return $this->hasMany(Attendance::class, 'student_id', 'student_id');
    }
}