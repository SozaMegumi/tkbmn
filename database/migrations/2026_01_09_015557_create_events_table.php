<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop the table if it exists (so we can recreate it cleanly)
        Schema::dropIfExists('events');

        // 2. Create the table with correct columns
        Schema::create('events', function (Blueprint $table) {
            $table->id('event_id'); // Matches Model primary key
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date'); // <--- This fixes your error
            $table->date('end_date')->nullable();
            $table->string('theme')->default('primary'); // For colors (red/blue)
            $table->integer('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};