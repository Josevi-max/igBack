<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\NewChatMessage;
use App\Events\NewMessage;
use App\Events\UserTyping;
use App\Models\Chat;
use App\Models\Room;
use App\Models\User;
use App\Models\UserChat;
use App\Notifications\NewChatMessageNotification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Broadcast;

class ChatController extends Controller
{

    public function userTyping(Request $request)
    {
        $validator = Validator::make($request->only(['isUserTyping', 'roomId', 'idUserTyping']), [
            'isUserTyping' => 'required|boolean',
            'roomId' => 'required|integer',
            'idUserTyping' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $isUserTyping = $request->input('isUserTyping');
        $roomId = $request->input('roomId');
        $idUserTyping = $request->input('idUserTyping');

        broadcast(new UserTyping($roomId, $isUserTyping, $idUserTyping));
        return response()->json(['status' => 'Event sent!']);
    }

    public function getListUserWithChats($userId)
    {
        try {
            $usersChattedWith = User::whereHas('sentMessages', function ($query) use ($userId) {
                $query->where('receiver_id', $userId);
            })->orWhereHas('receivedMessages', function ($query) use ($userId) {
                $query->where('sender_id', $userId);
            })->get();
            return response()->json([
                'success' => true,
                'data' => $usersChattedWith
            ], 200);
        } catch (\Throwable $error) {
            return response()->json([
                'message' => 'An error occurred getting the info of that user',
                'status' => 'KO',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function getInfoUser($idUser)
    {
        try {
            $dataUser = User::find($idUser);
            return response()->json([
                'success' => true,
                'data' => $dataUser
            ], 200);
        } catch (\Throwable $error) {
            return response()->json([
                'message' => 'An error occurred getting the info of that user',
                'status' => 'KO',
                'error' => $error->getMessage()
            ], 500);
        }
    }


    public function createRoom(Request $request)
    {
        $validator = Validator::make($request->only(['sender_id', 'receiver_id']), [
            'sender_id' => 'required|integer',
            'receiver_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $senderId = $request->input('sender_id');
        $receiverId = $request->input('receiver_id');

        try {
            $roomData = Room::getOrCreateRoom($senderId, $receiverId);
            return response()->json([
                'success' => true,
                'data' => ['roomId' => $roomData->id]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred creating your room',
                'status' => 'KO',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->only(['message', 'sender_id', 'receiver_id', 'tempId']), [
            'message' => 'required|string',
            'sender_id' => 'required|integer',
            'receiver_id' => 'required|integer',
            'tempId' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $message = $request->input('message');
        $senderId = $request->input('sender_id');
        $receiverId = $request->input('receiver_id');
        $tempId = $request->input('tempId');

        $roomData = Room::getOrCreateRoom($senderId, $receiverId);

        $newChatAdded = Chat::create([
            'message' => $message,
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'room_id' => $roomData->id,
            'tempId' => $tempId,
            'status' => 'sent'
        ]);

        broadcast(new NewChatMessage($newChatAdded));

        return response()->json(['status' => 'Message sent!']);
    }

    public function changeStatusMessage(Request $request)
    {
        $validator = Validator::make($request->only(['idMessage', 'status']), [
            'status' => 'required|in:sent,delivered,read',
            'idMessage' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $status = $request->input('status');
        $idMessage = $request->input('idMessage');

        if ($idMessage === -1) {
            Chat::where('sender_id', auth()->id())->update(['status' => $status]);
        } else {
            Chat::where('id', $idMessage)->update(['status' => $status]);
        }

        return response()->json(['status' => 'OK']);
    }

    public function getChatMessages($receiverId)
    {
        if (!is_numeric($receiverId) || $receiverId <= 0) {
            return response()->json([
                'message' => 'Invalid receiver ID',
                'status' => 'KO'
            ], 400);
        }

        try {
            $userId = auth()->id();

            $messages = Chat::where(function ($query) use ($userId, $receiverId) {
                $query->where('sender_id', $userId)->where('receiver_id', $receiverId);
            })
                ->orWhere(function ($query) use ($userId, $receiverId) {
                    $query->where('sender_id', $receiverId)->where('receiver_id', $userId);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json(['status' => 'OK', 'response' => $messages]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching messages',
                'status' => 'KO',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
