document.addEventListener('alpine:init', () => {
    Alpine.prefix('pf-');

    Alpine.data('darkModeToggle', () => ({
        mode: localStorage.getItem('plugin_frame_theme') || 'system', // Default to system mode
        init() {
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
            // Apply system, light, or dark mode to the root element
            if (mode === 'system') {
                const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)').matches;
                html.setAttribute('data-mode', prefersDarkScheme ? 'pf-dark' : 'pf-light');
            } else {
                html.setAttribute('data-mode', `pf-${mode}`);
            }
        },
    }));
});
