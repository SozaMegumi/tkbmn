<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    // 1. Custom Primary Key
    protected $primaryKey = 'message_id';

    // 2. Allow Mass Assignment
    protected $fillable = [
        'sender_id',
        'sender_type',
        'receiver_id',
        'receiver_type',
        'message_content', // Renamed from 'message'
        'read_at',
    ];

    // 3. Casts
    protected $casts = [
        'read_at' => 'datetime',
    ];

    // 4. Relationships (Polymorphic)
    public function sender()
    {
        return $this->morphTo();
    }

    public function receiver()
    {
        return $this->morphTo();
    }

    // 5. Helper Scope to get conversation between two entities
    public function scopeConversation($query, $user1_id, $user1_type, $user2_id, $user2_type)
    {
        return $query->where(function($q) use ($user1_id, $user1_type, $user2_id, $user2_type) {
            $q->where('sender_id', $user1_id)
              ->where('sender_type', $user1_type)
              ->where('receiver_id', $user2_id)
              ->where('receiver_type', $user2_type);
        })->orWhere(function($q) use ($user1_id, $user1_type, $user2_id, $user2_type) {
            $q->where('sender_id', $user2_id)
              ->where('sender_type', $user2_type)
              ->where('receiver_id', $user1_id)
              ->where('receiver_type', $user1_type);
        });
    }
}