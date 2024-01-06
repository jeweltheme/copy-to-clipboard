<?php

/**
 * Plugin Name: Copy to Clipboard
 * Plugin URI:  https://jeweltheme.com/copy-to-clipboard
 * Description: Text Copy to Clipboard on WordPress way
 * Version:     1.0.2
 * Author:      Jewel Theme
 * Author URI:  https://jeweltheme.com
 * Text Domain: copy-to-clipboard
 * Domain Path: languages/
 * License:     GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package copy-to-clipboard
 */

/*
 * don't call the file directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	wp_die( esc_html__( 'You can\'t access this page', 'copy-to-clipboard' ) );
}

$copy_to_clipboard_plugin_data = get_file_data(
	__FILE__,
	array(
		'Version'     => 'Version',
		'Plugin Name' => 'Plugin Name',
		'Author'      => 'Author',
		'Description' => 'Description',
		'Plugin URI'  => 'Plugin URI',
	),
	false
);

// Define Constants.
if ( ! defined( 'COPYTOCLIPBOARD' ) ) {
	define( 'COPYTOCLIPBOARD', $copy_to_clipboard_plugin_data['Plugin Name'] );
}

if ( ! defined( 'COPYTOCLIPBOARD_VER' ) ) {
	define( 'COPYTOCLIPBOARD_VER', $copy_to_clipboard_plugin_data['Version'] );
}

if ( ! defined( 'COPYTOCLIPBOARD_AUTHOR' ) ) {
	define( 'COPYTOCLIPBOARD_AUTHOR', $copy_to_clipboard_plugin_data['Author'] );
}

if ( ! defined( 'COPYTOCLIPBOARD_DESC' ) ) {
	define( 'COPYTOCLIPBOARD_DESC', $copy_to_clipboard_plugin_data['Author'] );
}

if ( ! defined( 'COPYTOCLIPBOARD_URI' ) ) {
	define( 'COPYTOCLIPBOARD_URI', $copy_to_clipboard_plugin_data['Plugin URI'] );
}

if ( ! defined( 'COPYTOCLIPBOARD_DIR' ) ) {
	define( 'COPYTOCLIPBOARD_DIR', __DIR__ );
}

if ( ! defined( 'COPYTOCLIPBOARD_FILE' ) ) {
	define( 'COPYTOCLIPBOARD_FILE', __FILE__ );
}

if ( ! defined( 'COPYTOCLIPBOARD_SLUG' ) ) {
	define( 'COPYTOCLIPBOARD_SLUG', dirname( plugin_basename( __FILE__ ) ) );
}

if ( ! defined( 'COPYTOCLIPBOARD_BASE' ) ) {
	define( 'COPYTOCLIPBOARD_BASE', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'COPYTOCLIPBOARD_PATH' ) ) {
	define( 'COPYTOCLIPBOARD_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
}

if ( ! defined( 'COPYTOCLIPBOARD_URL' ) ) {
	define( 'COPYTOCLIPBOARD_URL', trailingslashit( plugins_url( '/', __FILE__ ) ) );
}

if ( ! defined( 'COPYTOCLIPBOARD_INC' ) ) {
	define( 'COPYTOCLIPBOARD_INC', COPYTOCLIPBOARD_PATH . '/Inc/' );
}

if ( ! defined( 'COPYTOCLIPBOARD_LIBS' ) ) {
	define( 'COPYTOCLIPBOARD_LIBS', COPYTOCLIPBOARD_PATH . 'Libs' );
}

if ( ! defined( 'COPYTOCLIPBOARD_ASSETS' ) ) {
	define( 'COPYTOCLIPBOARD_ASSETS', COPYTOCLIPBOARD_URL . 'assets/' );
}

if ( ! defined( 'COPYTOCLIPBOARD_IMAGES' ) ) {
	define( 'COPYTOCLIPBOARD_IMAGES', COPYTOCLIPBOARD_ASSETS . 'images/' );
}

if ( ! class_exists( '\\COPYTOCLIPBOARD\\Copy_To_Clipboard' ) ) {
	// Autoload Files.
	include_once COPYTOCLIPBOARD_DIR . '/vendor/autoload.php';
	// Instantiate Copy_To_Clipboard Class.
	include_once COPYTOCLIPBOARD_DIR . '/class-copy-to-clipboard.php';
}

// Activation and Deactivation hooks.
if ( class_exists( '\\COPYTOCLIPBOARD\\Copy_To_Clipboard' ) ) {
	register_activation_hook( COPYTOCLIPBOARD_FILE, array( '\\COPYTOCLIPBOARD\\Copy_To_Clipboard', 'copy_to_clipboard_activation_hook' ) );
	// register_deactivation_hook( COPYTOCLIPBOARD_FILE, array( '\\COPYTOCLIPBOARD\\Copy_To_Clipboard', 'copy_to_clipboard_deactivation_hook' ) );
}
