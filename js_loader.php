<?php
/**
 * Plugin Name: Javascript Loader
 * Plugin URI: https://github.com/calibreworks/js_loader
 * Description: Javascript easy loader a compliment to support Calibrefx Themes Framework
 * Version: 0.1.0
 * Author: Ivan Kristianto
 * Author URI: https://www.ivankristianto.com
 * Requires at least: 4.1
 * Tested up to: 4.2
 *
 * Text Domain: js_loader
 * Domain Path: /languages/
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

define( 'JSLOADER__MINIMUM_WP_VERSION', '4.1' );
define( 'JSLOADER__VERSION',            '0.1.0' );
define( 'JSLOADER__PLUGIN_DIR',         plugin_dir_path( __FILE__ ) );
define( 'JSLOADER__PLUGIN_URL',         plugins_url( '', __FILE__ ) );
define( 'JSLOADER__PLUGIN_FILE',        __FILE__ );


require_once( JSLOADER__PLUGIN_DIR . 'class.js_loader.php' );

if( is_admin() ){
	require_once( JSLOADER__PLUGIN_DIR . 'class.js_loader-admin.php' );
}

register_activation_hook( __FILE__, array( 'Js_Loader', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'Js_Loader', 'plugin_deactivation' ) );

Js_Loader::init();