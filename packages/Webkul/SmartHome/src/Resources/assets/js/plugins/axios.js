/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */
import axios from "axios";

window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// Use a relative base so we never bake in an http scheme from APP_URL.
axios.defaults.baseURL = '/';

// Upgrade insecure same-host http URLs when page is loaded over https.
axios.interceptors.request.use((config) => {
    try {
        if (typeof window !== 'undefined' && window.location.protocol === 'https:' && config.url) {
            const loc = window.location;
            const httpHost = 'http://' + loc.host + '/';
            if (config.url.startsWith(httpHost)) {
                config.url = 'https://' + loc.host + '/' + config.url.substring(httpHost.length);
            }
        }
    } catch (_) {}
    return config;
});

export default {
    install(app) {
        app.config.globalProperties.$axios = axios;
    },
};
