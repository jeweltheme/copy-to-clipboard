<?php

namespace COPYTOCLIPBOARD;

use  COPYTOCLIPBOARD\Inc\Assets ;

// No, Direct access Sir !!!
if (!defined('ABSPATH') ) {
    exit;
}

if (!class_exists('Copy_To_Clipboard') ) {
    class Copy_To_Clipboard
    {
        private static  $instance = null;
        public function __construct()
        {
            // add_action('plugins_loaded', [$this, 'master_clipboard_maybe_run_upgrades'], -100);

            // master_clipboard()->add_filter('freemius_pricing_js_path', [$this, 'master_clipboard_freemius_pricing_js']);

        }

        /*
        * Init method
        */
        public function master_clipboard_init()
        {
            $this->master_clipboard_include_files();
        }

        public function master_clipboard_include_files()
        {
            // new Assets();
            include COPY_TO_CLIPBOARD_PATH . '/Inc/Assets.php';
        }


        public function master_clipboard_freemius_pricing_js($default_pricing_js_path)
        {
            return COPY_TO_CLIPBOARD_PATH . '/lib/freemius-pricing/freemius-pricing.js';
        }

        /**
         * Returns the singleton instance of the class.
         */
        public static function get_instance()
        {
            if (!isset(self::$instance) && !self::$instance instanceof Copy_To_Clipboard) {
                self::$instance = new Copy_To_Clipboard();
                self::$instance->master_clipboard_init();
            }

            return self::$instance;
        }



    }

    Copy_To_Clipboard::get_instance();
}
