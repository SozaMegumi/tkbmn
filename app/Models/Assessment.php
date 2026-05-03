<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Notice there is NO "use SoftDeletes" up here anymore!

class Assessment extends Model
{
    use HasFactory; // Notice it ONLY says HasFactory here now!

    protected $fillable = [
        'title',       
        'start_date',  
        'end_date',    
        'status',      
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function results()
    {
        return $this->hasMany(AssessmentResult::class, 'assessment_id');
    }
}