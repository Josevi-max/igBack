<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Room extends Model
{
    protected $fillable = ['user_id_1', 'user_id_2'];

    public static function getOrCreateRoom($userId1, $userId2)
    {
        // Ordena los IDs para asegurarte de que siempre coincidan
        [$minUserId, $maxUserId] = [min($userId1, $userId2), max($userId1, $userId2)];

        // Busca una room existente
        $room = Room::where('user_id_1', $minUserId)
            ->where('user_id_2', $maxUserId)
            ->first();

        // Si no existe, créala
        if (!$room) {
            $room = Room::create([
                'user_id_1' => $minUserId,
                'user_id_2' => $maxUserId,
            ]);
        }

        return $room;
    }
}
