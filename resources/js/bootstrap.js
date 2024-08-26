/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */
// window.axios = require('axios');

// window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');
var pusherConfiguration = {
    broadcaster: 'pusher',
    key: window.appConfig.pusher.key,
    cluster: window.appConfig.pusher.cluster,
    forceTLS: true,
    withoutInterceptors: true,
    authEndpoint: window.appConfig.pusher.authEndpoint
};
// soketi configuration if required
if (window.appConfig.broadcast_connection_driver == 'soketi') {
    pusherConfiguration = {
        broadcaster: 'pusher',
        key: window.appConfig.pusher.key,
        cluster: '',
        forceTLS: window.appConfig.pusher.useTLS,
        wsHost: window.appConfig.pusher.host,
        wsPort: window.appConfig.pusher.port,
        wssPort: window.appConfig.pusher.port,
        encrypted: window.appConfig.pusher.encrypted,
        disableStats: true,
        enabledTransports: ['ws', 'wss'],
        authEndpoint: window.appConfig.pusher.authEndpoint
    };
}
window.Echo = new Echo(pusherConfiguration);