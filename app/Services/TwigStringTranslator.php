<?php

namespace PluginFrame\Services;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

/**
 * Class TwigStringTranslator
 * Handles dynamic extraction of translatable strings from Twig templates.
 */
class TwigStringTranslator
{
    private $tempFilePath;
    private $uniqueTranslations = [];

    public function __construct()
    {
        $this->tempFilePath = PLUGIN_FRAME_DIR . 'languages/temp-translations.php';

        // Reset the temp file at the beginning of a session
        if (defined('PF_TWIG_STRING_TRANSLATOR') && PF_TWIG_STRING_TRANSLATOR) {
            $this->resetTempFile();
        }
    }

    /**
     * Add a translatable string to the temporary file.
     *
     * @param string $text Translatable string.
     * @param string $domain Text domain.
     */
    public function addTranslation(string $text, string $domain)
    {
        // Check if the feature is enabled
        if (!defined('PF_TWIG_STRING_TRANSLATOR') || !PF_TWIG_STRING_TRANSLATOR) {
            return;
        }

        // Generate unique key for the string
        $key = md5($text . $domain);

        // Avoid duplicates
        if (isset($this->uniqueTranslations[$key])) {
            $this->debugLog("Skipped duplicate string: '$text' with domain '$domain'.");
            return;
        }

        // Mark as logged
        $this->uniqueTranslations[$key] = true;

        // Prepare entry
        $entry = sprintf("__('%s', '%s');\n", addslashes($text), $domain);

        // Append to temp file
        file_put_contents($this->tempFilePath, $entry, FILE_APPEND | LOCK_EX);

        $this->debugLog("Added string: '$text' with domain '$domain'.");
    }

    /**
     * Reset the temporary file (clears content).
     */
    private function resetTempFile()
    {
        file_put_contents($this->tempFilePath, "<?php\n\n", LOCK_EX);
        $this->debugLog("Temporary file reset: {$this->tempFilePath}");
    }

    /**
     * Clear the temporary file (delete the file).
     */
    public function clearTempFile()
    {
        if (file_exists($this->tempFilePath)) {
            unlink($this->tempFilePath);
            $this->debugLog("Temporary file cleared: {$this->tempFilePath}");
        }
    }

    /**
     * Debug log for development mode.
     *
     * @param string $message Log message.
     */
    private function debugLog($message)
    {
        if (defined('WP_DEBUG') && WP_DEBUG && defined('PF_TWIG_STRING_TRANSLATOR') && PF_TWIG_STRING_TRANSLATOR) {
            error_log("[TwigStringTranslator] $message");
        }
    }
}
