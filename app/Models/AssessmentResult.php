<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentResult extends Model
{
    use HasFactory; // Removed SoftDeletes unless you explicitly added $table->softDeletes() in your migration earlier

    protected $primaryKey = 'result_id';

    // UPDATED: Changed to fit the Kindergarten KSPK format!
    protected $fillable = [
        'assessment_id',
        'student_id',
        'subject_id',      // ADDED: We need to know which subject is being graded
        'mastery_level',   // CHANGED: Replaced 'marks_obtained' (1 = Belum Menguasai, 2 = Sedang Maju, 3 = Menguasai)
        'teacher_remarks', // CHANGED: Replaced 'comments'
    ];

    public function assessment()
    {
        // Parameter 2: Foreign key in THIS table. Parameter 3: Primary key in the Target table.
        return $this->belongsTo(Assessment::class, 'assessment_id', 'id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    // ADDED: The relationship to the Subject model
    public function subject()
    {
        // First parameter is the foreign key on this table.
        // Second parameter is the primary key on the subjects table.
        return $this->belongsTo(Subject::class, 'subject_id', 'subject_id');
    }
}