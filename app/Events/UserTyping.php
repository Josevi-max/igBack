<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class UserTyping implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $roomId;
    public $isTyping;
    public $idUserTyping;
    public function __construct($roomId,$isTyping,$idUserTyping)
    {
        $this->roomId = $roomId;
        $this->isTyping = $isTyping;
        $this->idUserTyping = $idUserTyping;
    }

    public function broadcastWith()
    {
        return [
            'isTyping' => $this->isTyping,
            'idUserTyping' => $this->idUserTyping
        ];
    }

    public function broadcastOn()
    {
        return new PresenceChannel('chat.' . $this->roomId);
    }

    public function broadcastAs()
    {
        return 'user-typing';
    }
}
