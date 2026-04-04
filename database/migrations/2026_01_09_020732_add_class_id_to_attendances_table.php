<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Add the missing column
            // nullable() allows it to be empty for existing records so it doesn't crash
            $table->foreignId('class_id')
                  ->nullable() 
                  ->after('student_id') // Places it nicely after student_id
                  ->constrained('classrooms', 'class_id')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropColumn('class_id');
        });
    }
};