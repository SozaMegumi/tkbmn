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
    Schema::create('messages', function (Blueprint $table) {
        $table->bigIncrements('message_id'); // Custom Primary Key
        
        // Polymorphic Sender (e.g., App\Models\Parent, ID: 1)
        $table->unsignedBigInteger('sender_id');
        $table->string('sender_type'); 
        
        // Polymorphic Receiver (e.g., App\Models\Teacher, ID: 2)
        $table->unsignedBigInteger('receiver_id');
        $table->string('receiver_type'); 
        
        $table->text('message_content'); // Custom column name
        $table->timestamp('read_at')->nullable(); // Timestamp for read receipt
        
        $table->timestamps();
        $table->softDeletes(); // For SoftDeletes trait
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
