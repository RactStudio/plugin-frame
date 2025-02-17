<?php

namespace PluginFrame\Services;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Enqueue
{
    /**
     * Register a style for a given hook.
     *
     * @param string        $hook       The action hook to enqueue the style.
     *                                  For frontend styles, use 'wp_enqueue_scripts'.
     *                                  For admin styles, use 'admin_enqueue_scripts'.
     * @param string        $handle     The unique identifier for the style.
     * @param string        $src        The URL to the style.
     * @param array         $deps       Optional. An array of registered style handles this style depends on.
     * @param string|bool   $ver        Optional. Style version number, if it has one.
     * @param string        $media      Optional. The media for which this stylesheet has been defined. Defaults to 'all'.
     * @param callable|null $condition  Optional. A callable that determines if the style should be enqueued.
     * @param array|null    $attributes Optional. An associative array of additional attributes to add to the <link> tag.
     *                                  Each key is an attribute name and its value is the attribute value.
     *                                  If the value is empty, the attribute will be rendered without a value.
     */
    protected function registerStyle($hook, $handle, $src, $deps = [], $ver = false, $media = 'all', $condition = null, $attributes = null)
    {
        add_action($hook, function () use ($handle, $src, $deps, $ver, $media, $condition, $attributes) {
            if (!$condition || (is_callable($condition) && $condition())) {
                wp_enqueue_style($handle, $src, $deps, $ver, $media);

                if (!empty($attributes) && is_array($attributes)) {
                    add_filter('style_loader_tag', function ($tag, $handle2) use ($handle, $attributes) {
                        if ($handle2 === $handle) {
                            $attrString = '';
                            foreach ($attributes as $attr => $val) {
                                if (trim((string)$val) === '') {
                                    $attrString .= " {$attr}";
                                } else {
                                    $attrString .= " {$attr}=\"" . esc_attr($val) . "\"";
                                }
                            }
                            // Insert the attributes into the opening <link ...> tag.
                            return str_replace('<link ', "<link{$attrString} ", $tag);
                        }
                        return $tag;
                    }, 10, 2);
                }
            }
        });
    }

    /**
     * Register a frontend style.
     *
     * @param string        $handle     The unique identifier for the style.
     * @param string        $src        The URL to the style.
     * @param array         $deps       Optional. An array of registered style handles this style depends on.
     * @param string|bool   $ver        Optional. Style version number, if it has one.
     * @param string        $media      Optional. The media for which this stylesheet has been defined. Defaults to 'all'.
     * @param callable|null $condition  Optional. A callable that determines if the style should be enqueued.
     * @param array|null    $attributes Optional. An associative array of additional attributes to add to the <link> tag.
     *                                  Each key is an attribute name and its value is the attribute value.
     *                                  If the value is empty, the attribute will be rendered without a value.
     */
    public function registerFrontendStyle($handle, $src, $deps = [], $ver = false, $media = 'all', $condition = null, $attributes = null)
    {
        $this->registerStyle('wp_enqueue_scripts', $handle, $src, $deps, $ver, $media, $condition, $attributes);
    }

    /**
     * Register an admin style.
     *
     * @param string        $handle     The unique identifier for the style.
     * @param string        $src        The URL to the style.
     * @param array         $deps       Optional. An array of registered style handles this style depends on.
     * @param string|bool   $ver        Optional. Style version number, if it has one.
     * @param string        $media      Optional. The media for which this stylesheet has been defined. Defaults to 'all'.
     * @param callable|null $condition  Optional. A callable that determines if the style should be enqueued.
     * @param array|null    $attributes Optional. An associative array of additional attributes to add to the <link> tag.
     *                                  Each key is an attribute name and its value is the attribute value.
     *                                  If the value is empty, the attribute will be rendered without a value.
     */
    public function registerAdminStyle($handle, $src, $deps = [], $ver = false, $media = 'all', $condition = null, $attributes = null)
    {
        $this->registerStyle('admin_enqueue_scripts', $handle, $src, $deps, $ver, $media, $condition, $attributes);
    }

    /**
     * Register a script for a given hook.
     *
     * @param string        $hook       The action hook to enqueue the script. e.g. 'wp_enqueue_scripts' or 'admin_enqueue_scripts'.
     * @param string        $handle     The unique identifier for the script.
     * @param string        $src        The URL to the script.
     * @param array         $deps       Optional. An array of registered script handles this script depends on.
     * @param string|bool   $ver        Optional. Script version number, if it has one.
     * @param bool          $inFooter   Optional. Whether to enqueue the script before </body> instead of in the <head>.
     * @param callable|null $condition  Optional. A callable that determines if the script should be enqueued.
     * @param array|null    $attributes Optional. An associative array of additional attributes to add to the script tag.
     *                                  Each key is an attribute name and the corresponding value is the attribute value.
     *                                  If the value is empty, the attribute will be rendered without a value.
     */
    protected function registerScript($hook, $handle, $src, $deps = [], $ver = false, $inFooter = false, $condition = null, $attributes = null)
    {
        add_action($hook, function () use ($handle, $src, $deps, $ver, $inFooter, $condition, $attributes) {
            if (!$condition || (is_callable($condition) && $condition())) {
                wp_enqueue_script($handle, $src, $deps, $ver, $inFooter);

                if (!empty($attributes) && is_array($attributes)) {
                    add_filter('script_loader_tag', function ($tag, $enqueuedHandle) use ($handle, $attributes) {
                        if ($enqueuedHandle === $handle) {
                            $attrString = '';
                            foreach ($attributes as $attr => $val) {
                                // If the attribute's value is empty, output only the attribute name.
                                if (trim((string)$val) === '') {
                                    $attrString .= " {$attr}";
                                } else {
                                    $attrString .= " {$attr}=\"" . esc_attr($val) . "\"";
                                }
                            }
                            // Inject the attribute string into the opening <script ...> tag.
                            return str_replace('<script ', "<script{$attrString} ", $tag);
                        }
                        return $tag;
                    }, 10, 2);
                }
            }
        });
    }

    /**
     * Register a frontend script.
     *
     * @param string        $handle     The unique identifier for the script.
     * @param string        $src        The URL to the script.
     * @param array         $deps       Optional. An array of registered script handles this script depends on.
     * @param string|bool   $ver        Optional. Script version number, if it has one.
     * @param bool          $inFooter   Optional. Whether to enqueue the script before </body> instead of in the <head>.
     * @param callable|null $condition  Optional. A callable that determines if the script should be enqueued.
     * @param array|null    $attributes Optional. An associative array of additional attributes to add to the script tag.
     *                                  Each key is an attribute name and the value is the attribute value.
     *                                  If the value is empty, the attribute will be rendered without a value.
     */
    public function registerFrontendScript($handle, $src, $deps = [], $ver = false, $inFooter = false, $condition = null, $attributes = null)
    {
        $this->registerScript('wp_enqueue_scripts', $handle, $src, $deps, $ver, $inFooter, $condition, $attributes);
    }

    /**
     * Register an admin script.
     *
     * @param string        $handle     The unique identifier for the script.
     * @param string        $src        The URL to the script.
     * @param array         $deps       Optional. An array of registered script handles this script depends on.
     * @param string|bool   $ver        Optional. Script version number, if it has one.
     * @param bool          $inFooter   Optional. Whether to enqueue the script before </body> instead of in the <head>.
     * @param callable|null $condition  Optional. A callable that determines if the script should be enqueued.
     * @param array|null    $attributes Optional. An associative array of additional attributes to add to the script tag.
     *                                  Each key is an attribute name and the value is the attribute value.
     *                                  If the value is empty, the attribute will be rendered without a value.
     */
    public function registerAdminScript($handle, $src, $deps = [], $ver = false, $inFooter = false, $condition = null, $attributes = null)
    {
        $this->registerScript('admin_enqueue_scripts', $handle, $src, $deps, $ver, $inFooter, $condition, $attributes);
    }

}
