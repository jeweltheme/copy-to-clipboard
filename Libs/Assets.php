<?php
namespace COPYTOCLIPBOARD\Libs;

// No, Direct access Sir !!!
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Assets' ) ) {

	/**
	 * Assets Class
	 *
	 * Jewel Theme <support@jeweltheme.com>
	 * @version     1.0.2
	 */
	class Assets {

		/**
		 * Constructor method
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'copy_to_clipboard_enqueue_scripts' ), 100 );
			add_action( 'admin_enqueue_scripts', array( $this, 'copy_to_clipboard_admin_enqueue_scripts' ), 100 );
		}


		/**
		 * Get environment mode
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function get_mode() {
			return defined( 'WP_DEBUG' ) && WP_DEBUG ? 'development' : 'production';
		}

		/**
		 * Enqueue Scripts
		 *
		 * @method wp_enqueue_scripts()
		 */
		public function copy_to_clipboard_enqueue_scripts() {

			// CSS Files .
			wp_enqueue_style( 'copy-to-clipboard-frontend', COPYTOCLIPBOARD_ASSETS . 'css/copy-to-clipboard-frontend.css', COPYTOCLIPBOARD_VER, 'all' );

			// JS Files .
			wp_enqueue_script( 'copy-to-clipboard-frontend', COPYTOCLIPBOARD_ASSETS . 'js/copy-to-clipboard-frontend.js', array( 'jquery' ), COPYTOCLIPBOARD_VER, true );
		}


		/**
		 * Enqueue Scripts
		 *
		 * @method admin_enqueue_scripts()
		 */
		public function copy_to_clipboard_admin_enqueue_scripts() {
			// CSS Files .
			wp_enqueue_style( 'copy-to-clipboard-admin', COPYTOCLIPBOARD_ASSETS . 'css/copy-to-clipboard-admin.css', array( 'dashicons' ), COPYTOCLIPBOARD_VER, 'all' );

			// JS Files .
			wp_enqueue_script( 'copy-to-clipboard-admin', COPYTOCLIPBOARD_ASSETS . 'js/copy-to-clipboard-admin.js', array( 'jquery' ), COPYTOCLIPBOARD_VER, true );
			wp_localize_script(
				'copy-to-clipboard-admin',
				'COPYTOCLIPBOARDCORE',
				array(
					'admin_ajax'        => admin_url( 'admin-ajax.php' ),
					'recommended_nonce' => wp_create_nonce( 'copy_to_clipboard_recommended_nonce' ),
					'is_premium'        => copy_to_clipboard_license_client()->is_premium() ? true : false,
					'is_agency'         => copy_to_clipboard_license_client()->is_plan( 'agency' ) ? true : false,
				)
			);
		}
	}
}