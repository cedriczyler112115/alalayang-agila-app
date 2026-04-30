@extends('layouts.app')

@section('title', 'Group Chat - Caragados EC')

@section('content')
<!-- Include jQuery and Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/jquery-confirm@3.3.4/css/jquery-confirm.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-confirm@3.3.4/js/jquery-confirm.min.js"></script>

<style>
    .chat-layout {
        display: flex;
        height: min(calc(100vh - 120px), 800px);
        margin-top: 2rem;
        background-color: var(--card-bg);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }
    
    .chat-sidebar {
        width: 320px;
        border-right: 1px solid var(--border-color);
        background-color: rgba(0,0,0,0.01);
        display: flex;
        flex-direction: column;
    }
    
    .chat-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border-color);
        background-color: var(--card-bg);
    }
    
    .chat-groups {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
    }
    
    .chat-group-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border-radius: var(--radius-md);
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid transparent;
        margin-bottom: 0.5rem;
    }
    
    .chat-group-item:hover, .chat-group-item.active {
        background-color: var(--card-bg);
        border-color: var(--border-color);
        box-shadow: var(--shadow-sm);
    }
    .chat-group-item.active {
        border-right: 4px solid var(--accent);
    }
    
    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background-color: var(--card-bg);
    }
    
    .chat-main-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 2rem;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        background-color: rgba(0,0,0,0.01);
    }
    
    .chat-input-area {
        padding: 1.5rem;
        background-color: var(--card-bg);
        border-top: 1px solid var(--border-color);
    }
    
    .message-bubble {
        max-width: 70%;
        display: flex;
        gap: 1rem;
        align-items: flex-end;
    }
    .message-bubble.mine {
        align-self: flex-end;
        flex-direction: row-reverse;
    }
    
    .message-content {
        padding: 1rem 1.2rem;
        border-radius: 1.2rem;
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        position: relative;
    }
    .message-bubble.mine .message-content {
        background-color: var(--accent);
        color: white;
        border-color: var(--accent);
    }
    .message-bubble.mine .message-content p {
        color: white;
    }

    .message-content.is-sticker {
        min-width: 140px;
        text-align: center;
        padding: 1rem;
    }

    .message-text {
        margin: 0;
        line-height: 1.4;
        color: var(--text-main);
        font-size: 0.95rem;
        white-space: pre-wrap;
        word-break: break-word;
    }

    .message-sticker {
        font-size: 3rem;
        line-height: 1;
        margin-top: 0.25rem;
    }

    .message-sticker-label {
        margin-top: 0.45rem;
        font-size: 0.78rem;
        color: var(--text-muted);
        font-weight: 600;
    }

    .message-bubble.mine .message-sticker-label {
        color: rgba(255,255,255,0.85);
    }
    
    .message-header {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 1rem;
        margin-bottom: 0.25rem;
    }
    .message-sender {
        font-weight: 700;
        font-size: 0.85rem;
        color: var(--secondary);
    }
    .message-bubble.mine .message-sender {
        display: none;
    }
    .message-time {
        font-size: 0.7rem;
        color: var(--text-muted);
    }
    .message-bubble.mine .message-time {
        color: rgba(255,255,255,0.8);
    }

    .chat-active-icon {
        cursor: pointer;
        transition: box-shadow 0.2s ease, opacity 0.2s ease;
    }

    .chat-active-icon.clickable {
        cursor: pointer;
    }

    .chat-active-icon.clickable:hover {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
    }

    .chat-members-modal {
        display: none;
        position: fixed;
        inset: 0;
        background-color: rgba(15, 23, 42, 0.55);
        z-index: 60;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .chat-members-modal.is-open {
        display: flex;
    }

    .chat-members-dialog {
        width: 100%;
        max-width: 440px;
        max-height: min(70vh, 520px);
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .chat-members-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border-color);
    }

    .chat-members-list {
        overflow-y: auto;
        padding: 0.5rem 1rem 1rem;
    }

    .chat-members-item {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
        padding: 0.9rem 0.25rem;
        border-bottom: 1px solid rgba(148, 163, 184, 0.16);
    }

    .chat-members-item:last-child {
        border-bottom: none;
    }

    .chat-members-name {
        font-weight: 700;
        color: var(--text-main);
    }

    .chat-members-club {
        font-size: 0.82rem;
        color: var(--text-muted);
    }

    .chat-composer {
        position: relative;
    }

    .chat-tool-button {
        width: 42px;
        height: 42px;
        border: 1px solid var(--border-color);
        background-color: var(--card-bg);
        color: var(--text-muted);
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    .chat-tool-button:hover,
    .chat-tool-button.is-active {
        color: var(--accent);
        border-color: rgba(59, 130, 246, 0.25);
        background-color: rgba(59, 130, 246, 0.08);
    }

    .chat-picker {
        position: absolute;
        left: 0;
        bottom: calc(100% + 0.75rem);
        width: min(420px, calc(100vw - 3rem));
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        padding: 1rem;
        display: none;
        z-index: 40;
    }

    .chat-picker.is-open {
        display: block;
    }

    .chat-picker-sections {
        display: grid;
        gap: 1rem;
    }

    .chat-picker-title {
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 0.55rem;
    }

    .chat-emoji-grid {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 0.5rem;
    }

    .chat-sticker-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.75rem;
    }

    .chat-emoji-item,
    .chat-sticker-item {
        border: 1px solid var(--border-color);
        background-color: rgba(0,0,0,0.01);
        border-radius: var(--radius-md);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .chat-emoji-item {
        min-height: 44px;
        font-size: 1.25rem;
    }

    .chat-sticker-item {
        align-items: center;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 0.35rem;
        min-height: 92px;
        padding: 0.75rem 0.5rem;
        color: var(--text-main);
    }

    .chat-emoji-item:hover,
    .chat-sticker-item:hover {
        border-color: rgba(59, 130, 246, 0.25);
        background-color: rgba(59, 130, 246, 0.08);
        transform: translateY(-1px);
    }

    .chat-sticker-emoji {
        font-size: 1.9rem;
        line-height: 1;
    }

    .chat-sticker-label {
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--text-muted);
    }

    .chat-context-menu {
        position: fixed;
        min-width: 210px;
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-md);
        padding: 0.4rem;
        z-index: 80;
        display: none;
    }

    .chat-context-menu.is-open {
        display: block;
    }

    .chat-context-item {
        width: 100%;
        border: none;
        background: transparent;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 0.85rem;
        border-radius: var(--radius-md);
        color: var(--text-main);
        cursor: pointer;
        text-align: left;
        font-size: 0.92rem;
        font-weight: 600;
    }

    .chat-context-item:hover {
        background-color: rgba(59, 130, 246, 0.08);
    }

    .chat-context-item svg {
        color: var(--text-muted);
        flex-shrink: 0;
    }

    .chat-context-item.disabled {
        opacity: 0.45;
        cursor: not-allowed;
        pointer-events: none;
    }

    .chat-mobile-back,
    .chat-mobile-actions {
        display: none;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 1px solid var(--border-color);
        background-color: var(--card-bg);
        color: var(--text-muted);
        cursor: pointer;
        flex-shrink: 0;
    }

    @media (max-width: 900px) {
        .chat-layout {
            height: calc(100vh - 150px);
        }

        .chat-sidebar {
            width: 290px;
        }

        .chat-messages {
            padding: 1.25rem;
        }
    }

    @media (max-width: 768px) {
        .chat-layout {
            position: relative;
            height: calc(100vh - 125px);
            min-height: 560px;
        }

        .chat-sidebar,
        .chat-main {
            width: 100%;
            min-width: 0;
        }

        .chat-layout:not(.mobile-chat-active) .chat-main {
            display: none;
        }

        .chat-layout.mobile-chat-active .chat-sidebar {
            display: none;
        }

        .chat-header,
        .chat-main-header,
        .chat-input-area {
            padding: 1rem;
        }

        .chat-main-header {
            gap: 0.75rem;
        }

        .chat-groups {
            padding: 0.75rem;
        }

        .chat-group-item {
            padding: 0.9rem;
        }

        .chat-messages {
            padding: 1rem 0.85rem;
            gap: 1rem;
        }

        .message-bubble {
            max-width: 88%;
            gap: 0.7rem;
        }

        .message-content {
            padding: 0.85rem 1rem;
        }

        .chat-picker {
            left: 0;
            right: 0;
            width: auto;
            bottom: calc(100% + 0.5rem);
            max-height: min(55vh, 420px);
            overflow-y: auto;
        }

        .chat-emoji-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .chat-sticker-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .chat-mobile-back,
        .chat-mobile-actions {
            display: inline-flex;
        }
    }

    @media (max-width: 520px) {
        .chat-layout {
            margin-top: 1rem;
            height: calc(100vh - 110px);
            min-height: 500px;
            border-radius: var(--radius-md);
        }

        .chat-header h2,
        #chatActiveName {
            font-size: 1rem !important;
        }

        .chat-header p,
        #chatActiveType {
            font-size: 0.75rem !important;
        }

        .chat-tool-button,
        .chat-mobile-back,
        .chat-mobile-actions {
            width: 38px;
            height: 38px;
        }

        .chat-input-area form {
            gap: 0.5rem !important;
        }

        #messageInput {
            padding-left: 1rem !important;
        }
    }
</style>

<div class="chat-layout">
    <!-- Sidebar -->
    <div class="chat-sidebar">
        <div class="chat-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="font-size: 1.1rem; font-weight: 700; margin: 0; color: var(--text-main);">Discussions</h2>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0; margin-top: 0.2rem;">Connect with your brothers.</p>
            </div>
            <button type="button" onclick="document.getElementById('newChatModal').style.display='flex';" class="btn btn-primary" style="padding: 0.5rem; border-radius: 50%; width: 36px; height: 36px;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
            </button>
        </div>
        <div class="chat-groups">
            @if($regionConversation)
                @php
                    $regionMembers = $regionConversation->getParticipants()
                        ->map(function ($participant) {
                            return [
                                'name' => $participant->fullname ?? trim(($participant->first_name ?? '') . ' ' . ($participant->last_name ?? '')),
                                'club' => $participant->club->name ?? 'No Club',
                            ];
                        })
                        ->filter(fn ($participant) => !empty($participant['name']))
                        ->values()
                        ->all();
                @endphp
                <div class="chat-group-item active" data-id="{{ $regionConversation->id }}" data-type="Regional Chat" data-name="{{ $user->region->name ?? 'Region' }}" data-is-group="1" data-is-deletable="0" data-is-renamable="0" data-members='@json($regionMembers)'>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background-color: rgba(59, 130, 246, 0.1); display: flex; align-items: center; justify-content: center; color: var(--accent);">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div>
                        <div class="chat-group-name" style="font-weight: 700; color: var(--text-main); font-size: 0.95rem;">{{ $user->region->name ?? 'Region Group' }}</div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">Regional Chat</div>
                    </div>
                </div>
            @endif

            @if($clubConversation)
                @php
                    $clubMembers = $clubConversation->getParticipants()
                        ->map(function ($participant) {
                            return [
                                'name' => $participant->fullname ?? trim(($participant->first_name ?? '') . ' ' . ($participant->last_name ?? '')),
                                'club' => $participant->club->name ?? 'No Club',
                            ];
                        })
                        ->filter(fn ($participant) => !empty($participant['name']))
                        ->values()
                        ->all();
                @endphp
                <div class="chat-group-item {{ !$regionConversation ? 'active' : '' }}" data-id="{{ $clubConversation->id }}" data-type="Club Chat" data-name="{{ $user->club->name ?? 'Club' }}" data-is-group="1" data-is-deletable="0" data-is-renamable="0" data-members='@json($clubMembers)'>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background-color: rgba(139, 92, 246, 0.1); display: flex; align-items: center; justify-content: center; color: #8b5cf6;">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <div class="chat-group-name" style="font-weight: 700; color: var(--text-main); font-size: 0.95rem;">{{ $user->club->name ?? 'Club Group' }}</div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">Club Chat</div>
                    </div>
                </div>
            @endif
            
            @if(!$regionConversation && !$clubConversation && $customConversations->isEmpty())
                <div style="text-align: center; padding: 2rem; color: var(--text-muted); font-size: 0.9rem;">
                    You do not have any active discussions. Click + to start a new chat.
                </div>
            @endif

            @foreach($customConversations as $conv)
                @php
                    $data = is_string($conv->data) ? json_decode($conv->data, true) : $conv->data;
                    $isGroup = ($data['type'] ?? '') === 'custom_group';
                    $chatName = $data['title'] ?? 'Chat';
                    $memberDetails = [];
                    if (!$isGroup) {
                        // Find the other participant's name
                        $other = $conv->getParticipants()->firstWhere('id', '!=', $user->id);
                        $chatName = $other ? $other->first_name . ' ' . $other->last_name : 'Direct Message';
                    } else {
                        $memberDetails = $conv->getParticipants()
                            ->map(function ($participant) {
                                return [
                                    'name' => $participant->fullname ?? trim(($participant->first_name ?? '') . ' ' . ($participant->last_name ?? '')),
                                    'club' => $participant->club->name ?? 'No Club',
                                ];
                            })
                            ->filter(fn ($participant) => !empty($participant['name']))
                            ->values()
                            ->all();
                    }
                @endphp
                <div class="chat-group-item {{ (!$regionConversation && !$clubConversation && $loop->first) ? 'active' : '' }}" data-id="{{ $conv->id }}" data-type="{{ $isGroup ? 'Group Chat' : 'Direct Message' }}" data-name="{{ $chatName }}" data-is-group="{{ $isGroup ? '1' : '0' }}" data-is-deletable="1" data-is-renamable="{{ $isGroup ? '1' : '0' }}" data-members='@json($memberDetails)'>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background-color: rgba(236, 72, 153, 0.1); display: flex; align-items: center; justify-content: center; color: #ec4899;">
                        @if($isGroup)
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        @else
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        @endif
                    </div>
                    <div>
                        <div class="chat-group-name" style="font-weight: 700; color: var(--text-main); font-size: 0.95rem;">{{ $chatName }}</div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $isGroup ? 'Group Chat' : 'Direct Message' }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    
    <!-- Main Chat Area -->
    <div class="chat-main" id="chatMainBox">
        @if($regionConversation || $clubConversation || $customConversations->isNotEmpty())
            <div class="chat-main-header">
                <button type="button" id="chatMobileBack" class="chat-mobile-back" title="Back to chats">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"></path></svg>
                </button>
                <div id="chatActiveIcon" class="chat-active-icon" style="width: 40px; height: 40px; border-radius: 50%; background-color: rgba(59, 130, 246, 0.1); display: flex; align-items: center; justify-content: center; color: var(--accent);">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div style="flex: 1; min-width: 0;">
                    <h3 id="chatActiveName" style="font-size: 1.1rem; font-weight: 700; margin: 0; color: var(--text-main);">Loading...</h3>
                    <span id="chatActiveType" style="font-size: 0.8rem; color: var(--text-muted); font-weight: 500;">Loading...</span>
                </div>
                <button type="button" id="chatMobileActions" class="chat-mobile-actions" title="Conversation actions">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5"></circle><circle cx="12" cy="12" r="1.5"></circle><circle cx="12" cy="19" r="1.5"></circle></svg>
                </button>
            </div>
            
            <div class="chat-messages" id="chatDisplay">
                <!-- Messages JS Injected -->
            </div>
            
            <div class="chat-input-area">
                <div class="chat-composer">
                    <div id="chatPickerPanel" class="chat-picker">
                        <div class="chat-picker-sections">
                            <div>
                                <div class="chat-picker-title">Smiley</div>
                                <div class="chat-emoji-grid">
                                    <button type="button" class="chat-emoji-item" onclick="insertEmoji('😀')">😀</button>
                                    <button type="button" class="chat-emoji-item" onclick="insertEmoji('😂')">😂</button>
                                    <button type="button" class="chat-emoji-item" onclick="insertEmoji('😍')">😍</button>
                                    <button type="button" class="chat-emoji-item" onclick="insertEmoji('😎')">😎</button>
                                    <button type="button" class="chat-emoji-item" onclick="insertEmoji('🥳')">🥳</button>
                                    <button type="button" class="chat-emoji-item" onclick="insertEmoji('🙏')">🙏</button>
                                    <button type="button" class="chat-emoji-item" onclick="insertEmoji('🔥')">🔥</button>
                                    <button type="button" class="chat-emoji-item" onclick="insertEmoji('👍')">👍</button>
                                    <button type="button" class="chat-emoji-item" onclick="insertEmoji('❤️')">❤️</button>
                                    <button type="button" class="chat-emoji-item" onclick="insertEmoji('🎉')">🎉</button>
                                    <button type="button" class="chat-emoji-item" onclick="insertEmoji('🤝')">🤝</button>
                                    <button type="button" class="chat-emoji-item" onclick="insertEmoji('🙌')">🙌</button>
                                </div>
                            </div>
                            <div>
                                <div class="chat-picker-title">Stickers</div>
                                <div class="chat-sticker-grid">
                                    <button type="button" class="chat-sticker-item" onclick="sendSticker('🎉', 'Celebrate')"><span class="chat-sticker-emoji">🎉</span><span class="chat-sticker-label">Celebrate</span></button>
                                    <button type="button" class="chat-sticker-item" onclick="sendSticker('🙏', 'Blessed')"><span class="chat-sticker-emoji">🙏</span><span class="chat-sticker-label">Blessed</span></button>
                                    <button type="button" class="chat-sticker-item" onclick="sendSticker('🔥', 'On Fire')"><span class="chat-sticker-emoji">🔥</span><span class="chat-sticker-label">On Fire</span></button>
                                    <button type="button" class="chat-sticker-item" onclick="sendSticker('👏', 'Well Done')"><span class="chat-sticker-emoji">👏</span><span class="chat-sticker-label">Well Done</span></button>
                                    <button type="button" class="chat-sticker-item" onclick="sendSticker('💪', 'Stay Strong')"><span class="chat-sticker-emoji">💪</span><span class="chat-sticker-label">Stay Strong</span></button>
                                    <button type="button" class="chat-sticker-item" onclick="sendSticker('❤️', 'Much Love')"><span class="chat-sticker-emoji">❤️</span><span class="chat-sticker-label">Much Love</span></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form id="chatForm" onsubmit="event.preventDefault(); sendMessage();" style="display: flex; gap: 0.75rem; align-items: center;">
                        @csrf
                        <input type="hidden" id="activeConversationId" value="">
                        <button type="button" id="emojiToggleBtn" class="chat-tool-button" title="Insert smiley">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"></circle><path stroke-linecap="round" d="M8 15s1.5 2 4 2 4-2 4-2"></path><path d="M9 10h.01M15 10h.01"></path></svg>
                        </button>
                        <button type="button" id="stickerToggleBtn" class="chat-tool-button" title="Send sticker">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 3h7l5 5v11a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"></path><path d="M14 3v6h6"></path></svg>
                        </button>
                        <input type="text" id="messageInput" class="form-control" placeholder="Type your message..." required style="flex: 1; border-radius: 9999px; padding-left: 1.5rem; border-color: var(--border-color);">
                        <button type="submit" class="btn btn-primary" style="border-radius: 50%; width: 48px; height: 48px; padding: 0; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="transform: translateX(-1px) translateY(1px);"><path d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div style="flex: 1; display: flex; align-items: center; justify-content: center; flex-direction: column; color: var(--text-muted);">
                <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom: 1rem; opacity: 0.3;"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                <p>Select a chat from the sidebar or click + to start a new discussion.</p>
            </div>
        @endif
    </div>
</div>

<div id="chatContextMenu" class="chat-context-menu">
    <button type="button" class="chat-context-item" data-action="rename">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 113 3L7 19l-4 1 1-4 12.5-12.5z"></path></svg>
        <span>Rename</span>
    </button>
    <button type="button" class="chat-context-item" data-action="delete">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6h18"></path><path d="M8 6V4h8v2"></path><path d="M19 6l-1 14H6L5 6"></path><path d="M10 11v6M14 11v6"></path></svg>
        <span>Delete</span>
    </button>
    <button type="button" class="chat-context-item" data-action="members">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 00-3-3.87"></path><path d="M16 3.13a4 4 0 010 7.75"></path></svg>
        <span>Chat Members</span>
    </button>
</div>

<div id="chatMembersModal" class="chat-members-modal" onclick="closeMembersPopup(event)">
    <div class="chat-members-dialog" onclick="event.stopPropagation()">
        <div class="chat-members-header">
            <div>
                <div id="chatMembersTitle" style="font-size: 1rem; font-weight: 700; color: var(--text-main);">Group Members</div>
                <div id="chatMembersCount" style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.2rem;"></div>
            </div>
            <button type="button" onclick="closeMembersPopup()" style="background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 0;">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div id="chatMembersList" class="chat-members-list"></div>
    </div>
</div>

<!-- New Chat Modal -->
<div id="newChatModal" style="display: none; position: fixed; inset: 0; background-color: rgba(0, 0, 0, 0.5); z-index: 50; align-items: center; justify-content: center; padding: 1rem;">
    <div class="card" style="width: 100%; max-width: 500px; background-color: var(--card-bg);">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2 class="card-title" style="margin: 0;">Start a New Chat</h2>
            <button type="button" onclick="document.getElementById('newChatModal').style.display='none';" style="background: none; border: none; color: var(--text-muted); cursor: pointer;">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="card-body">
            <form id="newChatForm" onsubmit="event.preventDefault(); createCustomChat();">
                @csrf
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Chat Type</label>
                    <select id="newChatType" class="form-control" style="width: 100%; padding: 0.75rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);" onchange="toggleGroupFields()">
                        <option value="p2p">Direct Message (1-on-1)</option>
                        <option value="custom_group">Group Chat (Multiple)</option>
                    </select>
                </div>

                <div id="groupNameField" class="form-group" style="margin-bottom: 1.5rem; display: none;">
                    <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Group Name</label>
                    <input type="text" id="newChatGroupName" class="form-control" style="width: 100%; padding: 0.75rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);" placeholder="Enter group name...">
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Select Members (Hold CTRL to select multiple for groups)</label>
                    <select id="newChatUsers" class="form-control" style="width: 100%;" required>
                        <option></option>
                        @foreach($allUsers as $u)
                            <option value="{{ $u->id }}" data-avatar="{{ $u->profile_photo ? asset('storage/' . $u->profile_photo) : asset('images/default-avatar.svg') }}" data-club="{{ $u->club->name ?? 'No Club' }}">
                                {{ $u->fullname }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Create Chat</button>
            </form>
        </div>
    </div>
</div>

<script>
    let pollInterval;
    let currentUserId = {{ auth()->id() }};
    let activeConversationId = '';
    let activeConversationMembers = [];
    let activeConversationIsGroup = false;
    let activeConversationName = '';
    let activeConversationType = '';
    let contextConversationItem = null;

    document.querySelectorAll('.chat-group-item').forEach(item => {
        item.addEventListener('click', function () {
            hideContextMenu();
            document.querySelectorAll('.chat-group-item').forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            loadConversation(this);
        });

        item.addEventListener('contextmenu', function (event) {
            event.preventDefault();
            openContextMenu(event, this);
        });
    });

    document.getElementById('chatActiveIcon')?.addEventListener('click', function () {
        if (!activeConversationIsGroup || activeConversationMembers.length === 0) {
            return;
        }

        openMembersPopup(activeConversationMembers, activeConversationName);
    });

    document.getElementById('emojiToggleBtn')?.addEventListener('click', function (event) {
        event.stopPropagation();
        toggleChatPicker('emoji');
    });

    document.getElementById('stickerToggleBtn')?.addEventListener('click', function (event) {
        event.stopPropagation();
        toggleChatPicker('sticker');
    });

    document.getElementById('chatMobileBack')?.addEventListener('click', function () {
        closeChatPicker();
        hideContextMenu();
        document.querySelector('.chat-layout')?.classList.remove('mobile-chat-active');
    });

    document.getElementById('chatMobileActions')?.addEventListener('click', function (event) {
        event.stopPropagation();
        let activeItem = document.querySelector('.chat-group-item.active');
        if (!activeItem) {
            return;
        }

        let rect = this.getBoundingClientRect();
        openContextMenuAtPosition(rect.right - 4, rect.bottom + 8, activeItem);
    });

    document.getElementById('chatContextMenu')?.addEventListener('click', function (event) {
        let actionButton = event.target.closest('.chat-context-item');
        if (!actionButton || actionButton.classList.contains('disabled') || !contextConversationItem) {
            return;
        }

        let action = actionButton.getAttribute('data-action');
        let targetItem = contextConversationItem;
        hideContextMenu();

        if (action === 'rename') {
            openRenameDialog(targetItem);
        } else if (action === 'delete') {
            confirmDeleteConversation(targetItem);
        } else if (action === 'members') {
            let context = getConversationContext(targetItem);
            openMembersPopup(context.members, context.name);
        }
    });

    document.addEventListener('click', function (event) {
        if (!event.target.closest('#chatContextMenu')) {
            hideContextMenu();
        }

        if (!event.target.closest('.chat-composer')) {
            closeChatPicker();
        }
    });

    document.querySelector('.chat-groups')?.addEventListener('scroll', hideContextMenu);
    window.addEventListener('resize', function () {
        hideContextMenu();
        syncMobileChatLayout(!!activeConversationId);
    });

    // Auto load first active
    let activeItem = document.querySelector('.chat-group-item.active');
    if (activeItem) {
        loadConversation(activeItem);
    }

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (char) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            }[char];
        });
    }

    function escapeAttribute(value) {
        return escapeHtml(value).replace(/"/g, '&quot;');
    }

    function getConversationMembers(item) {
        try {
            let members = JSON.parse(item.getAttribute('data-members') || '[]');
            return Array.isArray(members) ? members.filter(member => member && member.name) : [];
        } catch (error) {
            return [];
        }
    }

    function getConversationContext(item) {
        return {
            id: item.getAttribute('data-id'),
            name: item.getAttribute('data-name') || 'Conversation',
            type: item.getAttribute('data-type') || 'Conversation',
            isGroup: item.getAttribute('data-is-group') === '1',
            isDeletable: item.getAttribute('data-is-deletable') === '1',
            isRenamable: item.getAttribute('data-is-renamable') === '1',
            members: getConversationMembers(item),
        };
    }

    function updateChatActiveIcon() {
        let icon = document.getElementById('chatActiveIcon');

        if (!icon) {
            return;
        }

        icon.classList.toggle('clickable', activeConversationIsGroup && activeConversationMembers.length > 0);
        icon.title = activeConversationIsGroup && activeConversationMembers.length > 0
            ? 'View group members'
            : '';
    }

    function setConversationDisplayName(item, newName) {
        item.setAttribute('data-name', newName);

        let title = item.querySelector('.chat-group-name');
        if (title) {
            title.textContent = newName;
        }

        if (item.classList.contains('active')) {
            activeConversationName = newName;
            document.getElementById('chatActiveName').innerText = newName;
        }
    }

    function openMembersPopup(members = activeConversationMembers, titleText = activeConversationName) {
        let modal = document.getElementById('chatMembersModal');
        let list = document.getElementById('chatMembersList');
        let title = document.getElementById('chatMembersTitle');
        let count = document.getElementById('chatMembersCount');

        title.textContent = titleText || 'Group Members';
        count.textContent = `${members.length} member${members.length === 1 ? '' : 's'}`;
        list.innerHTML = members.map(member => `
            <div class="chat-members-item">
                <div class="chat-members-name">${escapeHtml(member.name)}</div>
                <div class="chat-members-club">${escapeHtml(member.club || 'No Club')}</div>
            </div>
        `).join('');

        modal.classList.add('is-open');
    }

    function closeMembersPopup(event) {
        if (event && event.target !== event.currentTarget) {
            return;
        }

        document.getElementById('chatMembersModal').classList.remove('is-open');
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            hideContextMenu();
            closeChatPicker();
            closeMembersPopup();
        }
    });

    function toggleChatPicker(mode) {
        let panel = document.getElementById('chatPickerPanel');
        let emojiButton = document.getElementById('emojiToggleBtn');
        let stickerButton = document.getElementById('stickerToggleBtn');

        if (!panel) {
            return;
        }

        let shouldOpen = !panel.classList.contains('is-open') || panel.getAttribute('data-mode') !== mode;
        panel.classList.toggle('is-open', shouldOpen);
        panel.setAttribute('data-mode', shouldOpen ? mode : '');
        emojiButton?.classList.toggle('is-active', shouldOpen && mode === 'emoji');
        stickerButton?.classList.toggle('is-active', shouldOpen && mode === 'sticker');
    }

    function closeChatPicker() {
        let panel = document.getElementById('chatPickerPanel');
        panel?.classList.remove('is-open');
        panel?.setAttribute('data-mode', '');
        document.getElementById('emojiToggleBtn')?.classList.remove('is-active');
        document.getElementById('stickerToggleBtn')?.classList.remove('is-active');
    }

    function insertEmoji(emoji) {
        let input = document.getElementById('messageInput');
        if (!input) {
            return;
        }

        input.value += emoji;
        input.focus();
    }

    function isMobileChatViewport() {
        return window.innerWidth <= 768;
    }

    function syncMobileChatLayout(showConversation) {
        let layout = document.querySelector('.chat-layout');
        if (!layout) {
            return;
        }

        if (!isMobileChatViewport()) {
            layout.classList.remove('mobile-chat-active');
            return;
        }

        layout.classList.toggle('mobile-chat-active', !!showConversation);
    }

    function openContextMenuAtPosition(clientX, clientY, item) {
        let menu = document.getElementById('chatContextMenu');
        if (!menu) {
            return;
        }

        contextConversationItem = item;
        updateContextMenu(item);
        menu.classList.add('is-open');

        requestAnimationFrame(() => {
            let maxLeft = window.innerWidth - menu.offsetWidth - 8;
            let maxTop = window.innerHeight - menu.offsetHeight - 8;
            let left = Math.min(clientX, Math.max(maxLeft, 8));
            let top = Math.min(clientY, Math.max(maxTop, 8));

            menu.style.left = `${Math.max(8, left)}px`;
            menu.style.top = `${Math.max(8, top)}px`;
        });
    }

    function openContextMenu(event, item) {
        openContextMenuAtPosition(event.clientX, event.clientY, item);
    }

    function hideContextMenu() {
        let menu = document.getElementById('chatContextMenu');
        if (!menu) {
            return;
        }

        menu.classList.remove('is-open');
        contextConversationItem = null;
    }

    function updateContextMenu(item) {
        let context = getConversationContext(item);
        let menu = document.getElementById('chatContextMenu');
        if (!menu) {
            return;
        }

        menu.querySelector('[data-action="rename"]')?.classList.toggle('disabled', !context.isRenamable);
        menu.querySelector('[data-action="delete"]')?.classList.toggle('disabled', !context.isDeletable);
        menu.querySelector('[data-action="members"]')?.classList.toggle('disabled', !context.isGroup || context.members.length === 0);
    }

    function openRenameDialog(item) {
        let context = getConversationContext(item);
        if (!context.isRenamable) {
            return;
        }

        $.confirm({
            title: 'Rename Group Chat',
            content: `
                <div style="margin-top: 0.5rem;">
                    <input type="text" id="renameConversationInput" class="form-control" value="${escapeAttribute(context.name)}" maxlength="255" placeholder="Enter new group chat name">
                </div>
            `,
            boxWidth: '420px',
            useBootstrap: false,
            buttons: {
                cancel: {
                    text: 'Cancel'
                },
                save: {
                    text: 'Save',
                    btnClass: 'btn-blue',
                    action: function () {
                        let dialog = this;
                        let newName = dialog.$content.find('#renameConversationInput').val().trim();

                        if (!newName) {
                            $.alert('Group chat name is required.');
                            return false;
                        }

                        dialog.showLoading(true);
                        dialog.buttons.save.disable();

                        renameConversation(item, newName)
                            .then((json) => {
                                setConversationDisplayName(item, json.title);
                                dialog.close();
                            })
                            .catch((error) => {
                                dialog.hideLoading(true);
                                dialog.buttons.save.enable();
                                $.alert(error.message || 'Unable to rename group chat.');
                            });

                        return false;
                    }
                }
            },
            onContentReady: function () {
                let dialog = this;
                let input = dialog.$content.find('#renameConversationInput');
                input.trigger('focus');
                input[0]?.select();
                input.on('keypress', function (event) {
                    if (event.which === 13) {
                        event.preventDefault();
                        dialog.$$save.trigger('click');
                    }
                });
            }
        });
    }

    async function renameConversation(item, newName) {
        let response = await fetch(`{{ url('/chat/custom-conversations') }}/${item.getAttribute('data-id')}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ title: newName })
        });

        let json = await response.json();
        if (!response.ok || !json.success) {
            throw new Error(json.message || 'Unable to rename group chat.');
        }

        return json;
    }

    function confirmDeleteConversation(item) {
        let context = getConversationContext(item);
        if (!context.isDeletable) {
            return;
        }

        $.confirm({
            title: 'Delete Conversation',
            content: `Delete this ${context.type.toLowerCase()}? This will permanently remove the whole conversation and all messages for everyone.`,
            type: 'red',
            boxWidth: '420px',
            useBootstrap: false,
            buttons: {
                cancel: {
                    text: 'Cancel'
                },
                delete: {
                    text: 'Delete',
                    btnClass: 'btn-red',
                    action: function () {
                        let dialog = this;
                        dialog.showLoading(true);
                        dialog.buttons.delete.disable();

                        deleteConversation(item)
                            .then(() => {
                                if (pollInterval) {
                                    clearInterval(pollInterval);
                                }

                                closeMembersPopup();
                                window.location.reload();
                            })
                            .catch((error) => {
                                dialog.hideLoading(true);
                                dialog.buttons.delete.enable();
                                $.alert(error.message || 'Unable to delete conversation.');
                            });

                        return false;
                    }
                }
            }
        });
    }

    async function deleteConversation(item) {
        let response = await fetch(`{{ url('/chat/custom-conversations') }}/${item.getAttribute('data-id')}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });

        let json = await response.json();
        if (!response.ok || !json.success) {
            throw new Error(json.message || 'Unable to delete conversation.');
        }

        return json;
    }

    function loadConversation(item) {
        hideContextMenu();
        let convoId = item.getAttribute('data-id');
        let name = item.getAttribute('data-name');
        let type = item.getAttribute('data-type');
        
        document.getElementById('activeConversationId').value = convoId;
        document.getElementById('chatActiveName').innerText = name;
        document.getElementById('chatActiveType').innerText = type;
        document.getElementById('chatDisplay').innerHTML = '<div style="text-align: center; color: var(--text-muted); margin-top: 2rem;">Loading messages...</div>';
        activeConversationId = convoId;
        activeConversationName = name;
        activeConversationType = type;
        activeConversationIsGroup = item.getAttribute('data-is-group') === '1';
        activeConversationMembers = getConversationMembers(item);
        updateChatActiveIcon();
        syncMobileChatLayout(true);

        fetchMessages(convoId);

        if(pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(() => { fetchMessages(convoId, false) }, 3000);
    }

    async function fetchMessages(convoId, scroll = true) {
        try {
            let res = await fetch('{{ url("/chat/conversations") }}/' + convoId + '/messages');
            let json = await res.json();
            if(json.messages) {
                renderMessages(json.messages, scroll);
            }
        } catch(e) {}
    }

    function renderMessages(messages, scroll) {
        let display = document.getElementById('chatDisplay');
        
        if (messages.length === 0) {
            display.innerHTML = '<div style="text-align: center; color: var(--text-muted); margin-top: 2rem;">No messages here yet. Say hello!</div>';
            return;
        }

        let html = '';
        messages.forEach(msg => {
            let isMine = msg.sender.id == currentUserId;
            let isSticker = msg.type === 'sticker';
            let stickerValue = escapeHtml((msg.data && msg.data.sticker) || msg.body || '🙂');
            let stickerLabel = escapeHtml((msg.data && msg.data.label) || 'Sticker');
            
            html += `
                <div class="message-bubble ${isMine ? 'mine' : ''}">
                    <!-- Avatar omitted conditionally for mine via CSS display:none -->
                    ${!isMine ? `<img src="${msg.sender.avatar}" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; flex-shrink: 0;">` : ''}
                    
                    <div class="message-content ${isSticker ? 'is-sticker' : ''}">
                        <div class="message-header">
                            <span class="message-sender">${escapeHtml(msg.sender.name)}</span>
                            <span class="message-time">${msg.created_at}</span>
                        </div>
                        ${isSticker
                            ? `<div class="message-sticker">${stickerValue}</div><div class="message-sticker-label">${stickerLabel}</div>`
                            : `<p class="message-text">${escapeHtml(msg.body)}</p>`}
                    </div>
                </div>
            `;
        });

        // check if scroll is needed (user explicitly loaded or sent msg)
        let isAtBottom = display.scrollHeight - display.scrollTop <= display.clientHeight + 100;
        
        display.innerHTML = html;

        if (scroll || isAtBottom) {
            display.scrollTop = display.scrollHeight;
        }
    }

    async function sendMessage() {
        let input = document.getElementById('messageInput');
        let text = input.value.trim();
        if (!text) return;
        
        input.value = ''; // clear instantly

        await sendMessagePayload({
            message: text,
            message_type: 'text'
        });
    }

    async function sendSticker(sticker, label) {
        if (!document.getElementById('activeConversationId').value) {
            return;
        }

        closeChatPicker();
        await sendMessagePayload({
            message: sticker,
            message_type: 'sticker',
            sticker: sticker,
            sticker_label: label
        });
    }

    async function sendMessagePayload(payload) {
        let convoId = document.getElementById('activeConversationId').value;
        if (!convoId) {
            return;
        }

        try {
            let res = await fetch('{{ url("/chat/conversations") }}/' + convoId + '/messages', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            });
            let json = await res.json();
            
            if (json.success) {
                fetchMessages(convoId, true);
            } else {
                $.alert(json.message || 'Unable to send message.');
            }
        } catch (e) {
            $.alert('Unable to send message.');
        }
    }

    function toggleGroupFields() {
        let type = document.getElementById('newChatType').value;
        document.getElementById('groupNameField').style.display = type === 'custom_group' ? 'block' : 'none';
        
        if ($('#newChatUsers').hasClass("select2-hidden-accessible")) {
            $('#newChatUsers').select2('destroy');
        }

        if (type === 'p2p') {
            $('#newChatUsers').removeAttr('multiple');
            // reset selection to only first selected if many were selected
            let select = document.getElementById('newChatUsers');
            let firstSelected = select.value;
            for(let i=0; i<select.options.length; i++) {
                if(select.options[i].value !== firstSelected) {
                    select.options[i].selected = false;
                }
            }
        } else {
            $('#newChatUsers').attr('multiple', 'multiple');
        }

        initSelect2();
    }

    function initSelect2() {
        $('#newChatUsers').select2({
            placeholder: "Select user(s)...",
            templateResult: formatUser,
            templateSelection: formatUserSelection,
            width: '100%'
        });
    }

    function formatUser (user) {
        if (!user.id) { return user.text; }
        var avatar = $(user.element).data('avatar');
        var club = $(user.element).data('club');
        var $user = $(
            '<div style="display: flex; align-items: center; gap: 10px; padding: 4px;">' +
            '<img src="' + avatar + '" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; flex-shrink: 0;" />' +
            '<div><div style="font-weight: 600; color: #1e293b; font-size: 0.95rem;">' + user.text + '</div><div style="font-size: 0.75rem; color: #64748b;">' + club + '</div></div>' +
            '</div>'
        );
        return $user;
    };

    function formatUserSelection (user) {
        if (!user.id) { return user.text; }
        return user.text;
    }

    // Initialize on load
    $(document).ready(function() {
        toggleGroupFields(); // This sets up the correct mode and initializes Select2
    });

    async function createCustomChat() {
        let type = document.getElementById('newChatType').value;
        let title = document.getElementById('newChatGroupName').value;
        
        // Get selected users
        let select = document.getElementById('newChatUsers');
        let userIds = Array.from(select.selectedOptions).map(option => option.value);

        if (userIds.length === 0) {
            alert('Please select at least one member.');
            return;
        }

        try {
            let res = await fetch('{{ route("chat.custom.create") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    type: type,
                    title: title,
                    user_ids: userIds
                })
            });
            
            let json = await res.json();
            
            if (json.success) {
                // Reload page to show new chat in sidebar
                window.location.reload();
            } else {
                alert(json.message || 'Error creating chat');
            }
        } catch(e) {
            console.error(e);
            alert('An error occurred.');
        }
    }
</script>
@endsection
