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

        /* HTML: <div class="loader"></div> */
        .loaderbot {
        height: 8px;
        aspect-ratio: 5;
        display: grid;
        --_g: no-repeat radial-gradient(farthest-side,#000 94%,#0000);
        }
        .loaderbot:before,
        .loaderbot:after {
        content: "";
        grid-area: 1/1;
        background:
            var(--_g) left,
            var(--_g) right;
        background-size: 20% 100%;
        animation: l32 1s infinite; 
        }
        .loaderbot:after { 
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
                    <div class="card-header bg-dark"><h4 class="mb-0">Chatbot</h4></div>
                    <div class="card-body chat-box" id="chat-box">
                        <div class="message bot">
                            <span class="msg-content">
                                <p class="mb-0">Halo, ada yang bisa saya bantu? 🧐</p>
                            </span>
                        </div>
                    </div>
                    <div class="message bot" id="loading" style="display: none; margin-left:20px;">
                        <span class="msg-content">
                            <div class="loaderbot"></div>
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