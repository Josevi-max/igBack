<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Commentary;
use App\Models\likesCommentaries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentaryController extends Controller
{
    public function newComment(Request $request)
    {
        $validator = Validator::make($request->only(['comment', 'publicationId']), [
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
            $newCommentaryData = Commentary::create([
                'commentary' => $comment,
                'user_id' => $userId,
                'publication_id' => $publicationId
            ]);
            return response()->json(['status' => 'OK', 'data' => $newCommentaryData]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'KO',
                'message' => 'Error al crear el comentario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function replyComment(Request $request)
    {
        $validator = Validator::make($request->only(['comment', 'commentaryId', 'publicationId']), [
            'comment' => 'required|string',
            'commentaryId' => 'required|integer|exists:commentaries,id',
            'publicationId' => 'required|integer|exists:publications,id',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $comment = $request->input('comment');
        $userId = $request->user()->id;
        $commentaryId = $request->input('commentaryId');
        try {
            $newCommentaryData = Commentary::create([
                'commentary' => $comment,
                'user_id' => $userId,
                'reply_to_id' => $commentaryId,
                'reply_to_user_id' => Commentary::findOrFail($commentaryId)->user_id,
                'publication_id' => $request->input('publicationId')
            ]);
            return response()->json(['status' => 'OK', 'data'=>$newCommentaryData]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'KO',
                'message' => 'Error while replying to the comment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function likeCommentary(Request $request)
    {
        $validator = Validator::make($request->only(['commentaryId']), [
            'commentaryId' => 'required|integer|exists:commentaries,id'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $userId = $request->user()->id;
        $commentaryId = $request->input('commentaryId');
        $alreadyLiked = likesCommentaries::where('user_id', $userId)
            ->where('commentary_id', $commentaryId)
            ->exists();
        if ($alreadyLiked) {
            return response()->json([
                'status' => '400',
                'message' => 'You have already liked this commentary'
            ], 400);
        }
        try {
            $commentary = Commentary::findOrFail($commentaryId);
            $commentary->likes()->create(['user_id' => $userId]);
            $commentary->increment('likes');
            return response()->json(['status' => 'OK']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => '400',
                'message' => 'Error while liking the commentary',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function unlikeCommentary(Request $request)
    {
        $validator = Validator::make($request->only(['commentaryId']), [
            'commentaryId' => 'required|integer|exists:commentaries,id'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $userId = $request->user()->id;
        $commentaryId = $request->input('commentaryId');
        $alreadyLiked = likesCommentaries::where('user_id', $userId)
            ->where('commentary_id', $commentaryId)
            ->exists();
        if (!$alreadyLiked) {
            return response()->json([
                'status' => '400',
                'message' => 'You have not liked this commentary yet'
            ], 400);
        }
        try {
            likesCommentaries::where('commentary_id', $commentaryId)
                ->where('user_id', $userId)
                ->delete();
            Commentary::find($commentaryId)->decrement('likes');

            return response()->json(['status' => 'OK']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => '400',
                'message' => 'Error while unliking the commentary',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getCommentaries($publicationId)
    {
        try {
            $commentaries = Commentary::where('publication_id', $publicationId)->with(['user', 'likes'])->get();
            return response()->json(['status' => 'OK', 'data' => $commentaries]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'KO',
                'message' => 'Error while fetching commentaries',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
