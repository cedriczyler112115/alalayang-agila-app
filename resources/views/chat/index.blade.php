@extends('layouts.app')

@section('title', 'Group Chat - Caragados EC')

@section('content')
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
</style>

<div class="chat-layout">
    <!-- Sidebar -->
    <div class="chat-sidebar">
        <div class="chat-header">
            <h2 style="font-size: 1.1rem; font-weight: 700; margin: 0; color: var(--text-main);">Discussions</h2>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0; margin-top: 0.2rem;">Connect with your brothers.</p>
        </div>
        <div class="chat-groups">
            @if($regionConversation)
                <div class="chat-group-item active" data-id="{{ $regionConversation->id }}" data-type="Regional Chat" data-name="{{ $user->region->name ?? 'Region' }}">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background-color: rgba(59, 130, 246, 0.1); display: flex; align-items: center; justify-content: center; color: var(--accent);">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div>
                        <div style="font-weight: 700; color: var(--text-main); font-size: 0.95rem;">{{ $user->region->name ?? 'Region Group' }}</div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">Regional Chat</div>
                    </div>
                </div>
            @endif

            @if($clubConversation)
                <div class="chat-group-item {{ !$regionConversation ? 'active' : '' }}" data-id="{{ $clubConversation->id }}" data-type="Club Chat" data-name="{{ $user->club->name ?? 'Club' }}">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background-color: rgba(139, 92, 246, 0.1); display: flex; align-items: center; justify-content: center; color: #8b5cf6;">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <div style="font-weight: 700; color: var(--text-main); font-size: 0.95rem;">{{ $user->club->name ?? 'Club Group' }}</div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">Club Chat</div>
                    </div>
                </div>
            @endif
            
            @if(!$regionConversation && !$clubConversation)
                <div style="text-align: center; padding: 2rem; color: var(--text-muted); font-size: 0.9rem;">
                    You are not assigned to a Region or Club yet to access group chats.
                </div>
            @endif
        </div>
    </div>
    
    <!-- Main Chat Area -->
    <div class="chat-main" id="chatMainBox">
        @if($regionConversation || $clubConversation)
            <div class="chat-main-header">
                <div id="chatActiveIcon" style="width: 40px; height: 40px; border-radius: 50%; background-color: rgba(59, 130, 246, 0.1); display: flex; align-items: center; justify-content: center; color: var(--accent);">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div>
                    <h3 id="chatActiveName" style="font-size: 1.1rem; font-weight: 700; margin: 0; color: var(--text-main);">Loading...</h3>
                    <span id="chatActiveType" style="font-size: 0.8rem; color: var(--text-muted); font-weight: 500;">Loading...</span>
                </div>
            </div>
            
            <div class="chat-messages" id="chatDisplay">
                <!-- Messages JS Injected -->
            </div>
            
            <div class="chat-input-area">
                <form id="chatForm" onsubmit="event.preventDefault(); sendMessage();" style="display: flex; gap: 1rem;">
                    @csrf
                    <input type="hidden" id="activeConversationId" value="">
                    <input type="text" id="messageInput" class="form-control" placeholder="Type your message..." required style="flex: 1; border-radius: 9999px; padding-left: 1.5rem; border-color: var(--border-color);">
                    <button type="submit" class="btn btn-primary" style="border-radius: 50%; width: 48px; height: 48px; padding: 0; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="transform: translateX(-1px) translateY(1px);"><path d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    </button>
                </form>
            </div>
        @else
            <div style="flex: 1; display: flex; align-items: center; justify-content: center; flex-direction: column; color: var(--text-muted);">
                <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom: 1rem; opacity: 0.3;"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                <p>Your access is restricted until you join a Region or Eagle Club.</p>
            </div>
        @endif
    </div>
</div>

<script>
    let pollInterval;
    let currentUserId = {{ auth()->id() }};
    
    document.querySelectorAll('.chat-group-item').forEach(item => {
        item.addEventListener('click', function() {
            document.querySelectorAll('.chat-group-item').forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            loadConversation(this);
        });
    });

    // Auto load first active
    let activeItem = document.querySelector('.chat-group-item.active');
    if (activeItem) {
        loadConversation(activeItem);
    }

    function loadConversation(item) {
        let convoId = item.getAttribute('data-id');
        let name = item.getAttribute('data-name');
        let type = item.getAttribute('data-type');
        
        document.getElementById('activeConversationId').value = convoId;
        document.getElementById('chatActiveName').innerText = name;
        document.getElementById('chatActiveType').innerText = type;
        document.getElementById('chatDisplay').innerHTML = '<div style="text-align: center; color: var(--text-muted); margin-top: 2rem;">Loading messages...</div>';

        fetchMessages(convoId);

        if(pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(() => { fetchMessages(convoId, false) }, 3000);
    }

    async function fetchMessages(convoId, scroll = true) {
        try {
            let res = await fetch('{{ url("/chat") }}/' + convoId + '/messages');
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
            
            html += `
                <div class="message-bubble ${isMine ? 'mine' : ''}">
                    <!-- Avatar omitted conditionally for mine via CSS display:none -->
                    ${!isMine ? `<img src="${msg.sender.avatar}" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; flex-shrink: 0;">` : ''}
                    
                    <div class="message-content">
                        <div class="message-header">
                            <span class="message-sender">${msg.sender.name}</span>
                            <span class="message-time">${msg.created_at}</span>
                        </div>
                        <p style="margin: 0; line-height: 1.4; color: var(--text-main); font-size: 0.95rem;">${msg.body}</p>
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
        let convoId = document.getElementById('activeConversationId').value;
        
        if (!text || !convoId) return;
        
        input.value = ''; // clear instantly
        
        try {
            let res = await fetch('{{ url("/chat") }}/' + convoId + '/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message: text })
            });
            let json = await res.json();
            
            if (json.success) {
                fetchMessages(convoId, true);
            }
        } catch(e) {}
    }
</script>
@endsection
