<?php

namespace PluginFrame\Core\Helpers;

use PluginFrame\Core\Services\Container;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ProvidersHelper
{
    /**
     * Load classes in the given priority group.
     *
     * @param array  $classes  Full providers list with 'priority_first'/'priority_last' keys.
     * @param string $priority 'priority_first' or 'priority_last'.
     */
    public function loadPriorityClasses(array $classes, string $priority): void
    {
        if (! isset($classes[$priority])) {
            error_log("Invalid priority group: {$priority}");
            return;
        }

        foreach ($classes[$priority] as $class) {
            $this->instantiateClass($class);
        }
    }

    /**
     * Scan app/Providers and instantiate each provider (excluding priority groups).
     *
     * @param array       $classes       Providers list with priority keys.
     * @param string|null $directory     Path to app/Providers (defaults to PLUGIN_FRAME_DIR . 'app/Providers/').
     * @param string|null $baseNamespace Base namespace for providers (defaults to 'PluginFrame\Providers').
     */
    public function loadProvidersClasses(
        array   $classes,
        ?string $directory     = null,
        ?string $baseNamespace = null
    ): void {
        $directory     = $directory     ?? PLUGIN_FRAME_DIR . 'app/Providers/';
        $baseNamespace = $baseNamespace ?? 'PluginFrame\Providers';

        $excluded = array_merge(
            $classes['priority_first'] ?? [],
            $classes['priority_last']  ?? []
        );

        foreach ($this->scanDirectory($directory) as $file) {
            $className = $this->constructClassName($file, $directory, $baseNamespace);
            if (in_array($className, $excluded, true)) {
                continue;
            }
            $this->instantiateClass($className);
        }
    }

    /**
     * Scan app/Views and instantiate each view class (autowiring dependencies).
     *
     * @param ?string|null $directory     Path to app/Views (defaults to PLUGIN_FRAME_DIR . 'app/Views/').
     * @param ?string|null $baseNamespace Base namespace for views (defaults to 'PluginFrame\Views').
     */
    public function loadViewClasses(
        ?string $directory     = null,
        ?string $baseNamespace = null
    ): void {
        $directory     = $directory     ?? PLUGIN_FRAME_DIR . 'app/Views/';
        $baseNamespace = $baseNamespace ?? 'PluginFrame\Views';

        foreach ($this->scanDirectory($directory) as $file) {
            $className = $this->constructClassName($file, $directory, $baseNamespace);
            $this->instantiateClass($className);
        }
    }

    /**
     * Convert a file path to the fully-qualified class name.
     *
     * @param string $file           Full filesystem path to the file.
     * @param string $directory      Base directory path.
     * @param string $baseNamespace  Corresponding PSR-4 base namespace.
     * @return string
     */
    protected function constructClassName(string $file, string $directory, string $baseNamespace): string
    {
        $relativePath      = substr($file, strlen($directory), -4); // strip ".php"
        $relativeNamespace = str_replace(DIRECTORY_SEPARATOR, '\\', $relativePath);
        return "{$baseNamespace}\\{$relativeNamespace}";
    }

    /**
     * Instantiate a class, injecting via the container if available,
     * then calling register() and/or boot() if present.
     */
    protected function instantiateClass(string $className): void
    {
        if (! class_exists($className)) {
            error_log("ProvidersHelper: Class not found: {$className}");
            return;
        }

        $container = Container::getInstance();

        // Autowiring fallback means get() will handle both bound and unbound classes
        $instance = $container->get($className);

        if (method_exists($instance, 'register')) {
            $instance->register();
        }
        if (method_exists($instance, 'boot')) {
            $instance->boot();
        }
    }

    /**
     * Recursively collect all .php files under a directory.
     *
     * @param string $directory
     * @return string[]
     */
    protected function scanDirectory(string $directory): array
    {
        $files = [];
        if (! is_dir($directory) || ! is_readable($directory)) {
            error_log("ProvidersHelper: Directory not found or not readable: {$directory}");
            return $files;
        }

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
