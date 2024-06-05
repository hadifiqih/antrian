@extends('layouts.app')

@section('title', 'ChatBot | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'AI')

@section('breadcrumb', 'ChatBot')

@section('content')
    <style>
        .chat-box {
            max-height: 400px;
            overflow-y: auto;
        }
        .message {
            margin: 10px 0;
        }
        .message.user {
            text-align: right;
        }
        .message.bot {
            text-align: left;
        }
        .message .msg-content {
            display: inline-block;
            padding: 10px;
            border-radius: 20px;
        }
        .message.user .msg-content {
            background-color: #007bff;
            color: white;
        }
        .message.bot .msg-content {
            background-color: #e9ecef;
            color: black;
        }
        /* HTML: <div class="loader"></div> */
        .loader {
        height: 15px;
        aspect-ratio: 5;
        display: grid;
        --_g: no-repeat radial-gradient(farthest-side,#000 94%,#0000);
        }
        .loader:before,
        .loader:after {
        content: "";
        grid-area: 1/1;
        background:
            var(--_g) left,
            var(--_g) right;
        background-size: 20% 100%;
        animation: l32 1s infinite; 
        }
        .loader:after { 
        background:
            var(--_g) calc(1*100%/3),
            var(--_g) calc(2*100%/3);
        background-size: 20% 100%;
        animation-direction: reverse;
        }
        @keyframes l32 {
        80%,100% {transform:rotate(.5turn)}
        }
    </style>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Chatbot</div>
                    <div class="card-body chat-box" id="chat-box">
                        <!-- Messages will appear here -->
                        <!-- Loading animation -->
                    </div>
                    <div class="message bot" id="loading" style="display: none;">
                        <span class="msg-content">
                            <div  class="loader" ></div>
                        </span>
                    </div>
                    <div class="card-footer">
                        <div class="input-group">
                            <textarea type="text" id="message-input" class="form-control" placeholder="Masukkan perintah disini.."></textarea>
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" id="send-button">Kirim</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const chatBox = document.getElementById('chat-box');
    const sendButton = document.getElementById('send-button');
    const messageInput = document.getElementById('message-input');
    const csrfToken = '{{ csrf_token() }}';
    const loading = document.getElementById('loading');

    function addMessage(sender, message) {
        const messageElement = document.createElement('div');
        messageElement.classList.add('message', sender);
        messageElement.innerHTML = `<span class="msg-content">${message}</span>`;
        chatBox.appendChild(messageElement);
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    async function sendMessage() {
        const message = messageInput.value;
        if (message) {
            addMessage('user', message);
            messageInput.value = '';
            loading.style.display = 'block'; // Show loading animation

            try {
                const response = await fetch('/bot/send-message', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ message })
                });
                const data = await response.json();
                addMessage('bot', response.data.messages[0].content);
            } catch (error) {
                console.error('Error:', error);
                addMessage('bot', 'Maaf terjadi kesalahan..');
            } finally {
                loading.style.display = 'none'; // Hide loading animation
            }
        }
    }

    sendButton.addEventListener('click', sendMessage);
});
</script>
@endsection