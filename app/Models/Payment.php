<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// 1. Removed: use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory; 
    // 2. Removed: SoftDeletes from here

    protected $table = 'payments'; // Good practice to be explicit
    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'student_id',      // 3. Added this because your Dashboard query uses it
        'invoice_id',
        'amount',
        'method',
        'reference_number',
        'payment_date',
        'status',
    ];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}