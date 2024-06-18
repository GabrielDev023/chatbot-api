<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ConversationController;

Route::post('/chat', [ChatController::class, 'sendMessage']);
Route::get('/conversations', [ConversationController::class, 'index']);

