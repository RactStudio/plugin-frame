<?php

namespace PluginFrame\Core\Services;

use Exception;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;
use PluginFrame\Core\Services\Options\OptionManager;
use PluginFrame\Core\Services\Container;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Views
{
    /**
     * Render a Twig template.
     *
     * @param   string  $template     Relative path to the template (no extension).
     * @param   string  $extension    Template extension (default: 'twig').
     * @param   array   $data         Variables to pass to the template.
     * @param   string  $rootLocation Filesystem path under PLUGIN_FRAME_DIR (default: 'resources/views/').
     * @return  string  Rendered HTML.
     */
    public static function render(
        string $template,
        string $extension = 'twig',
        array $data = [],
        string $rootLocation = 'resources/views/'
    ): string {
        // 1) Set up Twig loader & environment
        $loader = new FilesystemLoader( PLUGIN_FRAME_DIR . $rootLocation );
        $twig   = new Environment($loader, [
            'cache'       => PLUGIN_FRAME_DIR . 'cache/twig',
            'auto_reload' => true,
        ]);

        // 2) Translation functions
        $twig->addFunction(new TwigFunction('__', fn(string $text, string $domain='plugin-frame') => __($text,$domain)));
        $twig->addFunction(new TwigFunction('_e', fn(string $text, string $domain='plugin-frame') => _e($text,$domain)));
        $twig->addFunction(new TwigFunction('_n', fn(string $single, string $plural, int $number, string $domain='plugin-frame') => _n($single,$plural,$number,$domain)));
        $twig->addFunction(new TwigFunction('_x', fn(string $text, string $context, string $domain='plugin-frame') => _x($text,$context,$domain)));
 
        // 3) Option retrieval function
        $twig->addFunction(new TwigFunction(
            'option',
            /**
             * Fetch a registered option value.
             *
             * @param string     $key     Option name.
             * @param mixed|null $default Default if not found.
             * @return mixed
             */
            function(string $key, $default = null) {
                /** @var OptionManager $om */
                $om = Container::getInstance()->get(OptionManager::class);
                return $om->get($key, $default);
            }
        ));

        try {
            // 4) Render and return
            return $twig->render("$template.{$extension}", $data);
        } catch (Exception $e) {
            return '<p>Error rendering twig view: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
    }
}
