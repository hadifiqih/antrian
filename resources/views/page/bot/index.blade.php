@extends('layouts.app')

@section('title', 'ChatBot | CV. Kassab Syariah')

@section('username', Auth::user()->name ?? 'Guest')

@section('page', 'AI')

@section('breadcrumb', 'ChatBot')

@section('content')
<style>
    body {
        background-color: #f8f9fa;
    }

    .card {
        border-radius: 15px;
        overflow: hidden;
    }

    .card-header {
        background-color: #007bff;
        color: #fff;
        padding: 15px;
    }

    .chat-box {
        max-height: 500px;
        overflow-y: auto;
        padding: 15px;
        background-color: #ffffff;
        border-bottom: 1px solid #f1f1f1;
    }

    .message {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    .message.bot .msg-content {
        background-color: #e9ecef;
        color: #333;
        border-radius: 15px;
        padding: 10px 15px;
        max-width: 60%;
    }

    .message.user .msg-content {
        background-color: #007bff;
        color: #fff;
        border-radius: 15px;
        padding: 10px 15px;
        max-width: 60%;
        margin-left: auto;
        text-align: left;
    }

    .input-group {
        margin-top: 15px;
    }

    .input-group textarea {
        border-radius: 15px 0 0 15px;
        resize: none;
    }

    .input-group .btn {
        border-radius: 0 15px 15px 0;
    }
</style>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 flex-fill">Chatbot</h4>
                    <p class="text-sm mb-0">Kuota Interaksi : {{ $remainingInteractions ?? 30 }}</p>
                </div>
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
                        <textarea rows="4" type="text" id="message-input" class="form-control" placeholder="Masukkan perintah disini.."></textarea>
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