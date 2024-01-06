<?php
namespace COPYTOCLIPBOARD\Inc\Classes;

use COPYTOCLIPBOARD\Libs\Helper;
use COPYTOCLIPBOARD\Inc\Classes\Notifications\Base\Date;


// No, Direct access Sir !!!
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Upgrade to Pro Class
 *
 * Jewel Theme <support@jeweltheme.com>
 */
class Admin_Menu {

    /**
     * Construct method
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'admin_menu'));
    }



    public function admin_menu()
    {
        add_menu_page(
            esc_html__('Copy2Clipboard', 'copy-to-clipboard' ),
            esc_html__( 'Copy2Clipboard', 'copy-to-clipboard' ),
            'manage_options',
            'copy-to-clipboards',
            array( $this, 'copy_to_clipboards_page' ),
            'dashicons-admin-multisite',
            4
        );

        // Statistics
        add_submenu_page(
            'copy-to-clipboards',
            esc_html__('Builder', 'copy-to-clipboard' ),
            esc_html__('Builder', 'copy-to-clipboard' ),
            'manage_options',
            'copy-to-clipboard',
            [$this, 'copy_to_clipboard_builder']
        );

        // Templates
        // add_submenu_page(
        //     'copy-to-clipboards',
        //     esc_html__('Templates', 'copy-to-clipboard' ),
        //     esc_html__('Templates', 'copy-to-clipboard' ),
        //     'manage_options',
        //     'outreach-templates',
        //     [$this, 'templates_data']
        // );
    }


    public function copy_to_clipboard_builder(){
        echo '<div id="copy-to-clipboard-builder"></div>';
    }

    public function copy_to_clipboard_settings_page(){
        echo '<div id="copy-to-clipboards"></div>';
    }

}
