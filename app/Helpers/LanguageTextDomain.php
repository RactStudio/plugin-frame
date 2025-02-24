<?php

namespace PluginFrame\Helpers;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

class LanguageTextDomain {
    public function load_textdomain() {
        $lang_dir = dirname( PLUGIN_FRAME_BASENAME ) . '/languages';
        load_plugin_textdomain( 'plugin-frame', false, $lang_dir );
    }
}