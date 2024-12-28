// Admin Alpine.js Initialization
document.addEventListener('alpine:init', () => {
    // Set a custom prefix for directives
    Alpine.prefix('pf-');
    console.log("Custom namespace 'pf-' set for Alpine.js.");

    // Create a global Alpine store for pf-data
    Alpine.store('data', {
        darkMode: false,
        toggleDarkMode() {
            this.darkMode = !this.darkMode;
            this.applyDarkMode();
        },
        applyDarkMode() {
            if (this.darkMode) {
                document.documentElement.classList.add('pf-dark');
            } else {
                document.documentElement.classList.remove('pf-dark');
            }
        },
    });

    // Apply dark mode state on page load
    Alpine.store('data').applyDarkMode();
    console.log("Global store for 'pf-data' initialized.");
});

// Initialize Alpine.js if not already started
document.addEventListener('DOMContentLoaded', () => {
    if (typeof Alpine === 'undefined') {
        console.log("Initializing Alpine.js...");
        Alpine.start();
    } else {
        console.log("Alpine.js is already initialized by another script.");
    }
});
