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
        
        // --- ADDED THESE TO FIX THE SQL ERROR & FILE UPLOADS ---
        'title',
        'receipt_path',
        'admin_remarks',
    ];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    // --- ADDED STUDENT RELATIONSHIP ---
    // The AdminController uses $payment->student->student_name in the pending table.
    // Without this, Laravel won't know how to find the student details!
    public function student()
    {
        // Assuming your Student model's primary key is 'student_id'
        return $this->belongsTo(Student::class, 'student_id', 'student_id'); 
    }
}