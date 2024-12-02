<?php

namespace PluginFrame;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Config
{
    public function __construct()
    {
        // Defined global constant of Plugin Frame
        $this->pf_defined();
        // True/False WordPress default features
        $this->wp_default_features();
    }

    public function pf_defined()
    {
        // Define constants for plugin directory paths
        define( 'PLUGIN_FRAME_NAME', 'Plugin Frame' ); // Required
        define( 'PLUGIN_FRAME_VERSION', '1.0.0' ); // Required
        define( 'PLUGIN_FRAME_DIR', \plugin_dir_path( __FILE__ . '../' ) ); // Required
        define( 'PLUGIN_FRAME_URL', \plugin_dir_url( __FILE__. '../' ) ); // Required
        define( 'PLUGIN_FRAME_FILE', __FILE__ ); // Required
        define( 'PLUGIN_FRAME_BASENAME', \plugin_basename( __FILE__. '../' ) ); // Required
        define( 'PLUGIN_FRAME_SLUG', 'plugin-frame' ); // Required
        define( 'PLUGIN_FRAME_PREFIX', 'plugin_frame' ); // Required
        define( 'PLUGIN_FRAME_PREFIX_SNAKE', 'plugin_frame_' );
        define( 'PLUGIN_FRAME_PREFIX_CAMEL', 'pluginFrame' );
    }

    public function wp_default_features()
    {
        return [
            'post-thumbnails' =>  true,
            'title_tag' =>  true,
            'automatic_feed_links' =>  true,
            'wp-editor' =>  true,
        ];
    }

}