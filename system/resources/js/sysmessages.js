// Javascript to handle system messages calls - actually used for subscribe/unsubscribe

document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    
    if (params.toString()) {
        const scriptEl = document.currentScript || document.querySelector('script[data-base]');
        const base = scriptEl?.dataset.base || '/';
        
        fetch(base + 'system/resources/js/sysmessages-set.js?' + params.toString())
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Parse the JSON string if needed
                const messages = typeof data === 'string' ? JSON.parse(data) : data;
                
                if (!Array.isArray(messages) || messages.length === 0) return;

                const messagesDiv = document.getElementById('messages-div');
                const messagesTextDiv = document.getElementById('messages-text');
                // const innerDiv = messagesDiv.querySelector('.message-alert');
                const innerDiv = messagesDiv.querySelector('.message-alert') || messagesDiv;

                // Show messages one at a time, sequentially
                let index = 0;

                function showMessage(msg) {
                    if (!msg.message || msg.message.trim() === '') return showNext();

                    messagesTextDiv.textContent = msg.message;
                    innerDiv.className = innerDiv.className + ' message-alert-' + msg.class;

                    // Reset display and opacity
                    messagesDiv.style.transition = 'none';
                    messagesDiv.style.opacity = '1';
                    messagesDiv.style.display = 'block';

                    // Fade out after 5 seconds, then show next
                    setTimeout(() => {
                        messagesDiv.style.transition = 'opacity 0.8s ease';
                        messagesDiv.style.opacity = '0';
                        messagesDiv.addEventListener('transitionend', () => {
                            showNext();
                        }, { once: true });
                    }, 5000);
                }

                function showNext() {
                    if (index < messages.length) {
                        showMessage(messages[index++]);
                    } else {
                        messagesDiv.style.display = 'none';
                    }
                }

                showNext();
            });
    }
});