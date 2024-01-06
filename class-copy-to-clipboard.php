<?php
namespace COPYTOCLIPBOARD;

use COPYTOCLIPBOARD\Libs\Assets;
use COPYTOCLIPBOARD\Libs\Helper;
use COPYTOCLIPBOARD\Libs\Featured;
use COPYTOCLIPBOARD\Inc\Classes\Recommended_Plugins;
use COPYTOCLIPBOARD\Inc\Classes\Notifications\Notifications;
use COPYTOCLIPBOARD\Inc\Classes\Pro_Upgrade;
use COPYTOCLIPBOARD\Inc\Classes\Upgrade_Plugin;
use COPYTOCLIPBOARD\Inc\Classes\Feedback;
use COPYTOCLIPBOARD\Inc\Classes\Admin_Menu;

/**
 * Main Class
 *
 * @copy-to-clipboard
 * Jewel Theme <support@jeweltheme.com>
 * @version     1.0.2
 */

// No, Direct access Sir !!!
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Copy_To_Clipboard Class
 */
if ( ! class_exists( '\COPYTOCLIPBOARD\Copy_To_Clipboard' ) ) {

	/**
	 * Class: Copy_To_Clipboard
	 */
	final class Copy_To_Clipboard {

		const VERSION            = COPYTOCLIPBOARD_VER;
		private static $instance = null;

		/**
		 * what we collect construct method
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function __construct() {
			$this->includes();
			add_action( 'plugins_loaded', array( $this, 'copy_to_clipboard_plugins_loaded' ), 999 );
			// Body Class.
			add_filter( 'admin_body_class', array( $this, 'copy_to_clipboard_body_class' ) );
			// This should run earlier .
			// add_action( 'plugins_loaded', [ $this, 'copy_to_clipboard_maybe_run_upgrades' ], -100 ); .
		}

		/**
		 * plugins_loaded method
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function copy_to_clipboard_plugins_loaded() {
			$this->copy_to_clipboard_activate();
		}

		/**
		 * Version Key
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public static function plugin_version_key() {
			return Helper::copy_to_clipboard_slug_cleanup() . '_version';
		}

		/**
		 * Activation Hook
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public static function copy_to_clipboard_activate() {
			$current_copy_to_clipboard_version = get_option( self::plugin_version_key(), null );

			if ( get_option( 'copy_to_clipboard_activation_time' ) === false ) {
				update_option( 'copy_to_clipboard_activation_time', strtotime( 'now' ) );
			}

			if ( is_null( $current_copy_to_clipboard_version ) ) {
				update_option( self::plugin_version_key(), self::VERSION );
			}

			$allowed = get_option( Helper::copy_to_clipboard_slug_cleanup() . '_allow_tracking', 'no' );

			// if it wasn't allowed before, do nothing .
			if ( 'yes' !== $allowed ) {
				return;
			}
			// re-schedule and delete the last sent time so we could force send again .
			$hook_name = Helper::copy_to_clipboard_slug_cleanup() . '_tracker_send_event';
			if ( ! wp_next_scheduled( $hook_name ) ) {
				wp_schedule_event( time(), 'weekly', $hook_name );
			}
		}


		/**
		 * Add Body Class
		 *
		 * @param [type] $classes .
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function copy_to_clipboard_body_class( $classes ) {
			$classes .= ' copy-to-clipboard ';
			return $classes;
		}

		/**
		 * Run Upgrader Class
		 *
		 * @return void
		 */
		public function copy_to_clipboard_maybe_run_upgrades() {
			if ( ! is_admin() && ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// Run Upgrader .
			$upgrade = new Upgrade_Plugin();

			// Need to work on Upgrade Class .
			if ( $upgrade->if_updates_available() ) {
				$upgrade->run_updates();
			}
		}

		/**
		 * Include methods
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function includes() {
			new Assets();
			new Recommended_Plugins();
			new Pro_Upgrade();
			new Notifications();
			new Featured();
			new Feedback();
			new Admin_Menu();
		}


		/**
		 * Initialization
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function copy_to_clipboard_init() {
			$this->copy_to_clipboard_load_textdomain();
		}


		/**
		 * Text Domain
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function copy_to_clipboard_load_textdomain() {
			$domain = 'copy-to-clipboard';
			$locale = apply_filters( 'copy_to_clipboard_plugin_locale', get_locale(), $domain );

			load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
			load_plugin_textdomain( $domain, false, dirname( COPYTOCLIPBOARD_BASE ) . '/languages/' );
		}

		/**
		* Deactivate Pro Plugin if it's not already active
		*
		* @author Jewel Theme <support@jeweltheme.com>
		*/
		public static function copy_to_clipboard_activation_hook() {
			if ( copy_to_clipboard_license_client()->is_free_plan() ) {
				$plugin = 'copy-to-clipboard-pro/copy-to-clipboard.php';
			} else {
				$plugin = 'copy-to-clipboard/copy-to-clipboard.php';
			}
			if ( is_plugin_active( $plugin ) ) {
				deactivate_plugins( $plugin );
			}
		}


		/**
		 * Returns the singleton instance of the class.
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Copy_To_Clipboard ) ) {
				self::$instance = new Copy_To_Clipboard();
				self::$instance->copy_to_clipboard_init();
			}

			return self::$instance;
		}
	}

	// Get Instant of Copy_To_Clipboard Class .
	Copy_To_Clipboard::get_instance();
}
