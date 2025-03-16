<?php
// routes/channels.php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{roomId}', function ($user, $chat) {
    return $user;
});