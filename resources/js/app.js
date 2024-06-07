import './bootstrap';
import '../css/app.css';
import axios from 'axios';
import showdown from 'showdown';

document.addEventListener('DOMContentLoaded', function () {
    const chatBox = $('#chat-box');
    const sendButton = $('#send-button');
    const messageInput = $('#message-input');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const loading = $('#loading');
    const converter = new showdown.Converter();

    window.setMessage = function setMessage(message) {
        loading.show();
        addMessage('user', message);
        $.ajax({
            url: '/bot/send-message',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            },
            data: JSON.stringify({ message: message }),
            success: function(data) {
                loading.hide();
                let messages = data.messages;
                for (let i = 0; i < messages.length; i++) {
                    if (messages[i].type == 'answer') {
                        addMessage('bot', converter.makeHtml(messages[i].content));
                    } else if (messages[i].type == 'follow_up') {
                        recommendationMessage(messages[i].content);
                    }
                }
            },
            error: function() {
                loading.hide();
                addMessage('bot', 'Maaf, terjadi kesalahan.');
            }
        });
    }

    function addMessage(sender, message) {
        const messageElement = $('<div></div>').addClass('message').addClass(sender);
        messageElement.html(`<span class="msg-content">${message}</span>`);
        chatBox.append(messageElement);
        chatBox.scrollTop(chatBox.prop('scrollHeight'));
    }

    function recommendationMessage(message) {
        const recomMessage = $('<div></div>').addClass('message-recommendation').addClass('bot');
        recomMessage.html(`<button type="button" onclick="setMessage('${message}')" class="btn msg-content"><p class="text-secondary mb-0">${message}</p></button>`);
        chatBox.append(recomMessage);
        chatBox.scrollTop(chatBox.prop('scrollHeight'));
    }

    sendButton.on('click', function() {
        const message = messageInput.val();
        if (message.trim() === '') {
            return;
        }

        addMessage('user', message);
        messageInput.val('');

        loading.show();

        $.ajax({
            url: '/bot/send-message',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            },
            data: JSON.stringify({ message: message }),
            success: function(data) {
                loading.hide();
                let messages = data.messages;
                for (let i = 0; i < messages.length; i++) {
                    if (messages[i].type == 'answer') {
                        addMessage('bot', converter.makeHtml(messages[i].content));
                    } else if (messages[i].type == 'follow_up') {
                        recommendationMessage(messages[i].content);
                    }
                }
            },
            error: function() {
                loading.hide();
                addMessage('bot', 'Maaf, terjadi kesalahan. Silahkan mencoba lagi.');
            }
        });
    });
});