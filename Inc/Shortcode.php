<?php

namespace COPYTOCLIPBOARD\Inc;

// no direct access allowed
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

class Shortcodes
{
    private static  $instance = null;


    public function __construct()
    {
        add_action('init', [ $this, 'copy_to_clipboard_shortcode_init' ]);

    }

    public function copy_to_clipboard_shortcode_init(){
        add_shortcode('copy2clip', [$this, 'copy_to_clipboard_shortcode' ]);
    }

    public function copy_to_clipboard_shortcode($atts, $content = null, $tag = ''){
        $copy2clip = shortcode_atts(array(
            'text_to_copied' => esc_html__('Hello, this content will be copied', 'copy-to-clipboard'),
            'btn_copy_text' => esc_html__('Copy', 'copy-to-clipboard'),
            'btn_clip_text' => esc_html__('copy', 'copy-to-clipboard')
        ), $atts);

        $output = '<div class="copy-to-clipboard">
                <input class="copy2clip-btn-wrap" id="copy2clip-btn-wrap" type="text" value="' . esc_attr( $copy2clip['text_to_copied'] ) . '" />
                <button
                    class="copy2clip-btn"
                    data-clipboard-action="' . esc_attr($copy2clip['btn_clip_text']) . '"
                    data-clipboard-target=".copy2clip-btn-wrap"
                >' . esc_attr($copy2clip['btn_copy_text']) . '</button>
            </div>';

        return $output;

    }

    /**
     * Returns the singleton instance of the class.
     */
    public static function get_instance()
    {
        if (!isset(self::$instance) && !self::$instance instanceof Shortcodes) {
            self::$instance = new Shortcodes();
        }
        return self::$instance;
    }
}
Shortcodes::get_instance();
