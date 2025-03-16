<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserChat extends Model
{
    use HasFactory;

    protected $table = 'user_chat';
    protected $fillable = [
        'sender_id',
        'receiver_id'
    ];

    public static function getOrCreateChat($senderId, $receiverId)
    {
        return self::firstOrCreate(
            [
                'sender_id' => $senderId,
                'receiver_id' => $receiverId
            ],
            ['created_at' => now(), 'updated_at' => now()]
        );
    }

    public function messages()
    {
        return $this->hasMany(Chat::class, 'user_chat_id');
    }
}