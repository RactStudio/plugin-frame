<?php

namespace PluginFrame\Config;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Config
{
    // Load WordPress default features on priority first
    public function priority_load_first()
    {
        // Fires when the plugin sarted loading classes
        do_action( 'plugin_frame_default_features_first_start' );

        // True/False WordPress default features
        $this->wp_default_features_first();

        // Fires when the plugin finishes loading classes
        do_action( 'plugin_frame_default_features_first_end' );

    }

    // Load WordPress default features on priority last
    public function priority_load_last()
    {
        // Fires when the plugin sarted loading classes
        do_action( 'plugin_frame_default_features_last_start' );

        // True/False WordPress default features
        $this->wp_default_features_last();

        // Fires when the plugin finishes loading classes
        do_action( 'plugin_frame_default_features_last_end' );

    }

    // Active / Deactive WordPress default features
    private function wp_default_features_first(): array
    {
        return [
            'post-thumbnails' =>  true,
            'title_tag' =>  true,
            'automatic_feed_links' =>  true,
            'wp-editor' =>  true,
        ];
    }

    // Active / Deactive WordPress default features
    private function wp_default_features_last(): array
    {
        return [
            'post-thumbnails' =>  true,
            'title_tag' =>  true,
            'automatic_feed_links' =>  true,
            'wp-editor' =>  true,
        ];
    }

}