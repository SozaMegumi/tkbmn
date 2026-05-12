<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('hafazan_records', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('student_id');
        $table->unsignedBigInteger('teacher_id');
        $table->string('surah');
        $table->string('verses')->nullable(); // e.g., "Ayat 1-5"
        $table->enum('status', ['Cemerlang', 'Baik', 'Sederhana', 'Lemah']); // Grading scale
        $table->date('date_recorded');
        $table->text('remarks')->nullable();
        $table->timestamps();

        // Add foreign keys (assuming your keys are named student_id and teacher_id)
        $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
        $table->foreign('teacher_id')->references('teacher_id')->on('teachers')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hafazan_records');
    }
};
