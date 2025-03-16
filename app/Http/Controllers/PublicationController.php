<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Http\Request;
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

        if(!is_numeric($page)) {
            return response()->json([
                'message' => 'Page must be a number',
                'status' => 'KO'
            ], 400);
        }

        if($page < 1) {
            return response()->json([
                'message' => 'Page must be a number up 0',
                'status' => 'KO'
            ], 400);
        }

        $MAX_ELEMENTS_TO_SHOW = 20;

        $publications = Publication::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($MAX_ELEMENTS_TO_SHOW, ['*'], 'page', $page);

        return response()->json(['status' => 'OK', 'response' => $publications]);
    }
}
