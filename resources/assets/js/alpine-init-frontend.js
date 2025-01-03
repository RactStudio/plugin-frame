document.addEventListener('alpine:init', () => {
    Alpine.prefix('pf-');

    Alpine.data('darkModeToggle', () => ({
        mode: localStorage.getItem('plugin_frame_theme') || 'system', // Default to system mode
        init() {
            // Apply the mode on initialization
            if (!localStorage.getItem('plugin_frame_theme')) {
                localStorage.setItem('plugin_frame_theme', 'system'); // Set default mode to system
            }
            this.mode = localStorage.getItem('plugin_frame_theme');
            this.applyMode(this.mode);
        },
        toggleMode(newMode) {
            this.mode = newMode;
            localStorage.setItem('plugin_frame_theme', newMode); // Save mode to localStorage
            this.applyMode(newMode);
        },
        applyMode(mode) {
            const html = document.documentElement;
            html.setAttribute('data-mode', `pf-${mode}`); // Set the data-mode attribute based on the selected mode
        },
    }));

    // Alpine.start();
});
