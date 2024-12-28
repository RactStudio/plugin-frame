/**
 * Tailwind Dark Mode
 */
document.addEventListener('alpine:init', () => {
    Alpine.store('darkMode', {
        isDark: localStorage.getItem('darkMode') === 'true' || document.documentElement.classList.contains('pf-dark'),

        toggle() {
            this.isDark = !this.isDark;
            this.apply();
        },

        apply() {
            if (this.isDark) {
                document.documentElement.setAttribute('data-mode', 'pf-dark');
                document.documentElement.classList.add('pf-dark');
            } else {
                document.documentElement.removeAttribute('data-mode');
                document.documentElement.classList.remove('pf-dark');
            }
            localStorage.setItem('darkMode', this.isDark);
        },
    });

    // Initialize mode on load
    Alpine.store('darkMode').apply();
});

// Function to toggle dark mode programmatically
function toggleDarkMode(isDark) {
    if (window.Alpine && Alpine.store('darkMode')) {
        Alpine.store('darkMode').isDark = isDark;
        Alpine.store('darkMode').apply();
    } else {
        console.error("Alpine.js or DarkMode store is not initialized.");
    }
}