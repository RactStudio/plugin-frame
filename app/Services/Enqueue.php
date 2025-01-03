<?php

namespace PluginFrame\Services;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Enqueue
{
    /**
     * Register a frontend style.
     *
     * @param string $handle
     * @param string $src
     * @param array $deps
     * @param string|bool $ver
     * @param callable|null $condition
     */
    public function registerFrontendStyle($handle, $src, $deps = [], $ver = false, $inFooter = false, $condition = null)
    {
        add_action('wp_enqueue_scripts', function () use ($handle, $src, $deps, $ver, $inFooter, $condition) {
            if (!$condition || (is_callable($condition) && $condition())) {
                wp_enqueue_style($handle, $src, $deps, $ver, $inFooter);
            }
        });
    }

    /**
     * Register a frontend script
     *
     * @param string $handle
     * @param string $src
     * @param array $deps
     * @param string|bool $ver
     * @param bool $inFooter
     * @param callable|null $condition
     * @param string $tagAttribute 'defer', 'async', or ''
     */
    public function registerFrontendScript($handle, $src, $deps = [], $ver = false, $inFooter = false, $condition = null, $tagAttribute = 'non')
    {
        add_action('wp_enqueue_scripts', function () use ($handle, $src, $deps, $ver, $inFooter, $condition, $tagAttribute) {
            if (!$condition || (is_callable($condition) && $condition())) {
                wp_enqueue_script($handle, $src, $deps, $ver, $inFooter);

                if ($tagAttribute !== '' && in_array($tagAttribute, ['defer', 'async'])) {
                    add_filter('script_loader_tag', function ($tag, $enqueuedHandle) use ($handle, $tagAttribute) {
                        if ($enqueuedHandle === $handle) {
                            return str_replace(' src', " $tagAttribute src", $tag);
                        }
                        return $tag;
                    }, 10, 2);
                }
            }
        });
    }

    /**
     * Register an admin style.
     *
     * @param string $handle
     * @param string $src
     * @param array $deps
     * @param string|bool $ver
     * @param callable|null $condition
     */
    public function registerAdminStyle($handle, $src, $deps = [], $ver = false, $inFooter = false, $condition = null)
    {
        add_action('admin_enqueue_scripts', function () use ($handle, $src, $deps, $ver, $inFooter, $condition) {
            if (!$condition || (is_callable($condition) && $condition())) {
                wp_enqueue_style($handle, $src, $deps, $ver, $inFooter);
            }
        });
    }

    /**
     * Register an admin script
     *
     * @param string $handle
     * @param string $src
     * @param array $deps
     * @param string|bool $ver
     * @param bool $inFooter
     * @param callable|null $condition
     * @param string $tagAttribute 'defer', 'async', or ''
     */
    public function registerAdminScript($handle, $src, $deps = [], $ver = false, $inFooter = false, $condition = null, $tagAttribute = 'non')
    {
        add_action('admin_enqueue_scripts', function () use ($handle, $src, $deps, $ver, $inFooter, $condition, $tagAttribute) {
            if (!$condition || (is_callable($condition) && $condition())) {
                wp_enqueue_script($handle, $src, $deps, $ver, $inFooter);

                if ($tagAttribute !== '' && in_array($tagAttribute, ['defer', 'async'])) {
                    add_filter('script_loader_tag', function ($tag, $enqueuedHandle) use ($handle, $tagAttribute) {
                        if ($enqueuedHandle === $handle) {
                            return str_replace(' src', " $tagAttribute src", $tag);
                        }
                        return $tag;
                    }, 10, 2);
                }
            }
        });
    }

}
