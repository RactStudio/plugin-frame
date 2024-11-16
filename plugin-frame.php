<?php
/**
 * Plugin Name: Plugin Frame
 * Plugin URI:  https://wp-plugin-frame.com
 * Description: A modern WordPress plugin development framework.
 * Version:     1.0.0
 * Author:      Mahamudul Hasan Rubel
 * Author URI:  https://mhrubel.com
 * Text Domain: plugin-frame
 * Domain Path: /languages
 * License:     LGPL-3.0-or-later
 */

namespace PluginFrame;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Define constants for plugin directory paths
define( 'PLUGIN_FRAME_NAME', 'Plugin Frame' );
define( 'PLUGIN_FRAME_VERSION', '1.0.0' );
define( 'PLUGIN_FRAME_DOMAIN', 'plugin-frame' );
define( 'PLUGIN_FRAME_DIR', plugin_dir_path( __FILE__ ) );
define( 'PLUGIN_FRAME_URL', plugin_dir_url( __FILE__ ) );

// Load The Plugin Frame
require_once PLUGIN_FRAME_DIR . 'config/Main.php';
new \PluginFrame\Main();