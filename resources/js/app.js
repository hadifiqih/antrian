import './bootstrap';
import '../css/app.css';
import Swal from 'sweetalert2';
import axios from 'axios';
import lightGallery from 'lightgallery';
import lgThumbnail from 'lightgallery/plugins/thumbnail';
import lgZoom from 'lightgallery/plugins/zoom';

import * as PusherPushNotifications from "@pusher/push-notifications-web";

//init lightgallery
lightGallery(document.getElementById('lightgallery'), {
    plugins: [lgThumbnail, lgZoom],
    speed: 500,
    thumbnail: true,
});

// Setup Axios Interceptor
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response.status === 401) {
            // Panggil fungsi untuk menangani logout otomatis
            handleAutoLogout();
        }
        return Promise.reject(error);
    }
);

function handleAutoLogout() {
    const beamsClient = new PusherPushNotifications.Client({
        instanceId: '0958376f-0b36-4f59-adae-c1e55ff3b848',
    });

    beamsClient.stop()
        .then(() => {
            console.log('Beams client stopped due to session timeout.');
            // Tambahkan logika logout Anda di sini, misalnya menghapus sesi pengguna dan mengarahkan ke halaman login
            localStorage.removeItem('beamsInitialized');
            window.location.href = '/login'; // Arahkan ke halaman login
        })
        .catch(console.error);
}



