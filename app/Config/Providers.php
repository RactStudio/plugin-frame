<?php

namespace PluginFrame\Config;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Providers
{
    /**
     * Add each class manually.
     * Only from directory inside [app/Providers]
     * These will be loaded first and last,
     * and will exclude from the standard providers classes loading.
     */
    protected $classes = [
        'priority_first' => [
            // Classes to load first
            \PluginFrame\Providers\Init::class,
            \PluginFrame\Providers\Activation::class, // keep this above
            // add yours
        ],
        'priority_last' => [
            // Classes to load last
            // add yours
            \PluginFrame\Providers\Deactivation::class, // Keep this last
        ],
    ];

    public function __construct()
    {
        // Load classes based on priority first
        $this->loadClasses($this->classes['priority_first']);
        
        // Dynamically load standard providers, excluding priority classes
        $this->loadProvidersClasses();

        // Load classes with priority last
        $this->loadClasses($this->classes['priority_last']);
    }

    /**
     * Instantiate each provider class.
     *
     * @param array $classes
     */
    public function loadClasses(array $classes)
    {
        foreach ($classes as $class) {
            if (class_exists($class)) {
                new $class();
            } else {
                error_log("Class not found: {$class}");
            }
        }
    }

    /**
     * Dynamically load classes from the app/Providers directory.
     * Excludes classes in priority_first and priority_last.
     */
    protected function loadProvidersClasses()
    {
        $directory = PLUGIN_FRAME_DIR . 'app/Providers/';
        $baseNamespace = 'PluginFrame\Providers';
    
        // Get all priority class names for exclusion
        $excludedClasses = array_merge(
            $this->classes['priority_first'],
            $this->classes['priority_last']
        );
    
        // Recursively scan the directory for provider files
        $files = $this->scanDirectory($directory);
    
        foreach ($files as $file) {
            // Strip the base directory and file extension
            $relativePath = substr($file, strlen($directory), -4); // Remove '.php'
    
            // Convert directory separators to namespace separators
            $relativeNamespace = str_replace(DIRECTORY_SEPARATOR, '\\', $relativePath);
    
            // Construct the full class name
            $className = $baseNamespace . '\\' . $relativeNamespace;

            // Skip excluded classes
            if (in_array($className, $excludedClasses, true)) {
                continue;
            }
    
            // Check if the class exists and instantiate it
            if (class_exists($className)) {
                new $className();
            } else {
                error_log("Provider class not found: {$className}");
            }
        }
    }
    
    /**
     * Recursively scan a directory for PHP files.
     *
     * @param string $directory
     * @return array
     */
    protected function scanDirectory(string $directory): array
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
    
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }
    
        return $files;
    }
    
}
