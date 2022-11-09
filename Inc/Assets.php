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
        add_action('wp_enqueue_scripts', [$this, 'copy_to_clipboard_enqueue_scripts'], 100);
    }


    public static function assets_ext($ext)
    {
        if (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) {
            return $ext;
        }
        return '.min' . $ext;
    }


    public function copy_to_clipboard_enqueue_scripts(){

        // Style
        wp_register_style(
            'copy-to-clipboard',
            COPY_TO_CLIPBOARD_ASSETS . 'css/copy-to-clipboard' . self::assets_ext('.css')
        );

        // Script
        wp_register_script(
            'copy-to-clipboard',
            COPY_TO_CLIPBOARD_ASSETS . 'js/clipboard' . self::assets_ext('.js'),
            ['jquery'],
            COPY_TO_CLIPBOARD_VER,
            true
        );

        // Enqueue
        wp_enqueue_style('copy-to-clipboard');
        wp_enqueue_script('copy-to-clipboard');

        wp_add_inline_script(
            'copy-to-clipboard',
            'let copy2clipBtn = document.querySelector(".copy2clip-btn");
            var clipboard = new ClipboardJS(".copy2clip-btn");
            let clearCopyText;

            clipboard.on("success", function (e) {
                copy2clipBtn.innerHTML = "Copied";
                clearInterval(clearCopyText);
                clearCopyText = setTimeout(() => {
                    copy2clipBtn.innerHTML = "Copy";
                }, 5000);
            });

            clipboard.on("error", function (e) {
                console.log(e);
            });'
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
