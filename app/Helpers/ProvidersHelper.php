<?php

namespace PluginFrame\Helpers;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class ProvidersHelper
{
    /**
     * Load priority classes (first or last).
     *
     * @param string $priority Key for the priority group ('priority_first' or 'priority_last').
     */
    public function loadPriorityClasses($classes, string $priority): void
    {
        if (!isset($classes[$priority])) {
            error_log("Invalid priority group: {$priority}");
            return;
        }

        foreach ($classes[$priority] as $class) {
            $this->instantiateClass($class);
        }
    }

    /**
     * Dynamically load classes from the app/Providers directory.
     * Excludes classes in priority_first and priority_last.
     */
    public function loadProvidersClasses($classes, $directory = null, $baseNamespace = null): void
    {
        if ($directory === null) {
            $directory = PLUGIN_FRAME_DIR . 'app/Providers/';
        }
        if ($baseNamespace === null) {
            $baseNamespace = 'PluginFrame\Providers';
        }

        // Get all priority class names for exclusion
        $excludedClasses = array_merge(
            $classes['priority_first'],
            $classes['priority_last']
        );

        // Recursively scan the directory for provider files
        $files = $this->scanDirectory($directory);

        foreach ($files as $file) {
            $className = $this->constructClassName($file, $directory, $baseNamespace);

            // Skip excluded classes
            if (in_array($className, $excludedClasses, true)) {
                continue;
            }

            $this->instantiateClass($className);
        }
    }

    /**
     * Construct a fully qualified class name from a file path.
     *
     * @param string $file The full file path.
     * @param string $directory The base directory.
     * @param string $baseNamespace The base namespace.
     * @return string The fully qualified class name.
     */
    protected function constructClassName(string $file, string $directory, string $baseNamespace): string
    {
        // Strip the base directory and file extension
        $relativePath = substr($file, strlen($directory), -4); // Remove '.php'

        // Convert directory separators to namespace separators
        $relativeNamespace = str_replace(DIRECTORY_SEPARATOR, '\\', $relativePath);

        // Construct the full class name
        return $baseNamespace . '\\' . $relativeNamespace;
    }

    /**
     * Instantiate a class if it exists.
     *
     * @param string $className The fully qualified class name.
     */
    protected function instantiateClass(string $className): void
    {
        if (class_exists($className)) {
            new $className();
        } else {
            error_log("Class not found: {$className}");
        }
    }

    /**
     * Recursively scan a directory for PHP files.
     *
     * @param string $directory The directory to scan.
     * @return array An array of file paths.
     */
    protected function scanDirectory(string $directory): array
    {
        $files = [];

        // Ensure the directory exists and is readable
        if (!is_dir($directory) || !is_readable($directory)) {
            error_log("Directory not found or not readable: {$directory}");
            return $files;
        }

        // Recursively scan the directory
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