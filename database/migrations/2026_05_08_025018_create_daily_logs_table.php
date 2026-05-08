<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daily_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->unsignedBigInteger('student_id');
            $table->date('date');
            $table->string('mood')->nullable(); // Happy, Tired, Crying
            $table->string('meals')->nullable(); // Ate All, Ate Half
            $table->boolean('napped')->default(false);
            $table->text('notes')->nullable(); // Teacher remarks
            $table->timestamps();

            // Link it to the student
            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_logs');
    }
};
