<?php

namespace PluginFrame;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class LoadProviders
{
    /**
     * Add each provider classes manually.
     * These will be loaded first and last,
     * and will exclude from the standard providers classes loading.
     */
    protected $providers = [
        'priority_first' => [
            // Providers to load first
            \PluginFrame\Providers\Init::class,
        ],
        'priority_last' => [
            // Providers to load last
            //\PluginFrame\Providers\EnqueueAssets::class,
        ],
    ];

    public function __construct()
    {
        // Load providers based on priority first
        $this->loadProviders($this->providers['priority_first']);
        
        // Dynamically load standard providers, excluding priority classes
        $this->loadStandardProviders();

        // Load providers with priority last
        $this->loadProviders($this->providers['priority_last']);
    }

    /**
     * Instantiate each provider class.
     *
     * @param array $providers
     */
    public function loadProviders(array $providers)
    {
        foreach ($providers as $provider) {
            if (class_exists($provider)) {
                new $provider();
            } else {
                error_log("Provider class not found: {$provider}");
            }
        }
    }

    /**
     * Dynamically load providers from the app/Providers directory.
     * Excludes classes in priority_first and priority_last.
     */
    protected function loadStandardProviders()
    {
        $directory = PLUGIN_FRAME_DIR . 'app/Providers/';
        $baseNamespace = 'PluginFrame\Providers';
    
        // Get all priority class names for exclusion
        $excludedClasses = array_merge(
            $this->providers['priority_first'],
            $this->providers['priority_last']
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
