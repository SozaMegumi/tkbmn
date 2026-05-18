<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// 1. Removed "SoftDeletes" import

class Attendance extends Model
{
    use HasFactory; // 2. Removed "SoftDeletes" from here

    protected $primaryKey = 'attendance_id';

    protected $fillable = [
        'student_id',
        'class_id',
        'date',
        'status',
        'reason',
        'attachment', 
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'class_id');
    }
}