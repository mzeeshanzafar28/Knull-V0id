import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;


const csrfTokenElement = document.head.querySelector('meta[name="csrf-token"]');
const csrfToken = csrfTokenElement ? csrfTokenElement.content : '';

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST || '127.0.0.1',
    wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
    forceTLS: false,
    disableStats: true,
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    },
    withCredentials: true,
    enabledTransports: ['ws', 'wss']
});
