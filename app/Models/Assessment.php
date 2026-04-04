<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assessment extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'assessment_id';

    protected $fillable = [
        'class_id',
        'academic_year_id',
        'title',
        'type',
        'date',
        'total_marks',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function results()
    {
        return $this->hasMany(AssessmentResult::class, 'assessment_id');
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'class_id');
    }
}
