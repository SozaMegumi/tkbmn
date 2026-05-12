<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentResult extends Model
{
    use HasFactory; 

    // REMOVED: protected $primaryKey = 'result_id'; (Laravel will automatically use 'id' now)

    protected $fillable = [
        'assessment_id',
        'student_id',
        'subject_id',      
        'mastery_level',   
        'teacher_remarks', 
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class, 'assessment_id', 'id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'subject_id');
    }
}