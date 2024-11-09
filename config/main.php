<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Recursive function to load PHP files from directories and subdirectories
function load_files_recursively($dir) {
    // Load all PHP files in the current directory
    foreach (glob($dir . '/*.php') as $file) {
        require_once $file;
    }

    // Recursively load PHP files from subdirectories
    foreach (glob($dir . '/*', GLOB_ONLYDIR) as $subdir) {
        load_files_recursively($subdir);  // Calls itself for each subdirectory
    }
}

// Directories to scan and load PHP files (use main directories)
$directories = [
    __DIR__ . '/../app/',        // App-related files (Controllers, Models, Services, etc.)
    __DIR__ . '/../admin/',      // Admin-related PHP files
    __DIR__ . '/../public/',     // Public assets or other PHP files (if any)
    __DIR__ . '/../languages/',  // Language files for i18n
    __DIR__ . '/../cli/',        // WP-CLI commands (optional)
];

// Loop through the directories and load files recursively
foreach ($directories as $directory) {
    load_files_recursively($directory);  // Calls the function to process each directory
}
