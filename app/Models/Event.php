<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    // Tell Laravel the primary key is 'event_id'
    protected $primaryKey = 'event_id';

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'theme',
        'created_by',
        'google_event_id'
    ];
}