document.addEventListener('DOMContentLoaded', () => {
    const chatbox = document.getElementById('chatbox');

    setInterval(() => {
        fetch(window.location.href)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newChatbox = doc.getElementById('chatbox');
                chatbox.innerHTML = newChatbox.innerHTML;
            });
    }, 5000);
});
