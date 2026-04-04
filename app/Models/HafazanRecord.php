<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HafazanRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'hafazan_id';

    protected $fillable = [
        'student_id',
        'teacher_id',
        'surah_name',
        'juz_number',
        'verse_range',
        'fluency_level',
        'tajweed_notes',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
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
