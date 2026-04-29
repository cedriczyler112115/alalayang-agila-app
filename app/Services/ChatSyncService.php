<?php

namespace App\Services;

use App\Models\LibRegion;
use App\Models\LibClubName;
use App\Models\User;
use Musonza\Chat\Facades\ChatFacade as Chat;

class ChatSyncService
{
    public static function syncAll()
    {
        // Sync Regions securely dynamically
        $regions = LibRegion::all();
        foreach ($regions as $region) {
            $conversation = \Musonza\Chat\Models\Conversation::whereJsonContains('data->type', 'region')
                ->whereJsonContains('data->lib_region_id', $region->id)
                ->first();

            $participants = User::where('lib_region_id', $region->id)->get()->all();

            if (!$conversation) {
                if(count($participants) > 0) {
                    $conversation = Chat::createConversation($participants)->makeDirect();
                    $conversation->update(['data' => ['type' => 'region', 'lib_region_id' => $region->id], 'private' => false, 'direct_message' => false]);
                }
            } else {
                if(count($participants) > 0) {
                    Chat::conversation($conversation)->addParticipants($participants);
                }
            }
        }

        // Sync Clubs securely dynamically
        $clubs = LibClubName::all();
        foreach ($clubs as $club) {
            $conversation = \Musonza\Chat\Models\Conversation::whereJsonContains('data->type', 'club')
                ->whereJsonContains('data->lib_club_name_id', $club->id)
                ->first();

            $participants = User::where('lib_club_name_id', $club->id)->get()->all();

            if (!$conversation) {
                if(count($participants) > 0) {
                    $conversation = Chat::createConversation($participants)->makeDirect();
                    $conversation->update(['data' => ['type' => 'club', 'lib_club_name_id' => $club->id], 'private' => false, 'direct_message' => false]);
                }
            } else {
                if(count($participants) > 0) {
                    Chat::conversation($conversation)->addParticipants($participants);
                }
            }
        }
    }
}
