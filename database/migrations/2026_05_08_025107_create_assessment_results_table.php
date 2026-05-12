<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assessment_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('subject_id');
            $table->string('mastery_level')->nullable(); // TP 1, TP 2, TP 3
            $table->string('teacher_remarks')->nullable();
            $table->timestamps();

            // Optional: If you want to add foreign keys, you can uncomment these
            // $table->foreign('assessment_id')->references('id')->on('assessments')->onDelete('cascade');
            // $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
            // $table->foreign('subject_id')->references('subject_id')->on('subjects')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_results');
    }
};