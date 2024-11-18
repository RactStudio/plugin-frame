<?php
/*
 * TwigStringExtractor.php
 *
 * This script is for development purposes only.
 *
 * It extracts translatable strings from Twig templates located in the `resources/views/` directory
 * and generates a temporary PHP file in the `languages/` directory containing these strings.
 *
 * Use this script before running the WordPress `wp i18n make-pot` command to include strings
 * from Twig files in the POT file.
 *
 * Execute this script as a standalone PHP file (e.g., `php TwigStringExtractor.php`) without loading WordPress.
 */

namespace PluginFrame;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

define('PLUGIN_FRAME_DIR', __DIR__ . '/');
define('LANGUAGE_DIR', PLUGIN_FRAME_DIR . 'languages/');
define('VIEWS_DIR', PLUGIN_FRAME_DIR . 'resources/views');
define('TEMP_TRANSLATIONS_FILE', LANGUAGE_DIR . 'temp-twig-strings.php');

// Ensure Composer's autoload is loaded
if (file_exists(PLUGIN_FRAME_DIR . 'vendor/autoload.php')) {
    require_once PLUGIN_FRAME_DIR . 'vendor/autoload.php';
} else {
    echo "Error: Composer dependencies not installed. Run `composer install`.\n";
    return;
}

// Define mock WordPress translation functions
if (!function_exists('__')) {
    function __(string $text, string $domain = 'plugin-frame'): string
    {
        return $text; // Return the original text for extraction purposes
    }
}

if (!function_exists('_e')) {
    function _e(string $text, string $domain = 'plugin-frame'): string
    {
        return $text; // Output the original text
    }
}

if (!function_exists('_n')) {
    function _n(string $single, string $plural, int $number, string $domain = 'plugin-frame'): string
    {
        return $number === 1 ? $single : $plural; // Return singular or plural form
    }
}

if (!function_exists('_x')) {
    function _x(string $text, string $context, string $domain = 'plugin-frame'): string
    {
        return $text; // Return the text for extraction purposes
    }
}

class TwigStringExtractor
{
    private $twig;

    public function __construct()
    {
        echo "[INFO] Initializing Twig environment...\n";
        
        // Load templates from resources/views and its subdirectories
        $loader = new FilesystemLoader(VIEWS_DIR);
        $this->twig = new Environment($loader);

        // Add WordPress translation functions to Twig
        $this->addWpTranslationFunctions();
    }

    public function extractTranslations()
    {
        echo "[INFO] Starting extraction...\n";

        if (!is_dir(VIEWS_DIR)) {
            echo "[ERROR] Views directory not found: " . VIEWS_DIR . "\n";
            return;
        }

        if (!is_dir(LANGUAGE_DIR)) {
            echo "[INFO] Creating languages directory: " . LANGUAGE_DIR . "\n";
            if (!mkdir(LANGUAGE_DIR, 0755, true)) {
                echo "[ERROR] Failed to create languages directory.\n";
                return;
            }
        }

        if (file_exists(TEMP_TRANSLATIONS_FILE)) {
            echo "[INFO] Resetting existing temp file: " . TEMP_TRANSLATIONS_FILE . "\n";
            unlink(TEMP_TRANSLATIONS_FILE);
        }

        if (file_put_contents(TEMP_TRANSLATIONS_FILE, "<?php\n\n") === false) {
            echo "[ERROR] Unable to create temp file: " . TEMP_TRANSLATIONS_FILE . "\n";
            return;
        }

        $twigFiles = $this->getAllTwigFiles(VIEWS_DIR);

        foreach ($twigFiles as $file) {
            echo "[INFO] Processing Twig file: $file\n";
            $this->processTwigFile($file);
        }

        echo "[INFO] Extraction completed.\n";
    }

    private function getAllTwigFiles($directory)
    {
        $twigFiles = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS));
        foreach ($iterator as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'twig') {
                $twigFiles[] = $file->getPathname();
            }
        }
        return $twigFiles;
    }

    private function processTwigFile($templatePath)
    {
        $templatePath = str_replace(VIEWS_DIR, '', $templatePath);

        try {
            $template = $this->twig->load($templatePath);
            $renderedContent = $template->render([
                'description' => 'Placeholder',
                'title' => 'Placeholder Title',
            ]);
            $this->extractTranslatableStrings($renderedContent);
        } catch (\Exception $e) {
            echo "[ERROR] Failed to process $templatePath: " . $e->getMessage() . "\n";
        }
    }

    private function extractTranslatableStrings($content)
    {
        // Updated regex to match translation functions with optional arguments
        preg_match_all(
            '/\{\{\s*(_{1,2}|_e|_n|_x)\(\s*[\'"](.+?)[\'"]\s*(?:,\s*[\'"](.+?)[\'"])?(?:,\s*[\'"](.+?)[\'"])?\s*\)\s*\}\}/',
            $content,
            $matches,
            PREG_SET_ORDER
        );
    
        if (!empty($matches)) {
            foreach ($matches as $match) {
                $function = $match[1]; // The function name: __, _e, _n, or _x
                $text = $match[2];    // The main string
                $context = $match[3] ?? ''; // Optional context
                $domain = $match[4] ?? 'plugin-frame'; // Optional domain
    
                // Append extracted string based on the function
                if ($function === '_n') {
                    // Handle plural forms
                    $this->appendToTempFile($match[2], $domain);  // Singular string
                    $this->appendToTempFile($match[3], $domain);  // Plural string
                } elseif ($function === '_x') {
                    // Handle contextual translation
                    $this->appendToTempFile($text . '|_x_context:' . $context, $domain);
                } else {
                    // Generic translation functions (__ and _e)
                    $this->appendToTempFile($text, $domain);
                }
            }
        } else {
            echo "[INFO] No translatable strings found in content.\n";
        }
    }    

    private function appendToTempFile($text, $domain)
    {
        static $uniqueStrings = [];
        $key = md5($text . $domain);
    
        if (isset($uniqueStrings[$key])) {
            echo "[INFO] Skipping duplicate string: '$text' (Domain: '$domain')\n";
            return;
        }
    
        $uniqueStrings[$key] = true;
    
        $entry = sprintf("__('%s', '%s');\n", addslashes($text), $domain);
        if (file_put_contents(TEMP_TRANSLATIONS_FILE, $entry, FILE_APPEND | LOCK_EX) === false) {
            echo "[ERROR] Failed to write string: '$text' to temp file.\n";
        } else {
            echo "[INFO] Extracted string: '$text' (Domain: '$domain')\n";
        }
    }    

    private function addWpTranslationFunctions()
    {
        // Add WordPress translation functions with optional `context` and `domain` parameters
        $this->twig->addFunction(new TwigFunction('__', function (string $text, string $domain = 'plugin-frame') {
            return __($text, $domain);  // Return translated text
        }));
    
        $this->twig->addFunction(new TwigFunction('_e', function (string $text, string $domain = 'plugin-frame') {
            return __($text, $domain);  // Echo translated text
        }));
    
        $this->twig->addFunction(new TwigFunction('_n', function (string $single, string $plural, int $number, string $domain = 'plugin-frame') {
            return _n($single, $plural, $number, $domain);  // Handle plural translation
        }));
    
        $this->twig->addFunction(new TwigFunction('_x', function (string $text, string $context = '', string $domain = 'plugin-frame') {
            return _x($text, $context, $domain);  // Contextual translation
        }));
    }
    
}

// Execute the extractor
$extractor = new TwigStringExtractor();
$extractor->extractTranslations();
