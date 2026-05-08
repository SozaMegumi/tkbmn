<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyLog extends Model
{
    protected $primaryKey = 'log_id';
    protected $fillable = ['student_id', 'date', 'mood', 'meals', 'napped', 'notes'];

    // Cast napped to a true/false boolean
    protected $casts = ['napped' => 'boolean'];

    public function student() {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }
}