<?php

/**
 * Plugin Name: Copy to Clipboard
 * Description: Copy anything by using Copy to Clipboard
 * Plugin URI: https://github.com/litonarefin/copy-to-clipboard
 * Author: Jewel Theme
 * Version: 1.0.0
 * Author URI: https://jeweltheme.com/
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: copy-to-clipboard
 * Domain Path: /languages
 */


// No, Direct access Sir !!!
if (! defined('ABSPATH') ) {
    exit;
}

$copy_to_cliipboard_plugin_data = get_file_data(
    __FILE__,
    [
        'Version'     => 'Version',
        'Plugin Name' => 'Plugin Name',
        'Author'      => 'Author',
        'Description' => 'Description',
        'Plugin URI'  => 'Plugin URI',
    ],
    false
);

// Define Constants
if (! defined('COPY_TO_CLIPBOARD') ) {
    define('COPY_TO_CLIPBOARD', $copy_to_cliipboard_plugin_data['Plugin Name']);
}
if (! defined('COPY_TO_CLIPBOARD_VER') ) {
    define('COPY_TO_CLIPBOARD_VER', $copy_to_cliipboard_plugin_data['Version']);
}
if (! defined('COPY_TO_CLIPBOARD_FILE') ) {
    define('COPY_TO_CLIPBOARD_FILE', __FILE__);
}
if (! defined('COPY_TO_CLIPBOARD_BASE') ) {
    define('COPY_TO_CLIPBOARD_BASE', plugin_basename(__FILE__));
}
if (! defined('COPY_TO_CLIPBOARD_PATH') ) {
    define('COPY_TO_CLIPBOARD_PATH', trailingslashit(plugin_dir_path(__FILE__)));
}
if (! defined('COPY_TO_CLIPBOARD_URL') ) {
    define('COPY_TO_CLIPBOARD_URL', trailingslashit(plugins_url('/', __FILE__)));
}
if (! defined('COPY_TO_CLIPBOARD_ASSETS') ) {
    define('COPY_TO_CLIPBOARD_ASSETS', COPY_TO_CLIPBOARD_URL . 'assets/');
}
if (! defined('COPY_TO_CLIPBOARD_ASSETS_IMAGE') ) {
    define('COPY_TO_CLIPBOARD_ASSETS_IMAGE', COPY_TO_CLIPBOARD_ASSETS . 'images/');
}
if (! defined('COPY_TO_CLIPBOARD_ASSET_PATH') ) {
    define('COPY_TO_CLIPBOARD_ASSET_PATH', wp_upload_dir()['basedir'] . '/wp-adminify');
}
if (! defined('COPY_TO_CLIPBOARD_ASSET_URL') ) {
    define('COPY_TO_CLIPBOARD_ASSET_URL', wp_upload_dir()['baseurl'] . '/wp-adminify');
}
if (! defined('COPY_TO_CLIPBOARD_DESC') ) {
    define('COPY_TO_CLIPBOARD_DESC', $copy_to_cliipboard_plugin_data['Description']);
}
if (! defined('COPY_TO_CLIPBOARD_AUTHOR') ) {
    define('COPY_TO_CLIPBOARD_AUTHOR', $copy_to_cliipboard_plugin_data['Author']);
}
if (! defined('COPY_TO_CLIPBOARD_URI') ) {
    define('COPY_TO_CLIPBOARD_URI', $copy_to_cliipboard_plugin_data['Plugin URI']);
}


if (! class_exists('\\COPYTOCLIPBOARD\\Copy_To_Clipboard') ) {
    include_once dirname(__FILE__) . '/class-copy-to-clipboard.php';
}
