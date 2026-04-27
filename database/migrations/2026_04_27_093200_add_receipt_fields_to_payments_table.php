<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            // Adding the new columns we need for Process 8.0
            $table->string('receipt_path')->nullable()->after('status');
            $table->string('admin_remarks')->nullable()->after('receipt_path');
            
            // If your 'title' column doesn't exist yet, uncomment the line below:
            // $table->string('title')->nullable()->after('student_id');
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            // This drops the columns if you ever need to rollback
            $table->dropColumn(['receipt_path', 'admin_remarks']);
        });
    }
};