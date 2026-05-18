<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HafazanRecord extends Model
{
    use HasFactory;

    protected $table = 'hafazan_records';
    protected $primaryKey = 'hafazan_id';

    protected $fillable = [
        'student_id',
        'teacher_id',
        'surah',
        'verses',
        'status',
        'date_recorded',
        'remarks'
    ];
    protected $casts = [
        'date_recorded' => 'date', 
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }
}