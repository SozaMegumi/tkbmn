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
    Schema::table('classrooms', function (Blueprint $table) {
        // Only add it if it doesn't exist
        if (!Schema::hasColumn('classrooms', 'teacher_id')) {
            $table->unsignedBigInteger('teacher_id')->nullable()->after('class_name');
            $table->foreign('teacher_id')->references('teacher_id')->on('teachers')->onDelete('set null');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            //
        });
    }
};
