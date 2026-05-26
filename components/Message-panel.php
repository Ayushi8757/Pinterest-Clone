
<!-- ── MESSAGES PANEL ── -->
<div id="msgPanel" class="msg-panel">
  <div class="msg-header">
    <span class="msg-title">Messages</span>
    <div style="display:flex;gap:6px;align-items:center;">
      <!-- Unread count badge -->
      <span id="headerUnreadBadge" class="unread-badge" style="display:none;"></span>
      <button class="msg-close" onclick="toggleMessages()" title="Close">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
  </div>

  <!-- New message button -->
  <div class="msg-new-wrap">
    <button class="msg-new-btn" onclick="openNewMessage()">
      <span class="msg-new-icon"><i class="bi bi-pencil-square"></i></span>
      New message
    </button>
  </div>

  <div class="msg-section-label">Recent</div>

  <!-- Conversation list — populated dynamically -->
  <div class="msg-list" id="msgList">
    <div class="msg-loading">
      <div class="msg-spinner"></div>
      <span>Loading chats...</span>
    </div>
  </div>
</div>

<!-- ── CONVERSATION VIEW ── -->
<div id="convView" class="conv-view">
  <div class="conv-header">
    <button class="conv-back" onclick="closeConv()"><i class="bi bi-arrow-left"></i></button>
    <div id="convAvatar" class="conv-avatar conv-avatar-default"></div>
    <div style="flex:1;min-width:0;">
      <div id="convName" class="conv-name">—</div>
      <div id="convStatus" class="conv-status">—</div>
    </div>
    <button class="conv-close" onclick="toggleMessages()"><i class="bi bi-x-lg"></i></button>
  </div>
  <div class="conv-messages" id="convMessages"></div>
  <div id="typingIndicator" class="typing-indicator" style="display:none;">
    <span></span><span></span><span></span>
  </div>
  <div class="conv-input-row">
    <input type="text" class="conv-input" placeholder="Message…" id="convInput"
           oninput="handleTyping()"
           onkeydown="if(event.key==='Enter') sendMsg()"/>
    <button class="conv-send" onclick="sendMsg()"><i class="bi bi-send-fill"></i></button>
  </div>
</div>

<!-- ── NEW MESSAGE MODAL ── -->
<div id="newMsgOverlay" class="new-msg-overlay" onclick="closeNewMessage()">
  <div class="new-msg-box" onclick="event.stopPropagation()">
    <div class="new-msg-header">
      <span class="new-msg-title">New message</span>
      <button class="msg-close" onclick="closeNewMessage()"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="new-msg-search-wrap">
      <i class="bi bi-search"></i>
      <input type="text" id="userSearchInput" placeholder="Search name or email..."
             class="new-msg-search" oninput="searchUsers(this.value)"/>
    </div>
    <div id="userSearchResults" class="user-search-results">
      <!-- Users loaded dynamically -->
    </div>
  </div>
</div>

<style>
 
  .msg-panel {
    position: fixed; 
    top: 80px; 
    bottom: 0; 
    left: -420px;
    width: 400px; background: #fff; z-index: 300;
    border-right: 1px solid #e8e8e8;
    display: flex; flex-direction: column;
    transition: left .28s cubic-bezier(.4,0,.2,1);
    box-shadow: 4px 0 24px rgba(0,0,0,.08);
  }
  .msg-panel.open { 
    left: var(--side-w, 60px); 
  }
  body.msg-open .main-content,
  body.msg-open .main {
    margin-left: calc(var(--side-w, 60px) + 400px) !important;
    transition: margin-left .28s cubic-bezier(.4,0,.2,1);
  }

  /* ── Header ── */
  .msg-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 20px 20px 12px; border-bottom: 1px solid #f0f0f0;
  }
  .msg-title { font-weight: 700; font-size: 1.15rem; }
  .msg-close {
    width: 36px; height: 36px; border-radius: 50%;
    border: none; background: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem; color: #111; transition: background .15s;
  }
  .msg-close:hover { background: #f0eeeb; }

  /* Unread badge */
  .unread-badge {
    background: #e60023; color: #fff; border-radius: 12px;
    font-size: .72rem; font-weight: 700;
    padding: 2px 7px; min-width: 20px; text-align: center;
    animation: badgePop .25s ease;
  }
  @keyframes badgePop {
    0% { transform: scale(0.5); } 80% { transform: scale(1.15); } 100% { transform: scale(1); }
  }

  /* Sidebar icon badge */
  #msgSideBtn { position: relative; }
  #msgSideBadge {
    position: absolute; top: 2px; right: 2px;
    background: #e60023; color: #fff; border-radius: 50%;
    width: 18px; height: 18px; font-size: .68rem; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    border: 2px solid #fff;
    animation: badgePop .25s ease;
  }

  /* ── New message button ── */
  .msg-new-wrap { padding: 14px 16px 10px; }
  .msg-new-btn {
    display: flex; align-items: center; gap: 12px;
    width: 100%; border: none; background: none; cursor: pointer;
    padding: 8px 4px; font-size: .95rem; font-weight: 600;
    font-family: 'DM Sans', sans-serif; color: #111;
    border-radius: 12px; transition: background .15s;
  }
  .msg-new-btn:hover { background: #f8f7f5; }
  .msg-new-icon {
    width: 44px; height: 44px; border-radius: 50%;
    background: #e60023; color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; flex-shrink: 0;
  }

  .msg-section-label { font-size: .85rem; font-weight: 500; color: #767676; padding: 4px 20px 8px; }

  /* ── Conversation list ── */
  .msg-list { flex: 1; overflow-y: auto; padding: 0 8px; }
  .msg-item {
    display: flex; align-items: center; gap: 12px;
    padding: 10px 12px; border-radius: 12px; cursor: pointer;
    transition: background .15s; position: relative;
  }
  .msg-item:hover { background: #f8f7f5; }
  .msg-item:hover .msg-item-close { opacity: 1; }
  .msg-item:hover .msg-time { display: none; }

  /* ── Avatars ── */
  .msg-avatar {
    width: 48px; height: 48px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; flex-shrink: 0; position: relative;
    overflow: hidden; background: #e8e8e8;
  }
  .msg-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
  .msg-avatar-red    { background: #e60023; color: #fff; }
  .msg-avatar-letter { background: #e60023; color: #fff; font-size: 1.2rem; font-weight: 700; }

  /* Online dot */
  .online-dot {
    position: absolute; bottom: 2px; right: 2px;
    width: 11px; height: 11px; border-radius: 50%;
    background: #31a24c; border: 2px solid #fff;
  }

  .msg-body { flex: 1; min-width: 0; }
  .msg-name { font-weight: 600; font-size: .92rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .msg-preview { font-size: .82rem; color: #767676; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .msg-preview.unread-preview { color: #111; font-weight: 600; }

  .msg-meta { display: flex; align-items: center; flex-direction: column; gap: 4px; flex-shrink: 0; }
  .msg-time { font-size: .78rem; color: #767676; }
  .msg-item-unread {
    width: 10px; height: 10px; border-radius: 50%;
    background: #e60023; flex-shrink: 0;
  }
  .msg-item-close {
    width: 24px; height: 24px; border-radius: 50%; border: none;
    background: #e8e8e8; cursor: pointer; font-size: .75rem;
    display: flex; align-items: center; justify-content: center;
    opacity: 0; transition: opacity .15s;
  }
  .msg-item-close:hover { background: #d0d0d0; }

  /* ── Conversation view ── */
  .conv-view {
    position: fixed; 
    top: 80px; bottom: 0; left: -420px;
    width: 400px; background: #fff; z-index: 301;
    border-right: 1px solid #e8e8e8;
    display: flex; flex-direction: column;
    transition: left .25s cubic-bezier(.4,0,.2,1);
    box-shadow: 4px 0 24px rgba(0,0,0,.08);
  }
  .conv-view.open { left: var(--side-w, 60px); }

  .conv-header {
    display: flex; align-items: center; gap: 10px;
    padding: 16px 16px 12px; border-bottom: 1px solid #f0f0f0;
  }
  .conv-back {
    width: 36px; height: 36px; border-radius: 50%; border: none;
    background: none; cursor: pointer; display: flex; align-items: center;
    justify-content: center; font-size: 1rem; color: #111; transition: background .15s;
  }
  .conv-back:hover { background: #f0eeeb; }
  .conv-avatar {
    width: 40px; height: 40px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; flex-shrink: 0; background: #e8e8e8; overflow: hidden;
  }
  .conv-avatar img { width: 100%; height: 100%; object-fit: cover; }
  .conv-avatar-default { background: #e60023; color: #fff; }
  .conv-name { font-weight: 700; font-size: .95rem; }
  .conv-status { font-size: .78rem; color: #767676; }
  .conv-status.online { color: #31a24c; }
  .conv-close {
    width: 36px; height: 36px; border-radius: 50%; border: none; background: none;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    font-size: 1rem; transition: background .15s; flex-shrink: 0;
  }
  .conv-close:hover { background: #f0eeeb; }

  .conv-messages {
    flex: 1; overflow-y: auto; padding: 16px;
    display: flex; flex-direction: column; gap: 10px;
  }
  .conv-msg { display: flex; flex-direction: column; }
  .conv-msg-in  { align-items: flex-start; }
  .conv-msg-out { align-items: flex-end; }
  .conv-bubble {
    background: #f0eeeb; border-radius: 18px; padding: 10px 14px;
    font-size: .9rem; max-width: 80%; word-wrap: break-word;
  }
  .conv-msg-out .conv-bubble { background: #e60023; color: #fff; }
  .conv-time { font-size: .74rem; color: #767676; margin-top: 4px; padding: 0 4px; }

  /* ── Typing indicator ── */
  .typing-indicator {
    padding: 0 16px 8px; display: flex; gap: 4px; align-items: center;
  }
  .typing-indicator span {
    width: 7px; height: 7px; background: #767676; border-radius: 50%;
    animation: typingBounce 1.2s infinite;
  }
  .typing-indicator span:nth-child(2) { animation-delay: .2s; }
  .typing-indicator span:nth-child(3) { animation-delay: .4s; }
  @keyframes typingBounce {
    0%, 60%, 100% { transform: translateY(0); }
    30%           { transform: translateY(-6px); }
  }

  .conv-input-row {
    display: flex; gap: 8px; padding: 12px 16px;
    border-top: 1px solid #f0f0f0;
  }
  .conv-input {
    flex: 1; border: 2px solid #e8e8e8; border-radius: 24px;
    padding: 10px 16px; font-size: .9rem;
    font-family: 'DM Sans', sans-serif; outline: none; transition: border-color .2s;
  }
  .conv-input:focus { border-color: #e60023; }
  .conv-send {
    width: 40px; height: 40px; border-radius: 50%;
    background: #e60023; color: #fff; border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: .95rem; transition: background .15s; flex-shrink: 0;
  }
  .conv-send:hover { background: #ad081b; }

  /* ── New message overlay ── */
  .new-msg-overlay {
    position: fixed; inset: 0; z-index: 400;
    background: rgba(0,0,0,.4);
    display: flex; align-items: center; justify-content: center;
    opacity: 0; pointer-events: none; transition: opacity .2s;
  }
  .new-msg-overlay.show { opacity: 1; pointer-events: all; }
  .new-msg-box {
    background: #fff; border-radius: 20px;
    width: 100%; max-width: 440px; padding: 24px;
    transform: translateY(12px); transition: transform .22s;
    max-height: 80vh; display: flex; flex-direction: column;
  }
  .new-msg-overlay.show .new-msg-box { transform: translateY(0); }
  .new-msg-header {
    display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;
  }
  .new-msg-title { font-weight: 700; font-size: 1.1rem; }
  .new-msg-search-wrap {
    display: flex; align-items: center; gap: 10px;
    background: #f0eeeb; border-radius: 24px;
    padding: 10px 16px; margin-bottom: 12px; flex-shrink: 0;
  }
  .new-msg-search {
    border: none; background: none; outline: none;
    font-size: .9rem; font-family: 'DM Sans', sans-serif; width: 100%;
  }
  .user-search-results { overflow-y: auto; flex: 1; }
  .user-search-item {
    display: flex; align-items: center; gap: 12px;
    padding: 10px; border-radius: 12px; cursor: pointer;
    transition: background .15s;
  }
  .user-search-item:hover { background: #f8f7f5; }

  /* ── Loading spinner ── */
  .msg-loading {
    display: flex; align-items: center; justify-content: center;
    gap: 10px; padding: 30px; color: #767676; font-size: .9rem;
  }
  .msg-spinner {
    width: 20px; height: 20px; border: 2px solid #e8e8e8;
    border-top-color: #e60023; border-radius: 50%;
    animation: spin .7s linear infinite;
  }
  @keyframes spin { to { transform: rotate(360deg); } }

  /* ── Connection status bar ── */
  #wsStatus {
    font-size: .75rem; padding: 4px 12px; text-align: center; display: none;
  }
  #wsStatus.disconnected {
    display: block; background: #fff3e0; color: #e65100;
  }

  /* Sidebar button active state */
  #msgSideBtn.msg-active { background: #f0eeeb; outline: 2px solid #ddd; }
</style>


<script>

const WS_URL  = 'ws://localhost:8080';        // WebSocket server address
const API_URL = '/pinterest_project_v5/Pinterest_Final/components/chat-api.php';

const MY_USER_ID = <?php echo (int)($_SESSION['user_id'] ?? 0); ?>;
const MY_NAME = <?php echo json_encode($_SESSION['user_name'] ?? ''); ?>;

let ws               = null;
let wsReconnectTimer = null;
let msgOpen          = false;
let convOpen         = false;
let activeConvId     = null;  // currently open conversation user ID
let typingTimer      = null;
let allUsers         = [];    // cached user list for search modal

// WebSocket connection

function wsConnect() {
  try {
    ws = new WebSocket(WS_URL);
  } catch(e) {
    wsScheduleReconnect();
    return;
  }

  ws.onopen = () => {
    console.log('[WS] Connected');
    document.getElementById('wsStatus')?.classList.remove('disconnected');
    clearTimeout(wsReconnectTimer);
    // Identify ourselves to the server
    wsSend({ type: 'auth', user_id: MY_USER_ID });
  };

  ws.onmessage = (e) => {
    try {
      const data = JSON.parse(e.data);
      handleWsMessage(data);
    } catch(err) { console.error('[WS] Bad JSON', err); }
  };

  ws.onclose = () => {
    console.log('[WS] Connection closed — reconnecting...');
    document.getElementById('wsStatus')?.classList.add('disconnected');
    wsScheduleReconnect();
  };

  ws.onerror = () => { ws.close(); };
}
function wsSend(data) {
  if (ws && ws.readyState === WebSocket.OPEN) {
    ws.send(JSON.stringify(data));
  }
}
function wsScheduleReconnect() {
  clearTimeout(wsReconnectTimer);
  wsReconnectTimer = setTimeout(wsConnect, 3000);
}

//  Handle incoming server messages 
function handleWsMessage(data) {
  switch(data.type) {

    case 'new_message':
      // Show the message if the relevant conversation is open
      if (data.from === activeConvId || data.to === activeConvId) {
        appendMessage(data);
        if (data.from === activeConvId) {
          wsSend({ type: 'mark_read', other_user: activeConvId });
        }
      }
      // Refresh the sidebar list
      loadConversations();
      break;

    case 'unread_count':
      updateUnreadBadge(data.count);
      break;

    case 'typing':
      if (data.from === activeConvId) showTyping();
      break;

    case 'user_status':
      // Update online/offline label in open conversation
      if (data.user_id === activeConvId) {
        const statusEl = document.getElementById('convStatus');
        if (statusEl) {
          statusEl.textContent = data.online ? 'Online' : 'Offline';
          statusEl.className   = 'conv-status ' + (data.online ? 'online' : '');
        }
      }
      break;
  }
}


// Toggle messages panel open / closed

function toggleMessages() {
  msgOpen = !msgOpen;
  const panel   = document.getElementById('msgPanel');
  const sideBtn = document.getElementById('msgSideBtn');

  if (msgOpen) {
    panel.classList.add('open');
    document.body.classList.add('msg-open');
    sideBtn?.classList.add('msg-active');
    loadConversations();
  } else {
    panel.classList.remove('open');
    document.body.classList.remove('msg-open');
    sideBtn?.classList.remove('msg-active');
    closeConv(true);
  }
}


// Load recent conversations into the sidebar list

async function loadConversations() {
  const list = document.getElementById('msgList');
  list.innerHTML = `<div class="msg-loading">
    <div class="msg-spinner"></div><span>Loading chats...</span>
  </div>`;

  try {
    const res  = await fetch(`${API_URL}?action=conversations`, { credentials: 'same-origin' });
    const text = await res.text();

    let json;
    try { json = JSON.parse(text); }
    catch(e) {
      console.error('[Chat] API returned non-JSON:', text.substring(0, 200));
      list.innerHTML = `<div class="msg-loading"><span>Could not load chats.<br><small style="color:#e60023">${text.substring(0,100)}</small></span></div>`;
      return;
    }

    const convs = json.conversations ?? [];

    if (json.error) {
      console.error('[Chat] API error:', json.error);
      list.innerHTML = `<div class="msg-loading"><span>Error: ${json.error}</span></div>`;
      return;
    }

    if (convs.length === 0) {
      list.innerHTML = `<div class="msg-loading" style="flex-direction:column;gap:6px;">
        <i class="bi bi-chat-dots" style="font-size:2rem;color:#e8e8e8;"></i>
        <span style="text-align:center;">No conversations yet.<br>Click <b>New message</b> to start chatting!</span>
      </div>`;
      return;
    }

    list.innerHTML = convs.map(c => `
      <div class="msg-item" id="conv-${c.id}"
           onclick="openConv(${c.id}, '${escHtml(c.name)}', '${escHtml(c.pic || '')}', ${c.is_online})">
        <div class="msg-avatar">
          ${c.pic
            ? `<img src="${escHtml(c.pic)}" alt="${escHtml(c.name)}">`
            : `<span style="font-weight:700;font-size:1.2rem;color:#fff;background:#e60023;
                width:100%;height:100%;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                ${escHtml(c.name[0]?.toUpperCase() || '?')}</span>`
          }
          ${c.is_online ? '<div class="online-dot"></div>' : ''}
        </div>
        <div class="msg-body">
          <div class="msg-name">${escHtml(c.name)}</div>
          <div class="msg-preview ${c.unread > 0 ? 'unread-preview' : ''}">${escHtml(c.last_msg)}</div>
        </div>
        <div class="msg-meta">
          <span class="msg-time">${escHtml(c.last_time || '')}</span>
          ${c.unread > 0 ? '<div class="msg-item-unread"></div>' : ''}
        </div>
      </div>
    `).join('');

  } catch(e) {
    console.error('[Chat] Fetch failed:', e);
    list.innerHTML = `<div class="msg-loading"><span>Could not load conversations</span></div>`;
  }
}

// Open a conversation
async function openConv(userId, name, pic, isOnline) {
  activeConvId = userId;

  // Update header info
  document.getElementById('convName').textContent = name;
  const statusEl = document.getElementById('convStatus');
  statusEl.textContent = isOnline ? 'Online' : 'Last seen recently';
  statusEl.className   = 'conv-status ' + (isOnline ? 'online' : '');

  // Set avatar
  const avEl = document.getElementById('convAvatar');
  avEl.innerHTML = pic
    ? `<img src="${escHtml(pic)}" alt="${escHtml(name)}">`
    : `<span style="font-weight:700;font-size:1.1rem;">${escHtml(name[0]?.toUpperCase() || '?')}</span>`;

  // Slide in conversation panel
  document.getElementById('convView').classList.add('open');
  convOpen = true;

  // Load message history
  const msgEl = document.getElementById('convMessages');
  msgEl.innerHTML = '<div class="msg-loading"><div class="msg-spinner"></div><span>Loading...</span></div>';

  try {
    const res  = await fetch(`${API_URL}?action=history&uid=${userId}`);
    const json = await res.json();
    msgEl.innerHTML = '';
    (json.messages || []).forEach(m => appendMessage(m, false));
    msgEl.scrollTop = msgEl.scrollHeight;
    // Mark messages as read
    wsSend({ type: 'mark_read', other_user: userId });
  } catch(e) {
    msgEl.innerHTML = '<div class="msg-loading"><span>Could not load messages</span></div>';
  }
}

function closeConv(silent) {
  document.getElementById('convView').classList.remove('open');
  convOpen     = false;
  activeConvId = null;
}


// Append a message bubble to the conversation view

function appendMessage(m, scroll = true) {
  const isMine = m.is_mine !== undefined ? m.is_mine : (m.from === MY_USER_ID);
  const msgs   = document.getElementById('convMessages');

  // Remove loading placeholder if present
  const loading = msgs.querySelector('.msg-loading');
  if (loading) loading.remove();

  const div = document.createElement('div');
  div.className = `conv-msg ${isMine ? 'conv-msg-out' : 'conv-msg-in'}`;
  div.innerHTML = `
    <div class="conv-bubble">${escHtml(m.message)}</div>
    <div class="conv-time">${m.time || 'Just now'}</div>
  `;
  msgs.appendChild(div);
  if (scroll) msgs.scrollTop = msgs.scrollHeight;
}

// Send a message via WebSocket

function sendMsg() {
  const input = document.getElementById('convInput');
  const text  = input.value.trim();
  if (!text || !activeConvId) return;

  wsSend({
    type:    'send_message',
    to:      activeConvId,
    message: text,
  });
  input.value = '';
}


// Typing indicator

function handleTyping() {
  wsSend({ type: 'typing', to: activeConvId });
  clearTimeout(typingTimer);
}

function showTyping() {
  const ti = document.getElementById('typingIndicator');
  ti.style.display = 'flex';
  clearTimeout(typingTimer);
  typingTimer = setTimeout(() => { ti.style.display = 'none'; }, 2500);
}


// Update unread badge on the sidebar icon and panel header

function updateUnreadBadge(count) {
  // Header badge
  const hb = document.getElementById('headerUnreadBadge');
  if (count > 0) {
    hb.textContent   = count > 99 ? '99+' : count;
    hb.style.display = 'inline-block';
  } else {
    hb.style.display = 'none';
  }

  // Sidebar icon badge 
  const sideBtn = document.getElementById('msgSideBtn');
  if (!sideBtn) return;
  let badge = document.getElementById('msgSideBadge');
  if (count > 0) {
    if (!badge) {
      badge = document.createElement('span');
      badge.id = 'msgSideBadge';
      badge.style.cssText = `position:absolute;top:2px;right:2px;background:#e60023;color:#fff;
        border-radius:50%;width:18px;height:18px;font-size:.68rem;font-weight:700;
        display:flex;align-items:center;justify-content:center;border:2px solid #fff;
        animation:badgePop .25s ease;`;
      sideBtn.style.position = 'relative';
      sideBtn.appendChild(badge);
    }
    badge.textContent = count > 99 ? '99+' : count;
  } else {
    badge?.remove();
  }
}


// New Message modal — load and search users

async function openNewMessage() {
  document.getElementById('newMsgOverlay').classList.add('show');
  document.getElementById('userSearchInput').value = '';

  const el = document.getElementById('userSearchResults');
  el.innerHTML = '<div class="msg-loading"><div class="msg-spinner"></div><span>Loading...</span></div>';

  try {
    // Saare users fetch karo
    const [usersRes, convsRes] = await Promise.all([
      fetch(`${API_URL}?action=get_users`, { credentials: 'same-origin' }),
      fetch(`${API_URL}?action=conversations`, { credentials: 'same-origin' })
    ]);

    const usersJson = await usersRes.json();
    const convsJson = await convsRes.json();

    allUsers = usersJson.users || [];

    // Jo users se already chat ho chuki hai unke IDs nikaalo
    const chattedIds = new Set((convsJson.conversations || []).map(c => c.id));

    // Pehle wo dikhao jinse chat nahi hui, phir baaki
    const newUsers      = allUsers.filter(u => !chattedIds.has(u.id));
    const existingUsers = allUsers.filter(u =>  chattedIds.has(u.id));

    renderUserList(newUsers, existingUsers);

  } catch(e) {
    el.innerHTML = '<div class="msg-loading"><span>Could not load users</span></div>';
  }
}

// renderUserList update 
function renderUserList(newUsers, existingUsers = []) {
  const el = document.getElementById('userSearchResults');

  if (!newUsers.length && !existingUsers.length) {
    el.innerHTML = '<div class="msg-loading"><span>No users found</span></div>';
    return;
  }

  let html = '';

  if (newUsers.length) {
    html += `<div style="font-size:.8rem;font-weight:600;color:#767676;padding:8px 10px 4px;">Suggested</div>`;
    html += newUsers.map(u => userItemHTML(u)).join('');
  }

  if (existingUsers.length) {
    html += `<div style="font-size:.8rem;font-weight:600;color:#767676;padding:8px 10px 4px;">Recent</div>`;
    html += existingUsers.map(u => userItemHTML(u)).join('');
  }

  el.innerHTML = html;
}

function userItemHTML(u) {
  return `
    <div class="user-search-item"
         onclick="startChat(${u.id}, '${escHtml(u.name)}', '${escHtml(u.pic || '')}', ${u.is_online})">
      <div class="msg-avatar" style="width:40px;height:40px;">
        ${u.pic
          ? `<img src="${escHtml(u.pic)}" alt="${escHtml(u.name)}">`
          : `<span style="font-weight:700;color:#fff;background:#e60023;width:100%;height:100%;
              border-radius:50%;display:flex;align-items:center;justify-content:center;">
              ${escHtml(u.name[0]?.toUpperCase() || '?')}</span>`
        }
        ${u.is_online ? '<div class="online-dot"></div>' : ''}
      </div>
      <div>
        <div style="font-weight:600;font-size:.92rem;">${escHtml(u.name)}</div>
        <div style="font-size:.8rem;color:${u.is_online ? '#31a24c' : '#767676'};">
          ${u.is_online ? 'Online' : 'Offline'}
        </div>
      </div>
    </div>`;
}

// Search bhi update karo
function searchUsers(query) {
  if (!query.trim()) {
    openNewMessage(); // Reset to full list
    return;
  }
  const q = query.toLowerCase();
  const filtered = allUsers.filter(u => u.name.toLowerCase().includes(q));
  renderUserList(filtered);
}

async function loadAllUsers() {
  const res  = await fetch(`${API_URL}?action=get_users`);
  const json = await res.json();
  allUsers   = json.users || [];
  renderUserList(allUsers);
}

function searchUsers(query) {
  if (!query.trim()) { renderUserList(allUsers); return; }
  const q = query.toLowerCase();
  renderUserList(allUsers.filter(u => u.name.toLowerCase().includes(q)));
}

function renderUserList(users) {
  const el = document.getElementById('userSearchResults');
  if (!users.length) {
    el.innerHTML = '<div class="msg-loading"><span>No users found</span></div>';
    return;
  }
  el.innerHTML = users.map(u => `
    <div class="user-search-item"
         onclick="startChat(${u.id}, '${escHtml(u.name)}', '${escHtml(u.pic || '')}', ${u.is_online})">
      <div class="msg-avatar" style="width:40px;height:40px;">
        ${u.pic
          ? `<img src="${escHtml(u.pic)}" alt="${escHtml(u.name)}">`
          : `<span style="font-weight:700;color:#fff;background:#e60023;width:100%;height:100%;
              border-radius:50%;display:flex;align-items:center;justify-content:center;">
              ${escHtml(u.name[0]?.toUpperCase() || '?')}</span>`
        }
        ${u.is_online ? '<div class="online-dot"></div>' : ''}
      </div>
      <div>
        <div style="font-weight:600;font-size:.92rem;">${escHtml(u.name)}</div>
        <div style="font-size:.8rem;color:${u.is_online ? '#31a24c' : '#767676'};">
          ${u.is_online ? 'Online' : 'Offline'}
        </div>
      </div>
    </div>
  `).join('');
}

function startChat(userId, name, pic, isOnline) {
  closeNewMessage();
  if (!msgOpen) toggleMessages();
  openConv(userId, name, pic, isOnline);
}

function closeNewMessage() {
  document.getElementById('newMsgOverlay').classList.remove('show');
}

// Remove a conversation item from the sidebar list

function dismissConv(id) {
  const el = document.getElementById('conv-' + id);
  if (el) {
    el.style.transition = 'all .2s';
    el.style.opacity    = '0';
    el.style.height     = '0';
    el.style.overflow   = 'hidden';
    setTimeout(() => el.remove(), 200);
  }
}


// Escape HTML to prevent XSS

function escHtml(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}


// Keyboard shortcuts

document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    if (document.getElementById('newMsgOverlay').classList.contains('show')) { closeNewMessage(); return; }
    if (convOpen) { closeConv(); return; }
    if (msgOpen)  { toggleMessages(); }
  }
});


if (MY_USER_ID > 0) {
  wsConnect();
} else {
  console.warn('[Chat] User is not logged in — chat disabled');
}
</script>