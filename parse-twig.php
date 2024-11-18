<?php

// Autoload Composer dependencies
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    die("Composer dependencies are not installed. Please run `composer install`.\n");
}

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

// Mock WordPress translation functions for standalone execution
if (!function_exists('__')) {
    function __($text, $domain = 'default')
    {
        return $text; // Simply return the text as-is
    }
}
if (!function_exists('_e')) {
    function _e($text, $domain = 'default')
    {
        echo $text; // Echo the text as-is
    }
}

function parse_twig_translations($twig_directory, $temp_php_file)
{
    if (!class_exists(Environment::class)) {
        die("Twig is not available. Ensure Twig is installed via Composer.\n");
    }

    // Initialize Twig
    $loader = new FilesystemLoader($twig_directory);
    $twig = new Environment($loader);

    // Add custom translation functions
    $twig->addFunction(new TwigFunction('__', function ($text, $domain) {
        return __($text, $domain);
    }));
    $twig->addFunction(new TwigFunction('_e', function ($text, $domain) {
        return _e($text, $domain);
    }));

    // Collect all .twig files in the directory and subdirectories
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($twig_directory, RecursiveDirectoryIterator::SKIP_DOTS)
    );

    $output = "<?php\n\n";

    foreach ($files as $file) {
        // Ensure only .twig files are processed
        if (pathinfo($file, PATHINFO_EXTENSION) !== 'twig') {
            continue;
        }

        // Get the relative file path
        $template_path = str_replace($twig_directory . DIRECTORY_SEPARATOR, '', $file->getPathname());

        // Render the template with placeholder values
        try {
            $template = $twig->load($template_path);
            $rendered_content = $template->render([
                'description' => 'Description Placeholder',
                'title' => 'Title Placeholder',
                // Add more placeholder values as needed
            ]);

            // Extract translation strings
            preg_match_all(
                '/\{\{\s*__\(\s*[\'"](.+?)[\'"]\s*,\s*[\'"](.+?)[\'"]\s*\)\s*\}\}/',
                $rendered_content,
                $matches
            );
            foreach ($matches[1] as $index => $text) {
                $domain = $matches[2][$index];
                $output .= "_e('$text', '$domain');\n";
            }

            preg_match_all(
                '/\{\{\s*_e\(\s*[\'"](.+?)[\'"]\s*,\s*[\'"](.+?)[\'"]\s*\)\s*\}\}/',
                $rendered_content,
                $matches
            );
            foreach ($matches[1] as $index => $text) {
                $domain = $matches[2][$index];
                $output .= "_e('$text', '$domain');\n";
            }
        } catch (Exception $e) {
            echo "Error processing template {$template_path}: {$e->getMessage()}\n";
            continue;
        }
    }

    // Write the output to the temp PHP file
    file_put_contents($temp_php_file, $output);

    echo "Temporary translation file created at $temp_php_file\n";
}

// Define paths
$twig_directory = __DIR__ . '/resources/views';
$temp_php_file = __DIR__ . '/languages/temp-translations.php';

// Parse Twig translations
parse_twig_translations($twig_directory, $temp_php_file);
