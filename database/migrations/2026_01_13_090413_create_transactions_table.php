<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id('transaction_id');
            $table->string('type'); // 'income' or 'expense'
            $table->string('category'); // e.g. 'School Fee', 'Food', 'Stationary'
            $table->string('description')->nullable(); // e.g. 'Ali Fee Jan', 'Rice 10kg'
            $table->decimal('amount', 10, 2); // Money value
            $table->date('date');
            $table->string('payment_method')->nullable(); // 'Cash', 'Online Transfer'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};