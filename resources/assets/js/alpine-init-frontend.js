// Frontend Alpine.js Initialization
// Custom namespace for Alpine instance
document.addEventListener('alpine:init', () => {
    Alpine.prefix('pf-'); // Use a custom prefix for directives in admin
    console.log("Custom namespace 'pf-' set for Alpine.js in Admin.");
});

// Initialize Alpine.js if not already started
document.addEventListener('DOMContentLoaded', () => {
    if (typeof Alpine === 'undefined') {
        console.log("Initializing Alpine.js for Admin...");
        Alpine.start();
    } else {
        console.log("Alpine.js is already initialized by another script.");
    }
});
