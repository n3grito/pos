import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

const key = import.meta.env.VITE_REVERB_APP_KEY;
const host = import.meta.env.VITE_REVERB_HOST;

let echo = null;

if (key && host) {
    window.Pusher = Pusher;

    echo = new Echo({
        broadcaster: 'reverb',
        key,
        wsHost: host,
        wsPort: Number(import.meta.env.VITE_REVERB_PORT || 8080),
        wssPort: 443,
        forceTLS: false,
        enabledTransports: ['ws', 'wss'],
    });
}

export default echo;
