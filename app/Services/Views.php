<?php

namespace PluginFrame\Services;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Views
{
    /**
     * Render a Twig template.
     *
     * @param string $template The relative path to the template (without extension).
     * @param array $data Data to pass to the template.
     * @return string Rendered HTML.
     */
    public static function render(string $template, array $data = [])
    {
        // Set up the loader to scan all subdirectories inside resources/views
        $loader = new FilesystemLoader( PLUGIN_FRAME_DIR . 'resources/views' );
        
        // Initialize the Twig environment with cache and auto-reload
        $twig = new Environment($loader, [
            'cache' => PLUGIN_FRAME_DIR . 'storage',
            'auto_reload' => true,
        ]);

        // Add translation functions
        $twig->addFunction(new TwigFunction('__', function (string $text, string $domain = 'plugin-frame') {
            return __($text, $domain);  // Return translated text
        }));

        $twig->addFunction(new TwigFunction('_e', function (string $text, string $domain = 'plugin-frame') {
            echo __($text, $domain);  // Echo translated text
        }));

        $twig->addFunction(new TwigFunction('_n', function (string $single, string $plural, int $number, string $domain = 'plugin-frame') {
            return _n($single, $plural, $number, $domain);  // Handle plural translation
        }));

        $twig->addFunction(new TwigFunction('_x', function (string $text, string $context, string $domain = 'plugin-frame') {
            return _x($text, $context, $domain);  // Contextual translation
        }));

        try {
            // Render the template, adding the .twig extension automatically
            return $twig->render("$template.twig", $data);
        } catch (\Exception $e) {
            return '<p>Error rendering view: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
    }
}
