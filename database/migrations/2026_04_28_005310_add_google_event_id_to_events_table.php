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
    Schema::table('events', function (Blueprint $table) {
        // This will hold the Google ID. It is nullable because past events don't have one.
        $table->string('google_event_id')->nullable()->after('theme'); 
    });
}

public function down()
{
    Schema::table('events', function (Blueprint $table) {
        $table->dropColumn('google_event_id');
    });
}
};
