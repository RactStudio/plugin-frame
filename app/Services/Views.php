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
    public static function render(string $template, array $data = []): string
    {
        $loader = new FilesystemLoader( PLUGIN_FRAME_DIR . 'resources/views');
        $twig = new Environment($loader, [
            'cache' => PLUGIN_FRAME_DIR . 'storage',
            'auto_reload' => true,
        ]);

        // Add translation functions
        $twig->addFunction(new TwigFunction('__', function (string $text, string $domain = PLUGIN_FRAME_DOMAIN){
            return __($text, $domain);
        }));

        $twig->addFunction(new TwigFunction('_e', function (string $text, string $domain = PLUGIN_FRAME_DOMAIN) {
            _e($text, $domain);
        }));

        try {
            return $twig->render("$template.twig", $data);
        } catch (\Exception $e) {
            return '<p>Error rendering view: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
    }
}
