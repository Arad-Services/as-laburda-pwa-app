/**
 * Service Worker Registration for AS Laburda PWA App.
 *
 * This script registers the service worker for the PWA functionality,
 * checking for browser compatibility.
 *
 * @link       https://arad-services.com
 * @since      2.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/public/js
 */

if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        // The service worker file is served from the root scope '/'
        // so its path is simply '/service-worker.js'
        navigator.serviceWorker.register('/service-worker.js').then(function(registration) {
            // Registration was successful
            console.log('ServiceWorker registration successful with scope: ', registration.scope);
        }, function(err) {
            // registration failed :(
            console.log('ServiceWorker registration failed: ', err);
        });
    });
} else {
    console.log('Service Workers are not supported in this browser.');
}
