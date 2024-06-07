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
        .loader {
        width: 30px;
        height: 30px;
        aspect-ratio: 1;
        border-radius: 50%;
        border: 0px;
        background: #f03355;
        clip-path: polygon(0 0,100% 0,100% 100%,0 100%);
        animation: l1 2s infinite cubic-bezier(0.3,1,0,1);
        }
        @keyframes l1 {
        33% {border-radius: 0;background: #514b82 ;clip-path: polygon(0 0,100% 0,100% 100%,0 100%)}
        66% {border-radius: 0;background: #ffa516 ;clip-path: polygon(50% 0,50% 0,100% 100%,0 100%)}
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
                            <div class="loader"></div>
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