<?php
/**
 * Plugin Name: Plugin Frame
 * Plugin URI:   https://mhr.ractstudio.com
 * Description: Plugin Frame: A modern WordPress plugin development framework with Composer, npm, Tailwind, Twig, and Laravel-like structure. Ideal for lightweight or complex plugins. Complies with WP & PSR-4 standards. Modular, scalable, and dev-friendly.
 * Version:     0.9.1
 * Author:      Mahamudul Hasan Rubel
 * Author URI:  https://mhr.ractstudio.com
 * Text Domain: plugin-frame
 * Domain Path: /languages
 * License:     LGPL-3.0-or-later
 * License URI: https://www.gnu.org/licenses/lgpl-3.0.html
 */

namespace PluginFrame;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Define Constants.
 * Also declared in app/config/Main.php for redundancy.
 * This will override the constant defined in app/config/Main.php
 */
define( 'PLUGIN_FRAME_NAME', 'Plugin Frame' ); // Required
define( 'PLUGIN_FRAME_VERSION', '0.9.1' ); // Required
define( 'PLUGIN_FRAME_NAMESPACE', 'PluginFrame' ); // Required
define( 'PLUGIN_FRAME_SLUG', 'plugin-frame' ); // Required
define( 'PLUGIN_FRAME_PREFIX', 'plugin_frame' ); // Required
define( 'PLUGIN_FRAME_MIN_PHP', '7.4' ); // Required
define( 'PLUGIN_FRAME_FILE', __FILE__ ); // Required [MUST BE HERE, IF NOT POSSIBLE THEN, DELETE UNDERNEATH CONSTANT'S AS WELL]
define( 'PLUGIN_FRAME_DIR', plugin_dir_path( PLUGIN_FRAME_FILE ) ); // Required
define( 'PLUGIN_FRAME_URL', plugin_dir_url( PLUGIN_FRAME_FILE ) ); // Required
define( 'PLUGIN_FRAME_BASENAME', plugin_basename( PLUGIN_FRAME_FILE ) ); // Required

// Load The Plugin Frame Main Bootstrap File
require_once __DIR__ . '/app/Config/Bootstrap.php';
new \PluginFrame\Config\BootStrap();