<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Musonza\Chat\Facades\ChatFacade as Chat;
use Musonza\Chat\Models\Conversation;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $regionConversation = null;
        $clubConversation = null;

        if ($user->lib_region_id) {
            $regionConversation = Conversation::whereJsonContains('data->type', 'region')
                ->whereJsonContains('data->lib_region_id', $user->lib_region_id)
                ->first();
        }

        if ($user->lib_club_name_id) {
            $clubConversation = Conversation::whereJsonContains('data->type', 'club')
                ->whereJsonContains('data->lib_club_name_id', $user->lib_club_name_id)
                ->first();
        }

        return view('chat.index', compact('regionConversation', 'clubConversation', 'user'));
    }

    public function getMessages(Conversation $conversation)
    {
        // Must verify user is participant natively
        // if (!$conversation->getParticipants()->contains('id', Auth::id())) {
        //     return response()->json(['error' => 'Unauthorized'], 403);
        // }

        $messages = Chat::conversation($conversation)->setParticipant(Auth::user())->setPaginationParams(['perPage' => 100, 'sorting' => 'asc'])->getMessages();

        $payload = [];
        foreach ($messages as $message) {
            $sender = $message->sender;
            $payload[] = [
                'id' => $message->id,
                'body' => $message->body,
                'created_at' => $message->created_at->format('M d, H:i'),
                'sender' => [
                    'id' => $sender->id,
                    'name' => $sender->fullname ?? 'Unknown',
                    'avatar' => $sender->profile_photo ? asset('storage/' . $sender->profile_photo) : asset('images/default-avatar.png')
                ]
            ];
        }

        return response()->json(['messages' => $payload]);
    }

    public function sendMessage(Request $request, Conversation $conversation)
    {
        $request->validate(['message' => 'required|string']);
        $user = Auth::user();

        if (!$conversation->getParticipants()->contains('id', $user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message = Chat::message($request->message)
            ->from($user)
            ->to($conversation)
            ->send();

        $formatted = [
            'id' => $message->id,
            'body' => $message->body,
            'created_at' => $message->created_at->format('M d, H:i'),
            'sender' => [
                'id' => $user->id,
                'name' => $user->fullname,
                'avatar' => $user->profile_photo ? asset('storage/' . $user->profile_photo) : asset('images/default-avatar.png')
            ]
        ];

        return response()->json(['success' => true, 'message' => $formatted]);
    }
}
