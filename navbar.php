
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Navbar</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <!-- ...existing code... -->
            <li class="nav-item">
                <button id="openChat" class="btn btn-primary">Ouvrir le chat</button>
            </li>
        </ul>
    </div>
</nav>
<!-- Chat Box -->
<div id="chatBox" class="chat-box">
    <div class="chat-header">
        <h5>Chat</h5>
        <button id="closeChat" class="close-chat">&times;</button>
    </div>
    <div id="chatMessages" class="chat-messages"></div>
    <div class="chat-input">
        <input type="text" id="chatMessage" placeholder="Écrire un message...">
        <button id="sendMessage" class="btn btn-primary">Envoyer</button>
    </div>
</div>
<script>
    document.getElementById('openChat').addEventListener('click', function() {
        document.getElementById('chatBox').style.display = 'block';
    });

    document.getElementById('closeChat').addEventListener('click', function() {
        document.getElementById('chatBox').style.display = 'none';
    });

    document.getElementById('sendMessage').addEventListener('click', function() {
        const message = document.getElementById('chatMessage').value;
        if (message.trim() !== '') {
            fetch('send_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'message=' + encodeURIComponent(message)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('chatMessage').value = '';
                    loadMessages();
                } else {
                    alert(data.message);
                }
            });
        }
    });

    function loadMessages() {
        fetch('get_messages.php')
            .then(response => response.json())
            .then(data => {
                const chatMessages = document.getElementById('chatMessages');
                chatMessages.innerHTML = '';
                data.forEach(msg => {
                    const messageElement = document.createElement('div');
                    messageElement.classList.add('chat-message');
                    messageElement.textContent = msg.username + ': ' + msg.message;
                    chatMessages.appendChild(messageElement);
                });
            });
    }

    loadMessages();
    setInterval(loadMessages, 5000);
</script>