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
            $this->copy_to_clipboard_init();
        }

        /*
        * Init method
        */
        public function copy_to_clipboard_init()
        {
            $this->copy_to_clipboard_include_files();
        }

        public function copy_to_clipboard_include_files()
        {
            include COPY_TO_CLIPBOARD_PATH . '/Inc/Assets.php';
            include COPY_TO_CLIPBOARD_PATH . '/Inc/Shortcode.php';
        }

        /**
         * Returns the singleton instance of the class.
         */
        public static function get_instance()
        {
            if (!isset(self::$instance) && !self::$instance instanceof Copy_To_Clipboard) {
                self::$instance = new Copy_To_Clipboard();
            }

            return self::$instance;
        }



    }

    Copy_To_Clipboard::get_instance();
}
