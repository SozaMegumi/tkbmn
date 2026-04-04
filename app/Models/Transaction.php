<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $primaryKey = 'transaction_id';
    
    protected $fillable = [
        'type', 
        'category', 
        'description', 
        'amount', 
        'date', 
        'payment_method'
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2'
    ];
}