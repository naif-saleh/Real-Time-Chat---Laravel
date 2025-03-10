<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use App\Events\UserTyping;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ChatController extends Controller
{
    public function index()
    {
        $users = User::where('id', '!=', Auth::id())->get();
        return view('users', compact('users'));
    }


    public function chat($receiver_id)
    {
        $receiver = User::find($receiver_id);

        $messages = Message::where(function ($query) use ($receiver_id){
            $query->where('sender_id', Auth::id())->where('receiver_id', $receiver_id);
        })->orWhere(function ($query) use ($receiver_id){
            $query->where('receiver_id', Auth::id())->where('sender_id', $receiver_id);
        })->get();
        return view('chat', compact('receiver', 'messages'));
    }


    public function sendMessage(Request $request, $receiver_id)
    {
        $message = Message::create([
            'sender_id'     => Auth::id(),
            'receiver_id'   =>$receiver_id,
            'message'       =>$request['message']
        ]);

        broadcast(new MessageEvent($message))->toOthers();
        return response()->json(['status' => 'message sent']);
    }


    public function typing()
    {
        broadcast(new UserTyping(Auth::id()))->toOthers();
        return response()->json(['status' => 'typing']);
    }


    public function setOnline()
    {
        Cache::put('user-is-online-'.Auth::id(), true, now()->addMinutes(5));
        return response()->json(['status' => 'onlie']);
    }

    public function setOffline()
    {
        Cache::forget('user-is-online-'.Auth::id());
        return response()->json(['status' => 'offlie']);
    }
}
