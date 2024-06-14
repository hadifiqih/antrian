@extends('layouts.app')

@section('title', 'ChatBot | CV. Kassab Syariah')

@section('username', Auth::user()->name ?? 'Guest')

@section('page', 'AI')

@section('breadcrumb', 'ChatBot')

@section('content')
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