<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    // Match the Primary Key in your ERD
    protected $primaryKey = 'event_id';

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'theme',       // e.g., 'Holiday' (Red), 'Activity' (Blue)
        'created_by',
        'google_event_id'
    ];
    
    // Cast dates so Carbon functions work (e.g., format('d M'))
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
}