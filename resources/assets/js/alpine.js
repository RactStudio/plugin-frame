// Import Alpine JS
import Alpine from 'alpinejs';

// Configure Alpine to use a custom prefix (e.g., "pf-")
Alpine.config = {
    prefix: 'pf-'
};

window.Alpine = Alpine;
Alpine.start();