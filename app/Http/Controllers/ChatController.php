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

        abort_unless($user->canUseChatFeature(), 403, 'Chat with Kuya is only available for availed members.');

        $regionConversation = null;
        $clubConversation = null;

        if ($user->lib_region_id) {
            $regionConversation = Conversation::whereJsonContains('data->type', 'region')
                ->whereJsonContains('data->lib_region_id', $user->lib_region_id)
                ->first();
                
            if (!$regionConversation) {
                $regionConversation = Chat::createConversation([$user]);
                $regionConversation->update(['data' => [
                    'type' => 'region',
                    'lib_region_id' => $user->lib_region_id,
                    'title' => $user->region->name ?? 'Region Chat'
                ]]);
            }
        }

        if ($user->lib_club_name_id) {
            $clubConversation = Conversation::whereJsonContains('data->type', 'club')
                ->whereJsonContains('data->lib_club_name_id', $user->lib_club_name_id)
                ->first();
                
            if (!$clubConversation) {
                $clubConversation = Chat::createConversation([$user]);
                $clubConversation->update(['data' => [
                    'type' => 'club',
                    'lib_club_name_id' => $user->lib_club_name_id,
                    'title' => $user->club->name ?? 'Club Chat'
                ]]);
            }
        }

        // Get all other users for creating chats
        $allUsers = \App\Models\User::where('id', '!=', $user->id)->where('status', 1)->orderBy('first_name')->get();

        // Get custom conversations this user is part of
        $participations = Chat::conversations()->setParticipant($user)->limit(200)->get();

        // Filter out the global region and club conversations from custom list if they appear
        $customConversations = collect($participations->items())
            ->map(function ($p) {
                return $p->conversation;
            })
            ->filter(function ($conv) {
                if (!$conv) return false;
                $data = is_string($conv->data) ? json_decode($conv->data, true) : $conv->data;
                $type = $data['type'] ?? '';
                return in_array($type, ['p2p', 'custom_group']);
            })->values();

        return view('chat.index', compact('regionConversation', 'clubConversation', 'user', 'allUsers', 'customConversations'));
    }

    public function getMessages(Conversation $conversation)
    {
        // Get all messages for the conversation, bypassing the participant filter
        // so that users who joined the club later can still see history.
        $messages = \Musonza\Chat\Models\Message::where('conversation_id', $conversation->id)
            ->orderBy('created_at', 'asc')
            ->get();

        $payload = [];
        foreach ($messages as $message) {
            $sender = $message->sender;
            $payload[] = [
                'id' => $message->id,
                'body' => $message->body,
                'type' => $message->type ?? 'text',
                'data' => $message->data ?? [],
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
        $validated = $request->validate([
            'message' => 'required|string',
            'message_type' => 'nullable|in:text,sticker',
            'sticker' => 'nullable|string|max:50',
            'sticker_label' => 'nullable|string|max:100',
        ]);

        $user = Auth::user();
        $messageType = $validated['message_type'] ?? 'text';
        $messageData = [];

        if ($messageType === 'sticker') {
            $messageData = [
                'sticker' => $validated['sticker'] ?? $validated['message'],
                'label' => $validated['sticker_label'] ?? 'Sticker',
            ];
        }

        if (!$conversation->getParticipants()->contains('id', $user->id)) {
            Chat::conversation($conversation)->addParticipants([$user]);
        }

        $message = Chat::message($validated['message'])
            ->type($messageType)
            ->data($messageData)
            ->from($user)
            ->to($conversation)
            ->send();

        $formatted = [
            'id' => $message->id,
            'body' => $message->body,
            'type' => $message->type ?? $messageType,
            'data' => $message->data ?? $messageData,
            'created_at' => $message->created_at->format('M d, H:i'),
            'sender' => [
                'id' => $user->id,
                'name' => $user->fullname,
                'avatar' => $user->profile_photo ? asset('storage/' . $user->profile_photo) : asset('images/default-avatar.png')
            ]
        ];

        return response()->json(['success' => true, 'message' => $formatted]);
    }

    public function updateCustomConversation(Request $request, Conversation $conversation)
    {
        $user = Auth::user();
        $data = is_string($conversation->data) ? json_decode($conversation->data, true) : ($conversation->data ?? []);
        $type = $data['type'] ?? '';

        if ($type !== 'custom_group') {
            return response()->json(['success' => false, 'message' => 'Only created group chats can be renamed.'], 403);
        }

        if (!$conversation->getParticipants()->contains('id', $user->id)) {
            return response()->json(['success' => false, 'message' => 'You are not allowed to rename this conversation.'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $updatedData = array_merge($data, [
            'title' => trim($validated['title']),
        ]);

        $conversation->update(['data' => $updatedData]);

        return response()->json([
            'success' => true,
            'title' => $updatedData['title'],
        ]);
    }

    public function destroyCustomConversation(Conversation $conversation)
    {
        $user = Auth::user();
        $data = is_string($conversation->data) ? json_decode($conversation->data, true) : ($conversation->data ?? []);
        $type = $data['type'] ?? '';

        if (!in_array($type, ['p2p', 'custom_group'], true)) {
            return response()->json(['success' => false, 'message' => 'Only created direct and group chats can be deleted.'], 403);
        }

        if (!$conversation->getParticipants()->contains('id', $user->id)) {
            return response()->json(['success' => false, 'message' => 'You are not allowed to delete this conversation.'], 403);
        }

        $conversation->delete();

        return response()->json(['success' => true]);
    }

    public function createCustomConversation(Request $request)
    {
        $request->validate([
            'type' => 'required|in:p2p,custom_group',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'title' => 'nullable|string|max:255'
        ]);

        $user = Auth::user();
        $participants = \App\Models\User::whereIn('id', $request->user_ids)
            ->where('id', '!=', $user->id)
            ->get()
            ->push($user);

        if ($request->type === 'p2p') {
            if (count($request->user_ids) !== 1) {
                return response()->json(['success' => false, 'message' => 'Direct messages must have exactly one other user.'], 400);
            }

            // Check if a P2P conversation already exists between these two users
            $otherUserId = $request->user_ids[0];
            $existingP2P = Chat::conversations()->setParticipant($user)->get()->first(function ($participation) use ($otherUserId) {
                $conv = $participation->conversation;
                if (!$conv) return false;
                $data = is_string($conv->data) ? json_decode($conv->data, true) : $conv->data;
                if (($data['type'] ?? '') === 'p2p') {
                    $hasOther = $conv->getParticipants()->contains('id', $otherUserId);
                    return $hasOther;
                }
                return false;
            });

            if ($existingP2P) {
                return response()->json(['success' => true, 'conversation_id' => $existingP2P->conversation_id]);
            }

            try {
                $conversation = Chat::createConversation($participants->all())->makeDirect();
                $conversation->update(['data' => ['type' => 'p2p']]);
            } catch (\Musonza\Chat\Exceptions\DirectMessagingExistsException $e) {
                $otherUser = \App\Models\User::find($otherUserId);
                $conversation = Chat::conversations()->between($user, $otherUser);
                $conversation->update(['data' => ['type' => 'p2p']]);
            }
        }
        else {
            $conversation = Chat::createConversation($participants->all());
            $conversation->update(['data' => [
                    'type' => 'custom_group',
                    'title' => $request->title ?: 'Group Chat'
                ]]);
        }

        return response()->json(['success' => true, 'conversation_id' => $conversation->id]);
    }
}
