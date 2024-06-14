import './bootstrap';
import '../css/app.css';
import axios from 'axios';
import showdown from 'showdown';

import 'dropzone/dist/dropzone.css';
import Dropzone from 'dropzone';

document.addEventListener('DOMContentLoaded', function () {
    const chatBox = $('#chat-box');
    const sendButton = $('#send-button');
    const messageInput = $('#message-input');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const loading = $('#loading');
    const converter = new showdown.Converter();
    let myDropzoneDokumentasi = $('#my-dropzone-dokumentasi');

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

  // Konfigurasi Dropzone
  Dropzone.autoDiscover = false;

  myDropzoneDokumentasi.dropzone({
    paramName: "file", // Nama yang digunakan untuk mentransfer file
    maxFilesize: 50, // Ukuran maksimum file dalam MB
    dictDefaultMessage: "Letakkan file di sini atau klik untuk memilih file",
    acceptedFiles: 'image/*', // Batasi hanya file gambar yang dapat diunggah
    addRemoveLinks: true, // Tampilkan tautan Hapus pada setiap file yang diunggah
    autoProcessQueue: false, // Nonaktifkan otomatis mengunggah file saat file dipilih
    init: function() {
      var submitButton = document.querySelector("#submitUpload")
      myDropzone = this; // Menyimpan objek Dropzone ke variabel global
      submitButton.addEventListener("click", function() {
        myDropzone.processQueue(); // Mengunggah file yang dipilih
      });
      // Menampilkan gambar saat file diunggah
      this.on("complete", function(file) {
        if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
          // tampilkan swal berhasil
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Dokumentasi berhasil diunggah',
                showConfirmButton: false,
                timer: 1500
            });

            // redirect ke halaman sebelumnya
            setTimeout(function() {
                window.location.href = "{{ route('documentation.index') }}";
            }, 2000);
        }
    });
    }
  });
});