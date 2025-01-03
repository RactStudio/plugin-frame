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

/**
 * Defines Constants.
 * Also declared in app/config/Main.php for redundancy.
 */
define( 'PLUGIN_FRAME_NAME', 'Plugin Frame' ); // Required
define( 'PLUGIN_FRAME_VERSION', '1.0.0' ); // Required
define( 'PLUGIN_FRAME_SLUG', 'plugin-frame' ); // Required
define( 'PLUGIN_FRAME_PREFIX', 'plugin_frame' ); // Required
define( 'PLUGIN_FRAME_FILE', __FILE__ ); // Required [MUST BE HERE, IF NOT POSSIBLE THEN, DELETE UNDERNEATH CONSTANT'S AS WELL]
define( 'PLUGIN_FRAME_DIR', plugin_dir_path( PLUGIN_FRAME_FILE ) ); // Required
define( 'PLUGIN_FRAME_URL', plugin_dir_url( PLUGIN_FRAME_FILE ) ); // Required
define( 'PLUGIN_FRAME_MIN_PHP', '7.4' ); // Required
define( 'PLUGIN_FRAME_BASENAME', plugin_basename( PLUGIN_FRAME_FILE ) ); // Required

// Load The Plugin Frame Main
require_once __DIR__ . '/app/Config/Main.php';
new \PluginFrame\Config\Main();

// WRITE ANYTHING BELOW THIS LINE
// ##############################