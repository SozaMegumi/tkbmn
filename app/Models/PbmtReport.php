<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PbmtReport extends Model
{
    use HasFactory;

    // Tell Laravel it is safe to mass-assign these exact columns
    protected $fillable = [
        'report_type',
        'phase',
        'month',
        'year',
        'data_snapshot',
        'generated_by'
    ];
}