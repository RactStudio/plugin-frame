<?php

namespace PluginFrame\Services;

use Exception;
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
     * @param   string  $template The relative path to the template (without extension).
     * @param   string  $extension Optional - The extension of the template.
     * @param   array   $data Data to pass to the template, default is `twig`.
     * @param   string  $rootLocation Optional - `resources/views/` is default.
     * @return  string  Rendered HTML.
     */
    public static function render(string $template, string $extension = 'twig', array $data = [], string $rootLocation = 'resources/views/')
    {
        // Set up the loader to scan all subdirectories inside 'resources/views/' or provided $rootLocation
        $loader = new FilesystemLoader( PLUGIN_FRAME_DIR . $rootLocation );
        
        // Initialize the Twig environment with cache and auto-reload
        $twig = new Environment($loader, [
            'cache' => PLUGIN_FRAME_DIR . 'cache/twig',
            'auto_reload' => true,
        ]);

        // Add translation functions
        $twig->addFunction(new TwigFunction('__', function (string $text, string $domain = 'plugin-frame') {
            return __($text, $domain);  // Return translated text
        }));

        $twig->addFunction(new TwigFunction('_e', function (string $text, string $domain = 'plugin-frame') {
            return __($text, $domain);  // Do not echo translated text
        }));

        $twig->addFunction(new TwigFunction('_n', function (string $single, string $plural, int $number, string $domain = 'plugin-frame') {
            return _n($single, $plural, $number, $domain);  // Handle plural translation
        }));

        $twig->addFunction(new TwigFunction('_x', function (string $text, string $context, string $domain = 'plugin-frame') {
            return _x($text, $context, $domain);  // Contextual translation
        }));

        try {
            // Render the template
            return $twig->render("$template.{$extension}", $data);
        } catch (Exception $e) {
            return '<p>Error rendering twig view: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
    }
}
