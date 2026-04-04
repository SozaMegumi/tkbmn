<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeType extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'fee_type_id';

    protected $fillable = [
        'name',
        'default_amount',
        'is_recurring',
    ];

    protected $casts = [
        'is_recurring' => 'boolean',
    ];
}
