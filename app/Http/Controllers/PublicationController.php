<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\likesPublications;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PublicationController extends Controller
{
    public function getPublicationById($idPublication)
    {
        try {
            $publication = Publication::find($idPublication);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Publication not found',
                'status' => 'KO'
            ], 404);
        }

        return response()->json(['status' => 'OK', 'response' => $publication]);
    }
    public function getPublicationsUser($idUser)
    {
        try {
            $user = User::find($idUser);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found',
                'status' => 'KO'
            ], 404);
        }
        return response()->json(['status' => 'OK', 'response' => $user->publications()->paginate(9)]);
    }

    public function getListPublications($page)
    {

        if (!is_numeric($page)) {
            return response()->json([
                'message' => 'Page must be a number',
                'status' => 'KO'
            ], 400);
        }

        if ($page < 1) {
            return response()->json([
                'message' => 'Page must be a number up 0',
                'status' => 'KO'
            ], 400);
        }

        $MAX_ELEMENTS_TO_SHOW = 20;
        $authUserId = Auth::id();
        $publications = Publication::with([
            'user',
            'comments' => function ($query) {
                $query->where('user_id', Auth::id());
            }
        ])
        ->orderBy('created_at', 'desc')
        ->paginate($MAX_ELEMENTS_TO_SHOW, ['*'], 'page', $page);

        $publicationIds = $publications->getCollection()->pluck('id')->toArray();

        $likedPublicationIds = likesPublications::where('user_id', $authUserId)
            ->whereIn('publication_id', $publicationIds)
            ->pluck('publication_id')
            ->toArray();

        $publications->setCollection(
            $publications->getCollection()->map(function ($publication) use ($likedPublicationIds) {
                $publication->liked_by_auth_user = in_array($publication->id, $likedPublicationIds);
                return $publication;
            })
        );

        return response()->json(['status' => 'OK', 'response' => $publications]);
    }

    public function likePublication(Request $request)
    {
        $validator = Validator::make($request->only(['publicationId']), [
            'publicationId' => 'required|integer:publications,id',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $userId = $request->user()->id;
        $publicationId = $request->input('publicationId');
        $alreadyLiked = likesPublications::where('user_id', $userId)
            ->where('publication_id', $publicationId)
            ->exists();
        if ($alreadyLiked) {
            return response()->json([
                'status' => 'KO',
                'message' => 'You have already liked this publication'
            ], 400);
        }
        try {
            Publication::find($publicationId)->likes()->create(['user_id' => $userId]);
            Publication::find($publicationId)->increment('likes');
            return response()->json(['status' => 'OK']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'KO',
                'message' => 'Error while liking the publication',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function unlikePublication(Request $request)
    {
        $validator = Validator::make($request->only(['publicationId']), [
            'publicationId' => 'required|integer|exists:publications,id',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $userId = $request->user()->id;
        $publicationId = $request->input('publicationId');
        $alreadyLiked = likesPublications::where('user_id', $userId)
            ->where('publication_id', $publicationId)
            ->exists();
        if (!$alreadyLiked) {
            return response()->json([
                'status' => 'KO',
                'message' => 'You have not liked this publication yet'
            ], 400);
        }
        try {
            likesPublications::where('publication_id', $publicationId)
                ->where('user_id', $userId)
                ->delete();
            Publication::find($publicationId)->decrement('likes');

            return response()->json(['status' => 'OK']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'KO',
                'message' => 'Error while unliking the publication',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
