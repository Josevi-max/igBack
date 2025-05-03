<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CommentaryController;
use App\Http\Controllers\PublicationController;
use App\Models\Publication;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api')->name('refresh');
    Route::post('/me', [AuthController::class, 'me'])->middleware('auth:api')->name('me');
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'chat'
], function ($router) {
    Route::post('/send-message', [ChatController::class, 'sendMessage'])->middleware('auth:api')->name('sendMessage');
    Route::post('/create-room', [ChatController::class, 'createRoom'])->middleware('auth:api')->name('createRoom');
    Route::get('/get-chat-messages/{receiverId}', [ChatController::class, 'getChatMessages'])->middleware('auth:api')->name('getChatMessages');
    Route::post('/chage-status-message', [ChatController::class, 'changeStatusMessage'])->middleware('auth:api')->name('changeStatusMessage');
    Route::post('/user-typing', [ChatController::class, 'userTyping'])->middleware('auth:api')->name('userTyping');
    Route::get('/get-info-user/{idUser}', [ChatController::class, 'getInfoUser'])->middleware('auth:api')->name('getInfoUser');
    Route::get('/get-list-user-with-chats/{idUser}', [ChatController::class, 'getListUserWithChats'])->middleware('auth:api')->name('getListUserWithChats');
});


Route::group([
    'middleware' => 'api',
    'prefix' => 'publications'
], function ($router) {
    Route::get('/page/{page}', [PublicationController::class, 'getListPublications'])->middleware('auth:api')->name('getListPublications');
    Route::get('/{id}', [PublicationController::class, 'getPublicationById'])->middleware('auth:api')->name('getPublicationById');
    Route::get('/user/{userId}', [PublicationController::class, 'getPublicationsUser'])->middleware('auth:api')->name('getPublicationsUser');
    Route::post('/like', [PublicationController::class, 'likePublication'])->middleware('auth:api')->name('likePublication');
    Route::post('/unlike', [PublicationController::class, 'unlikePublication'])->middleware('auth:api')->name('unlikePublication');
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'comment'
], function ($router) {
    Route::post('/new-comment', [CommentaryController::class, 'newComment'])->middleware('auth:api')->name('newComment');
    Route::post('/reply-comment', [CommentaryController::class, 'replyComment'])->middleware('auth:api')->name('replyComment');
    Route::post('/like', [CommentaryController::class, 'likeCommentary'])->middleware('auth:api')->name('likeComment');
    Route::post('/unlike', [CommentaryController::class, 'unlikeCommentary'])->middleware('auth:api')->name('unlikeComment');
    Route::get('/get-commentaries/{publicationId}', [CommentaryController::class, 'getCommentaries'])->middleware('auth:api')->name('getCommentaries');
});
