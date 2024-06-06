@extends('layouts.app')

@section('title', 'ChatBot | CV. Kassab Syariah')

@section('username', Auth::user()->name ?? 'Guest')

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
        .message-recommendation .msg-content {
            display: inline-block;
            padding: 10px;
            border-radius: 20px;
            margin-bottom: 5px;
        }
        .message.user .msg-content {
            background-color: #007bff;
            color: white;
        }
        .message.bot .msg-content {
            background-color: #e9ecef;
            color: black;
        }
        .message-recommendation.bot .msg-content {
            background-color: #e9ecef;
            color: grey;
        }

        .loader {
        width: 48px;
        height: 48px;
        display: block;
        margin:15px auto;
        position: relative;
        color: #FFF;
        box-sizing: border-box;
        animation: rotation 1s linear infinite;
        }
        .loader::after,
        .loader::before {
        content: '';  
        box-sizing: border-box;
        position: absolute;
        width: 24px;
        height: 24px;
        top: 0;
        background-color: #FFF;
        border-radius: 50%;
        animation: scale50 1s infinite ease-in-out;
        }
        .loader::before {
        top: auto;
        bottom: 0;
        background-color: #FF3D00;
        animation-delay: 0.5s;
        }

        @keyframes rotation {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
        } 
        @keyframes scale50 {
        0%, 100% {
            transform: scale(0);
        }
        50% {
            transform: scale(1);
        }
        } 
    </style>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header bg-dark"><h4 class="mb-0">Chatbot</h4></div>
                    <div class="card-body chat-box" id="chat-box">
                        <div class="message bot">
                            <span class="msg-content">
                                <p class="mb-0">Halo, ada yang bisa saya bantu? 🧐</p>
                            </span>
                        </div>
                        <!-- Messages will appear here -->
                        <!-- Loading animation -->
                    </div>
                    <div class="message bot" id="loading" style="display: none;margin-left:20px;">
                        <span class="msg-content">
                            <span class="loader" style="border:0px;border-top:0px;"></span>
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
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
    const chatBox = $('#chat-box');
    const sendButton = $('#send-button');
    const messageInput = $('#message-input');
    const csrfToken = '{{ csrf_token() }}';
    const loading = $('#loading');

    function setMessage(message) {
        const loading = $('#loading');
        loading.show();
        addMessage('user', message);
        $.ajax({
            url: '/bot/send-message',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            },
            data: JSON.stringify({
                message: message
            }),
            success: function(data) {
                loading.hide();
                let messages = data.messages;
                for (let i = 0; i < messages.length; i++) {
                    if(messages[i].type == 'answer') {
                        addMessage('bot', marked.parse(messages[i].content));
                    } else if(messages[i].type == 'follow_up') {
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
            messageElement.html(`<span class="msg-content"><p>${message}</p></span>`);
            chatBox.append(messageElement);
            chatBox.scrollTop(chatBox.prop('scrollHeight'));
        }

        function recommendationMessage(message) {
            const recomMessage = $('<div></div>').addClass('message-recommendation').addClass('bot');
            recomMessage.html(`<button type="button" onclick="setMessage('${message}')" class="btn msg-content"><p class="text-secondary mb-0">${message}</p></button>`);
            chatBox.append(recomMessage);
            chatBox.scrollTop(chatBox.prop('scrollHeight'));
        }

    $(document).ready(function() {

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
                data: JSON.stringify({
                    message: message
                }),
                success: function(data) {
                    loading.hide();
                    let messages = data.messages;
                    for (let i = 0; i < messages.length; i++) {
                        if(messages[i].type == 'answer') {
                            addMessage('bot', messages[i].content);
                        } else if(messages[i].type == 'follow_up') {
                            recommendationMessage(messages[i].content);
                        }
                    }
                },
                error: function() {
                    loading.hide();
                    addMessage('bot', 'Maaf, terjadi kesalahan.');
                }
            });
        });
    });
</script>
@endsection