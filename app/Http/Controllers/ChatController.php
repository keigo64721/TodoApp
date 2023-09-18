<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageCreated;
use App\Http\Resources\ChatResource;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChatController extends Controller
{
    public function index()
    {
        return Inertia::render('Chat/Index');
    }

    public function list()
    {
        $chat_messages = ChatMessage::with('user')
            ->limit(10)
            ->latest()
            ->get();

        return ChatResource::collection($chat_messages);
    }

    public function store(Request $request)
    {
        $chat_message = new ChatMessage();
        $chat_message->user_id = auth()->id();
        $chat_message->message = $request->message;
        $chat_message->save();

        ChatMessageCreated::dispatch($chat_message); //ブロードキャスト実行

        return to_route('chat.index'); //リダイレクト
    }
}
