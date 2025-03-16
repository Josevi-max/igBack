<?php

namespace App\Events;

use App\Models\Chat;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class NewChatMessage implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $chat;
    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }

    public function broadcastWith()
    {
        return [
            'chat' => $this->chat
        ];
    }

    public function broadcastOn()
    {
        return new PresenceChannel('chat.' . $this->chat->room_id);
    }

    public function broadcastAs()
    {
        return 'new-chat-message';
    }
}
