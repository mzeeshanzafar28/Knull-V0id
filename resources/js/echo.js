import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;


const csrfTokenElement = document.head.querySelector('meta[name="csrf-token"]');
const csrfToken = csrfTokenElement ? csrfTokenElement.content : '';
const onionDomain = import.meta.env.VITE_APP_ONION_DOMAIN;
const reverbHost = import.meta.env.VITE_REVERB_HOST;

const wsHost = (typeof onionDomain === 'string' && onionDomain.trim() !== '')
    ? onionDomain
    : reverbHost;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost,
    wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
    forceTLS: false,
    disableStats: true,
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: { 'X-CSRF-TOKEN': csrfToken }
    },
    withCredentials: true,
    enabledTransports: ['ws', 'wss']
});
