<?php
/**
 * Plugin Name: RW PostViewStats Lite
 * Description: Free version of the article page view statistics plug-in, which supports page view export, REST API interface and ranking by heat.
 * Version: 1.0.1
 * Author: Robert South
 * Author URI: https://robertwp.com
 * License: GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: rw-postviewstats-lite
 * Domain Path: /languages
 */

namespace RobertWP\PostViewStatsLite;

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'RWPSL_PLUGIN_NAME', 'RW PostViewStats Lite' );
define( 'RWPSL_VERSION_OPTION', 'rwpsl_version' );
define( 'RWPSL_PLUGIN_VERSION', '1.0.1' );
define( 'RWPSL_PLUGIN_FILE', __FILE__ );
define( 'RWPSL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'RWPSL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'RWPSL_ASSETS_URL', RWPSL_PLUGIN_URL . 'assets/' );

require_once RWPSL_PLUGIN_DIR . 'includes/core/plugin.php';

