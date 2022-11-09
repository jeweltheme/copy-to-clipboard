<?php

namespace COPYTOCLIPBOARD\Inc;

// no direct access allowed
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
class Assets
{
    private static  $instance = null;

    public function __construct()
    {
        // add_action('admin_enqueue_scripts', [$this, 'master_clipboard_admin_scripts'], 100);
        add_action('wp_enqueue_scripts', [$this, 'master_clipboard_enqueue_scripts'], 100);
    }


    public static function assets_ext($ext)
    {
        if (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) {
            return $ext;
        }
        return '.min' . $ext;
    }


    public function master_clipboard_enqueue_scripts(){
        wp_register_script(
            'copy-to-clipboard',
            COPY_TO_CLIPBOARD_ASSETS . 'js/clipboard' . self::assets_ext('.js'),
            ['jquery'],
            COPY_TO_CLIPBOARD_VER,
            true
        );
        wp_enqueue_script('copy-to-clipboard');

        wp_add_inline_script(
            'copy-to-clipboard',
            "
            // var btn = document.getElementById('btn');
            // var clipboard = new ClipboardJS(btn);

            // clipboard.on('success', function (e) {
            //     console.info('Action:', e.action);
            //     console.info('Text:', e.text);
            //     console.info('Trigger:', e.trigger);
            // });

            // clipboard.on('error', function (e) {
            //     console.info('Action:', e.action);
            //     console.info('Text:', e.text);
            //     console.info('Trigger:', e.trigger);
            // });

            var clipboard = new ClipboardJS('.btn');
            clipboard.on('success', function (e) {
                console.info('Action:', e.action);
                console.info('Text:', e.text);
                console.info('Trigger:', e.trigger);
            });

            clipboard.on('error', function (e) {
                console.info('Action:', e.action);
                console.info('Text:', e.text);
                console.info('Trigger:', e.trigger);
            });

            "
        );
    }


    public function master_clipboard_js_object(){
        return array(
            'ajax_url'       => admin_url('admin-ajax.php'),
            'security_nonce' => wp_create_nonce('adminify-admin-bar-security-nonce'),
            'notice_nonce'   => wp_create_nonce('adminify-notice-nonce'),
        );
    }
    /**
     * Returns the singleton instance of the class.
     */
    public static function get_instance()
    {
        if (!isset(self::$instance) && !self::$instance instanceof Assets) {
            self::$instance = new Assets();
        }
        return self::$instance;
    }


}
Assets::get_instance();
