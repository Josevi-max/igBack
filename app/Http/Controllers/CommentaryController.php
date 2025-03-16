<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Commentary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentaryController extends Controller
{
    public function newComment(Request $request)
    {
        $validator = Validator::make($request->only(['comment','publicationId']), [
            'comment' => 'required|string',
            'publicationId' => 'required|integer|exists:publications,id',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $comment = $request->input('comment');
        $userId = $request->user()->id;
        $publicationId = $request->input('publicationId');
        try {
            Commentary::create([
                'commentary' => $comment,
                'user_id' => $userId,
                'publication_id' => $publicationId
            ]);
            return response()->json(['status' => 'OK']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'KO',
                'message' => 'Error al crear el comentario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
