document.addEventListener("DOMContentLoaded", () => {

const chatBox = document.getElementById('chat-box'),
      msgInput = document.getElementById('msg'),
      sendBtn = document.getElementById('sendBtn'),
      chatList = document.getElementById('chat-list');

let currentChatId = null;

/* =========================
   ENABLE / DISABLE BUTTON
========================= */
msgInput?.addEventListener('input', () => {
    sendBtn.disabled = msgInput.value.trim() === '';

    // auto resize textarea
    msgInput.style.height = 'auto';
    msgInput.style.height = msgInput.scrollHeight + 'px';
});


/* =========================
   ADD MESSAGE (CORE FIX)
========================= */
function addMessage(role, text) {
    const wrapper = document.createElement('div');
    wrapper.style.display = 'flex';
    wrapper.style.justifyContent = role === 'user' ? 'flex-end' : 'flex-start';

    const div = document.createElement('div');
    div.className = 'message ' + role;
    div.textContent = text;

    wrapper.appendChild(div);
    chatBox.appendChild(wrapper);

    chatBox.scrollTop = chatBox.scrollHeight;
}


/* =========================
   SEND MESSAGE
========================= */
function send() {
    const text = msgInput.value.trim();
    if (!text) return;

    document.querySelector('.main').classList.add('chat-mode');

    // tampilkan user message
    addMessage('user', text);

    msgInput.value = '';
    sendBtn.disabled = true;
    msgInput.style.height = 'auto';

    // AI loading
    const wrapper = document.createElement('div');
    wrapper.style.display = 'flex';
    wrapper.style.justifyContent = 'flex-start';

    const aiDiv = document.createElement('div');
    aiDiv.className = 'message ai';
    aiDiv.textContent = 'Thinking...';

    wrapper.appendChild(aiDiv);
    chatBox.appendChild(wrapper);

    chatBox.scrollTop = chatBox.scrollHeight;

    fetch('chat.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: text, chat_id: currentChatId })
    })
    .then(res => res.json())
    .then(data => {
        currentChatId = data.chat_id;
        aiDiv.textContent = data.reply || 'Kosong';
        chatBox.scrollTop = chatBox.scrollHeight;
        loadChats();
    })
    .catch(err => {
        aiDiv.textContent = 'Error koneksi';
        console.error(err);
    });
}


/* =========================
   EVENT LISTENER
========================= */
sendBtn?.addEventListener('click', send);

msgInput?.addEventListener('keydown', e => {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        send();
    }
});


/* =========================
   NEW CHAT
========================= */
window.createChat = () => {
    currentChatId = null;
    chatBox.innerHTML = '<p style="opacity:0.5">Chat baru</p>';
};


/* =========================
   LOAD CHAT LIST
========================= */
function loadChats() {
    fetch('get-chats.php')
    .then(res => res.json())
    .then(data => {
        chatList.innerHTML = '';

        if (!data || data.length === 0) {
            chatList.innerHTML = '<p style="opacity:0.5">Belum ada chat</p>';
            return;
        }

        data.forEach(chat => {
            const div = document.createElement('div');
            div.className = 'chat-item';
            div.textContent = chat.title;

            if (chat.id == currentChatId) {
                div.classList.add('active');
            }

            div.onclick = () => {
                currentChatId = chat.id;
                document.querySelector('.main').classList.add('chat-mode');
                loadMessages(chat.id);
                loadChats();
            };

            chatList.appendChild(div);
        });
    })
    .catch(err => {
        chatList.innerHTML = '<p style="opacity:0.5">Gagal load chats</p>';
        console.error(err);
    });
}


/* =========================
   LOAD MESSAGES
========================= */
function loadMessages(chatId) {
    if (!chatId) {
        chatBox.innerHTML = '<p style="opacity:0.5">Pilih chat dulu</p>';
        return;
    }

    currentChatId = chatId;
    chatBox.innerHTML = '<p style="opacity:0.5">Loading...</p>';

    fetch('get-messages.php?chat_id=' + encodeURIComponent(chatId))
    .then(res => res.json())
    .then(data => {
        chatBox.innerHTML = '';

        if (!data || data.length === 0) {
            chatBox.innerHTML = '<p style="opacity:0.5">Chat kosong</p>';
            return;
        }

        data.forEach(msg => {
            addMessage(
                msg.role === 'user' ? 'user' : 'ai',
                msg.content
            );
        });

        chatBox.scrollTop = chatBox.scrollHeight;
    })
    .catch(err => {
        chatBox.innerHTML = '<p style="color:red">Gagal load chat</p>';
        console.error(err);
    });
}


/* =========================
   INIT
========================= */
loadChats();

});
