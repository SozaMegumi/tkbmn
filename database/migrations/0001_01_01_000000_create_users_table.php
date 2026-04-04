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
        // --- 1. ADMINS TABLE ---
        Schema::create('admins', function (Blueprint $table) {
            $table->id('admin_id'); // Primary Key
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone_number')->nullable();
            $table->timestamps();
        });

        // --- 2. CLASSROOMS TABLE (Create before Teachers/Students) ---
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id('class_id');
            $table->string('class_name'); // e.g., "Class A (6 Years)"
            $table->integer('capacity')->default(25);
            $table->timestamps();
        });

        // --- 3. TEACHERS TABLE ---
        Schema::create('teachers', function (Blueprint $table) {
            $table->id('teacher_id');
            $table->string('full_name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone_number')->nullable();
            $table->string('gender')->nullable();
            $table->text('address')->nullable();
            $table->date('join_date')->nullable();
            
            // Foreign Key: Link to Class
            $table->unsignedBigInteger('assigned_class_id')->nullable(); 
            $table->timestamps();
        });

        // --- 4. PARENTS TABLE ---
        Schema::create('parents', function (Blueprint $table) {
            $table->id('parent_id');
            $table->string('parent_name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone_number');
            $table->string('gender')->nullable();
            $table->timestamps();
        });

        // --- 5. STUDENTS TABLE ---
        Schema::create('students', function (Blueprint $table) {
            $table->id('student_id');
            $table->unsignedBigInteger('parent_id'); // Link to Parent
            $table->string('student_name');
            $table->string('mykid')->unique()->nullable();
            $table->date('dob');
            $table->string('gender');
            $table->string('race')->nullable();
            $table->string('religion')->nullable();
            $table->string('nationality')->default('Malaysian');
            $table->string('status')->default('active');
            
            // Link to Class
            $table->unsignedBigInteger('class_id')->nullable(); 
            $table->timestamps();

            // Define Foreign Key Constraints
            $table->foreign('parent_id')->references('parent_id')->on('parents')->onDelete('cascade');
        });

        // --- 6. ATTENDANCE TABLE ---
        Schema::create('attendances', function (Blueprint $table) {
            $table->id('attendance_id');
            $table->unsignedBigInteger('student_id');
            $table->date('date');
            $table->string('status'); // Present / Absent
            $table->string('reason')->nullable();
            $table->timestamps();
        });

        // --- 7. EXAMS TABLE ---
        Schema::create('exams', function (Blueprint $table) {
            $table->id('exam_id');
            $table->string('title'); // e.g. "Final Exam 2024"
            $table->string('term');  // e.g. "Term 1"
            $table->date('start_date');
            $table->string('status')->default('scheduled');
            $table->timestamps();
        });

        // --- 8. MARKS TABLE ---
        Schema::create('marks', function (Blueprint $table) {
            $table->id('mark_id');
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('student_id');
            $table->integer('bahasa_melayu')->nullable();
            $table->integer('english')->nullable();
            $table->integer('mathematics')->nullable();
            $table->integer('jawi')->nullable();
            $table->timestamps();
        });

        // --- 9. EVENTS TABLE ---
        Schema::create('events', function (Blueprint $table) {
            $table->id('event_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('event_date');
            $table->string('type'); // Holiday, Activity, Meeting
            $table->timestamps();
        });

        // --- 10. PAYMENTS TABLE ---
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->unsignedBigInteger('student_id');
            $table->string('title'); // e.g. "Monthly Fee Jan"
            $table->decimal('amount', 8, 2);
            $table->string('status')->default('unpaid'); // paid, unpaid
            $table->timestamps();
        });

        // --- LARAVEL DEFAULT TABLES (Required for System) ---
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order to prevent Foreign Key errors
        Schema::dropIfExists('payments');
        Schema::dropIfExists('events');
        Schema::dropIfExists('marks');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('students');
        Schema::dropIfExists('parents');
        Schema::dropIfExists('teachers');
        Schema::dropIfExists('classrooms');
        Schema::dropIfExists('admins');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
    }
};