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
        Schema::create('pbmt_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_type'); // e.g., 'takwim', 'unjuran', 'berkelompok', 'prestasi'
            $table->string('phase')->nullable(); // e.g., 'Fasa 1'
            $table->string('month')->nullable(); // e.g., 'Januari'
            $table->integer('year');
            $table->json('data_snapshot'); // Store the calculated data at the time of generation
            $table->string('generated_by'); // Admin name
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pbmt_reports');
    }
};
