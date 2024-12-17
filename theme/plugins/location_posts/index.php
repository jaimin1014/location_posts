<?php
/**
 * Plugin Name: Location Wise Posts
 * Plugin URI: https://www.jaimin.com
 * Description: Create a plugin that will add location field (tags) in posts and show the map on the front that will display markers of post on the map with clickable action
 * Author URI: https://www.jaimin.com
 * Author : Jaimin Panchal
 * Version: 1.0.0
 * License: GPL2+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package LocationPosts
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define global constants.
 *
 * @since 1.0.0
 */
// Plugin version.
if ( ! defined( 'LOCATION_POSTS_VERSION' ) ) {
	define( 'LOCATION_POSTS_VERSION', '1.0.0' );
}

if ( ! defined( 'LOCATION_POSTS_NAME' ) ) {
	define( 'LOCATION_POSTS_NAME', trim( dirname( plugin_basename( __FILE__ ) ), '/' ) );
}

if ( ! defined( 'LOCATION_POSTS_DIR' ) ) {
	define( 'LOCATION_POSTS_DIR', WP_PLUGIN_DIR . '/' . LOCATION_POSTS_NAME );
}

if ( ! defined( 'LOCATION_POSTS_URL' ) ) {
	define( 'LOCATION_POSTS_URL', plugins_url() . '/' . LOCATION_POSTS_NAME );
}

/**
 * WP_LOCATION_POSTS Initializer
 *
 * Initializes the WP_LOCATION_POSTS.
 *
 * @since   1.0.0
 */


/**
 * Class `WP_LOCATION_POSTS`.
 *
 * @since 1.0.0
 */
require_once LOCATION_POSTS_DIR . '/custom_functions.php';
require_once LOCATION_POSTS_DIR . '/class-location-posts.php';

/**
 * Activation and Deactivation hooks.
 *
 * @since 1.0.0
 */
function location_posts_activate() {
 	flush_rewrite_rules();   
}
function location_posts_deactivate() {
    flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'location_posts_activate' );
register_deactivation_hook( __FILE__, 'location_posts_deactivate' );