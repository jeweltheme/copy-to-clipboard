<?php
namespace COPYTOCLIPBOARD\Libs\License;

use COPYTOCLIPBOARD\Libs\Helper;

/*
 * @version       1.0.2
 * @package       Copy_To_Clipboard
 * @license       Copyright Copy_To_Clipboard
 */

if ( ! class_exists( 'Loader' ) ) {
	/**
	 * Class Loader.
	 */
	final class Loader extends Client {


		protected $args = array();
		public static $plugin_basename;
		protected $wc_am_api_resources_key;

		/**
		 *  Default Params
		 */
		public static function get_defaults() {
			return array(
				'plugin_root'      => null,
				'software_version' => null,
				'software_type'    => 'plugin',
				'api_end_point'    => Helper::api_endpoint(),
				'text_domain'      => '',
				'software_title'   => '',
				'product_id'       => '',
				'redirect_url'     => '',
				'license_menu'     => array(),
				'inactive_notice'  => true,
			);
		}

		/**
		 * Construct Method
		 *
		 * @param [type] $args .
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function __construct( $args ) {
			$this->wc_am_api_resources_key = $this->data_key . '_resource_key';

			$this->args = shortcode_atts( self::get_defaults(), $args );

			parent::__construct(
				$this->args['plugin_root'],
				$this->args['product_id'],
				$this->args['software_version'],
				$this->args['software_type'],
				$this->args['api_end_point'],
				$this->args['software_title'],
				$this->args['text_domain'],
				$this->args['license_menu'],
				$this->args['inactive_notice']
			);

			if ( ! is_array( $this->data ) ) {
				$this->data = array();
			}

			self::$plugin_basename = plugin_basename( $this->args['plugin_root'] );

			add_action( 'plugin_action_links_' . self::$plugin_basename, array( $this, 'license_popup_link' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

			add_action( 'wp_ajax_' . $this->plugin_base_key( 'activate_license' ), array( $this, 'activate_license' ) );
			add_action( 'wp_ajax_' . $this->plugin_base_key( 'deactivate_license' ), array( $this, 'deactivate_license' ) );
			add_action( 'wp_ajax_' . $this->plugin_base_key( 'disable_activate_license_notice' ), array( $this, 'disable_activate_license_notice' ) );

			add_action( 'admin_footer', array( $this, 'view_license_popup' ) );
			add_action( 'admin_init', array( $this, 'setup_refresh_event' ) );
		}

		/**
		 * Run Schedule eventds
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function setup_refresh_event() {
			// Fire refresh event .
			add_action( self::get_prefixed( 'refresh_event' ), array( $this, 'refresh' ) );
			// clear the schedule event on deactivation .
			register_deactivation_hook( COPYTOCLIPBOARD_FILE, array( get_class( $this ), 'clear_refresh_event' ) );
			// register the schedule event.
			if ( ! wp_next_scheduled( self::get_prefixed( 'refresh_event' ) ) ) {
				wp_schedule_event( time(), 'daily', self::get_prefixed( 'refresh_event' ) );
			}
		}

		/**
		 * Clear Schedule Events
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public static function clear_refresh_event() {
			wp_clear_scheduled_hook( self::get_prefixed( 'refresh_event' ) );
		}

        public function get_license_params() {

			$data = get_option( $this->data_key );

			$wc_am_api_key  	= $data[ $this->data_key . '_api_key' ];
			$wc_am_instance_key = $this->data_key . '_instance';
			$wc_am_domain 		= str_ireplace( array( 'http://', 'https://' ), '', home_url() );

			return array(
				'wc-api' 		=> 'wc-am-api',
				'wc_am_action' 	=> 'status',
				'api_key'      	=> $wc_am_api_key,
				'product_id'   	=> $this->product_id,
				'instance'     	=> get_option( $wc_am_instance_key ),
				'object'       	=> $wc_am_domain
			);

		}


		/**
		 * Refresh key
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function refresh() {
			if ( $this->get_api_key_status( true ) ) {
				update_option( $this->wc_am_activated_key, 'Activated' );
				update_option( $this->wc_am_deactivate_checkbox_key, 'off' );
			} else {
				update_option( $this->wc_am_activated_key, 'Deactivated' );
			}
		}

		/**
		 * Ger Resources
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function get_resource() {
			$data = get_option( $this->data_key );

			if ( ! empty( $data[ $this->wc_am_api_resources_key ] ) ) {
				return $data[ $this->wc_am_api_resources_key ];
			}

			return array();
		}

		/**
		 * Select which is plan
		 *
		 * @param [type] $plan_name .
		 *
		 * @return boolean
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function is_plan( $plan_name ) {

			if ( ! $this->is_premium() ) return false;

			$resources = $this->get_resource();

			$resources = wp_list_filter(
				$resources,
				array(
					'product_id'     => $this->product_id,
					'active'         => true,
					'variation_name' => $plan_name,
				)
			);

			if ( ! empty( $resources ) ) {
				return true;
			}

			return false;
		}

		/**
		 * check is_free_plan
		 *
		 * @return boolean
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function is_free_plan() {
			return ! $this->get_api_key_status();
		}

		/**
		 * Check is premium plan
		 *
		 * @return boolean
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function is_premium() {
			return $this->get_api_key_status();
		}

		/**
		 * Check can use premium code
		 *
		 * @return boolean
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function can_use_premium_code() {

			if ( $this->is_premium() ) return true;

			$resources = $this->get_resource();
			
			if ( empty( $resources ) ) {
				return false;
			} // no license found

			$resources_expired = wp_list_filter(
				$resources,
				array(
					'product_id'     => $this->product_id
				)
			);

			$access_expires = wp_list_pluck( $resources_expired, 'access_expires' );

			if ( ! empty( $access_expires ) ) {
				return true;
			} // expired license found

			return false;
		}

		/**
		 * Load settings
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function load_settings() {
			// Do not remove this method, This should be empty to override the parent method.
		}

		/**
		 * Register Menu
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function register_menu() {
			$page_title = $this->wc_am_settings_title;
			$menu_title = $this->wc_am_settings_menu_title;

			$capability  = ! empty( $this->menu['capability'] ) ? $this->menu['capability'] : 'manage_options';
			$menu_slug   = ! empty( $this->menu['menu_slug'] ) ? $this->menu['menu_slug'] : dirname( self::$plugin_basename ) . '-license-activation';
			$parent_slug = ! empty( $this->menu['parent_slug'] ) ? $this->menu['parent_slug'] : dirname( self::$plugin_basename ) . '-settings';
			$callback    = array( $this, 'config_page' );

			$icon_url = ! empty( $this->menu['icon_url'] ) ? $this->menu['icon_url'] : '';
			$position = ! empty( $this->menu['position'] ) ? $this->menu['position'] : null;

			$menu_type = ! empty( $this->menu['menu_type'] ) ? $this->menu['menu_type'] : '';

			switch ( $menu_type ) {
				case 'add_submenu_page':
					add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $callback, $position );
					break;

				case 'add_options_page':
					add_options_page( $page_title, $menu_title, $capability, $menu_slug, $callback, $position );
					break;

				case 'add_menu_page':
					add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $callback, $icon_url, $position );
					break;
				default:
					add_options_page( $page_title, $menu_title, 'manage_options', $menu_slug, $callback );
					break;
			}
		}


		/**
		 * license key status
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function get_wc_am_api_key_status() {
			ob_start();
			$this->wc_am_api_key_status();
			return ob_get_clean();
		}

		/**
		 * Configuration page
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function config_page() {
			$license_status    = $this->get_wc_am_api_key_status();
			$is_license_active = $this->get_api_key_status();
			$current_api_key   = ! empty( $this->data[ $this->wc_am_api_key_key ] ) ? $this->data[ $this->wc_am_api_key_key ] : '';
			$product_id        = empty( $this->product_id ) ? '' : $this->product_id;

			?>

			<div id="<?php echo esc_attr( self::get_prefixed( 'copy-to-clipboard-popup' ) ); ?>" class="copy-to-clipboard-license-popup copy-to-clipboard-license-settings-page
								<?php
								if ( $is_license_active ) {
									echo 'copy-to-clipboard-license-status--active'; }
								?>
			">
				<div class="copy-to-clipboard-license-popup--wrapper copy-to-clipboard-license-popup--display">
					<div class="copy-to-clipboard-license-popup--container">
						<div class="copy-to-clipboard-license-popup--container-inner">

							<div class="copy-to-clipboard-license-popup--notice"></div>

							<?php if ( $is_license_active ) { ?>

								<h3><?php esc_html_e( 'License Key Deactivation', 'copy-to-clipboard' ); ?></h3>

								<p>
									<?php esc_html_e( 'Deactivates an License Key Key so it can be used on another blog.', 'copy-to-clipboard' ); ?>
								</p>

								<table class="form-table">
									<tbody>
										<tr>
											<th scope="row"><?php esc_html_e( 'License Key', 'copy-to-clipboard' ); ?></th>
											<td><input class="license_key" size="25" type="text" value="<?php echo esc_attr( str_pad( substr( $current_api_key, -10 ), strlen( $current_api_key ), '*', STR_PAD_LEFT ) ); ?>" /></td>
										</tr>
										<tr class="copy-to-clipboard-license-status">
											<th scope="row"><?php esc_html_e( 'License Status', 'copy-to-clipboard' ); ?></th>
											<td><?php echo esc_html( $license_status ); ?></td>
										</tr>
									</tbody>
								</table>

								<div class="copy-to-clipboard-license-actions">
									<button class="copy-to-clipboard-license-deactivate button button-primary wc-btn-active"><?php esc_html_e( 'Deactivate License', 'copy-to-clipboard' ); ?></button>
									<input type="hidden" name="nonce" value="<?php echo esc_js( wp_create_nonce( self::get_prefixed( 'admin-notice-nonce' ) ) ); ?>">
									<input type="hidden" name="deactivate_action" value="<?php echo esc_attr( $this->plugin_base_key( 'deactivate_license' ) ); ?>">
								</div>

							<?php } else { ?>

								<h3><?php esc_html_e( 'License Key Activation', 'copy-to-clipboard' ); ?></h3>

								<table class="form-table">
									<tbody>
										<tr>
											<th scope="row"><?php esc_html_e( 'License Key', 'copy-to-clipboard' ); ?></th>
											<td><input class="license_key" name="<?php echo esc_attr( $this->data_key ) . '[' . esc_attr( $this->wc_am_api_key_key ) . ']'; ?>" size="25" type="text" value="<?php echo esc_attr( $current_api_key ); ?>" /></td>
										</tr>
										<?php if ( $this->no_product_id ) { ?>
											<tr>
												<th scope="row"><?php esc_html_e( 'Product ID', 'copy-to-clipboard' ); ?></th>
												<td><input class="product_id" name="<?php echo esc_attr( $this->wc_am_product_id ); ?>" size="25" type="text" value="<?php echo esc_attr( $product_id ); ?>" /></td>
											</tr>
										<?php } ?>
										<tr class="copy-to-clipboard-license-status">
											<th scope="row"><?php esc_html_e( 'License Status', 'copy-to-clipboard' ); ?></th>
											<td><?php echo esc_html( $license_status ); ?></td>
										</tr>
									</tbody>
								</table>

								<div class="copy-to-clipboard-license-actions">
									<button class="copy-to-clipboard-license-activate button button-primary wc-btn-active"><?php esc_html_e( 'Activate License', 'copy-to-clipboard' ); ?></button>
									<input type="hidden" name="nonce" value="<?php echo esc_js( wp_create_nonce( self::get_prefixed( 'admin-notice-nonce' ) ) ); ?>">
									<input type="hidden" name="activate_action" value="<?php echo esc_attr( $this->plugin_base_key( 'activate_license' ) ); ?>">
								</div>

							<?php } ?>

							<div class="copy-to-clipboard-license--loader-wrapper">
								<div class="copy-to-clipboard-license--loader"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Displays an inactive notice when the software is inactive.
		 */
		public function inactive_notice() {
			/**
			 * Filter wc_am_client_inactive_notice_override
			 * If set to false inactive_notice() method will be disabled.
			 *
			 * @since 2.5.1
			 */
			$menu_slug = dirname( self::$plugin_basename ) . '-license-activation';

			if ( apply_filters( 'wc_am_client_inactive_notice_override', true ) ) {
				if ( ! current_user_can( 'manage_options' ) ) {
					return;
				}

				if ( isset( $_GET['page'] ) && $menu_slug === $_GET['page'] ) {
					return;
				}

				?>

				<div class="notice notice-error">
					<p><?php printf( __( 'The <strong>%1$s</strong> API Key has not been activated, so the %2$s is inactive! %3$sClick here%4$s to activate <strong>%5$s</strong>.', 'copy-to-clipboard' ), esc_attr( $this->software_title ), esc_attr( $this->plugin_or_theme ), '<a href="' . esc_url( admin_url( 'admin.php?page=' . $menu_slug ) ) . '">', '</a>', esc_attr( $this->software_title ) ); ?></p>
				</div>

				<?php
			}
		}

		/**
		 * Base Key
		 *
		 * @param [type] $key .
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function plugin_base_key( $key ) {
			return md5( $key . '_' . self::$plugin_basename );
		}

		/**
		 * License Active
		 *
		 * @return void
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function activate_license() {
			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

			if ( ! wp_verify_nonce( $nonce, self::get_prefixed( 'admin-notice-nonce' ) ) ) {
				wp_send_json_error(
					array(
						'message' => esc_html__( 'Oops! Security nonce is invalid.', 'copy-to-clipboard' ),
					)
				);
			}

			// Load existing options, validate, and update with changes from input before returning .
			$license_key                         = isset( $_REQUEST['license_key'] ) ?  $_REQUEST['license_key'] : '';
			$options                             = $this->data;
			$api_key                             = trim( $license_key );
			$options[ $this->wc_am_api_key_key ] = $api_key;
			$activation_status                   = get_option( $this->wc_am_activated_key );
			$checkbox_status                     = get_option( $this->wc_am_deactivate_checkbox_key );
			$current_api_key                     = ! empty( $this->data[ $this->wc_am_api_key_key ] ) ? $this->data[ $this->wc_am_api_key_key ] : '';

			/*
			 * @since 2.3
			 */
			if ( $this->no_product_id ) {
				$new_product_id = isset( $_REQUEST['product_id'] ) ? absint( $_REQUEST['product_id'] ) : '';

				if ( ! empty( $new_product_id ) ) {
					update_option( $this->wc_am_product_id, $new_product_id );
					$this->product_id = $new_product_id;
				}
			}

			// Should match the settings_fields() value .
			if ( 'Deactivated' === $activation_status || '' === $activation_status || '' === $api_key || 'on' === $checkbox_status || $api_key !== $current_api_key ) {
				/*
				 * If this is a new key, and an existing key already exists in the database,
				 * try to deactivate the existing key before activating the new key.
				 */
				if ( ! empty( $current_api_key ) && $api_key !== $current_api_key ) {
					$this->replace_license_key( $current_api_key );
				}

				$args = array(
					'api_key' => $api_key,
				);

				$activation_result = $this->activate( $args );

				if ( ! empty( $activation_result ) ) {
					$activate_results = json_decode( $activation_result, true );

					if ( true === $activate_results['success'] && true === $activate_results['activated'] ) {
						update_option( $this->wc_am_activated_key, 'Activated' );
						update_option( $this->wc_am_deactivate_checkbox_key, 'off' );

						// Store the License key.
						update_option(
							$this->data_key,
							array(
								$this->wc_am_api_key_key => $license_key,
								$this->wc_am_api_resources_key => $activate_results['data']['resources'],
							)
						);

						wp_send_json_success(
							array(
								'message'        => sprintf( __( '%s activated. ', 'copy-to-clipboard' ), esc_attr( $this->software_title ) ) . esc_attr( "{$activate_results['message']}." ),
								'activated_text' => esc_html__( 'Activated', 'copy-to-clipboard' ),
							)
						);
					}

					if ( false === $activate_results && ! empty( $this->data ) && ! empty( $this->wc_am_activated_key ) ) {
						update_option( $this->wc_am_activated_key, 'Deactivated' );
						wp_send_json_error(
							array(
								'message' => esc_html__( 'Connection failed to the License Key API server. Try again later. There may be a problem on your server preventing outgoing requests, or the store is blocking your request to activate the plugin/theme.', 'copy-to-clipboard' ),
							)
						);
					}

					if ( isset( $activate_results['data']['error_code'] ) && ! empty( $this->data ) && ! empty( $this->wc_am_activated_key ) ) {
						update_option( $this->wc_am_activated_key, 'Deactivated' );
						wp_send_json_error(
							array(
								'message' => esc_attr( "{$activate_results['data']['error']}" ),
							)
						);
					}
				} else {
					wp_send_json_error(
						array(
							'message' => esc_html__( 'The License Key activation could not be commpleted due to an unknown error possibly on the store server The activation results were empty.', 'copy-to-clipboard' ),
						)
					);
				}
			}

			wp_send_json_error(
				array(
					'message' => esc_html__( 'License or Product ID is invalid.', 'copy-to-clipboard' ),
				)
			);
		}

		/**
		 * Deactivate License
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function deactivate_license() {
			$activation_status = get_option( $this->wc_am_activated_key );
			$api_key           = ! empty( $this->data[ $this->wc_am_api_key_key ] ) ? $this->data[ $this->wc_am_api_key_key ] : '';

			if ( empty( $api_key ) ) {
				wp_send_json_error(
					array(
						'message' => esc_html__( 'The License Key is missing from the deactivation request.', 'copy-to-clipboard' ),
					)
				);
			}

			$args = array(
				'api_key' => $api_key,
			);

			if ( 'Activated' === $activation_status ) {
				$deactivation_result = $this->deactivate( $args );

				if ( ! empty( $deactivation_result ) ) {
					$activate_results = json_decode( $deactivation_result, true );

					if ( true === $activate_results['success'] && true === $activate_results['deactivated'] ) {
						update_option( $this->wc_am_activated_key, 'Deactivated' );
						wp_send_json_success(
							array(
								'message' => esc_html__( 'License Key deactivated. ', 'copy-to-clipboard' ) . esc_attr( "{$activate_results['activations_remaining']}." ),
							)
						);
					}

					if ( ! empty( $this->data ) && isset( $activate_results['data']['error_code'] ) ) {
						wp_send_json_error(
							array(
								'message' => esc_attr( "{$activate_results['data']['error']}" ),
							)
						);
					}
				} else {
					wp_send_json_error(
						array(
							'message' => esc_html__( 'The License Key activation could not be commpleted due to an unknown error possibly on the store server The activation results were empty.', 'copy-to-clipboard' ),
						)
					);
				}
			}

			wp_send_json_success(
				array(
					'code'    => 'already_deactivated',
					'message' => esc_html__( 'The License Key is already deactivated.', 'copy-to-clipboard' ),
				)
			);
		}

		/**
		 * License Popup links
		 *
		 * @param [type] $links .
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function license_popup_link( $links ) {
			$popup_target = self::get_prefixed( 'copy-to-clipboard-popup' );

			if ( 'Activated' === $this->get_activation_status() ) {
				$links['license_key'] = '<a href="#" class="copy-to-clipboard-lpt-btn inactive" data-wc-popup-target="' . esc_attr( $popup_target ) . '" aria-label="' . esc_attr__( 'Settings', 'copy-to-clipboard' ) . '">' . esc_html__( 'Deactivate License', 'copy-to-clipboard' ) . '</a>';
			} else {
				$links['license_key'] = '<a href="#" class="copy-to-clipboard-lpt-btn active" data-wc-popup-target="' . esc_attr( $popup_target ) . '" aria-label="' . esc_attr__( 'Settings', 'copy-to-clipboard' ) . '">' . esc_html__( 'Activate License', 'copy-to-clipboard' ) . '</a>';
			}

			return $links;
		}

		/**
		 * Get Prefixed
		 *
		 * @param [type] $text .
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public static function get_prefixed( $text ) {
			return $text . '__' . dirname( self::$plugin_basename );
		}

		/**
		 * Get Actiavation Status
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function get_activation_status() {
			return get_option( $this->wc_am_activated_key );
		}

		/**
		 * Admin Scripts
		 *
		 * @param [type] $hook .
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function admin_scripts( $hook = '' ) {
			wp_enqueue_style( self::get_prefixed( 'copy-to-clipboard-license' ), plugin_dir_url( __FILE__ ) . '/assets/license.css', null, time() );
			wp_enqueue_script( self::get_prefixed( 'copy-to-clipboard-license' ), plugin_dir_url( __FILE__ ) . '/assets/license.js', array( 'jquery' ), time(), true );

			$localize_vars = array(
				'ajaxurl'      => admin_url( 'admin-ajax.php' ),
				'redirect_url' => $this->args['redirect_url'],
			);

			wp_localize_script( self::get_prefixed( 'copy-to-clipboard-license' ), 'Copy_To_Clipboard_License_Manager_Vars', $localize_vars );
		}

		/**
		 * View License Popup
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function view_license_popup() {
			$is_license_active = $this->get_api_key_status();
			$current_api_key   = ! empty( $this->data[ $this->wc_am_api_key_key ] ) ? $this->data[ $this->wc_am_api_key_key ] : '';
			$product_id        = empty( $this->product_id ) ? '' : $this->product_id;
			$activation_status = get_option( $this->wc_am_activated_key );
			?>

			<div id="<?php echo esc_attr( self::get_prefixed( 'copy-to-clipboard-popup' ) ); ?>" class="copy-to-clipboard-license-popup
								<?php
								if ( $is_license_active ) {
									echo 'copy-to-clipboard-license-status--active';
								}
								?>
			">
				<div class="copy-to-clipboard-license-popup--wrapper copy-to-clipboard-license-popup--display">
					<div class="copy-to-clipboard-license-popup--container">
						<div class="copy-to-clipboard-license-popup--container-inner">

							<button class="wc-licelse--close"><svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
									<g fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2px">
										<line x1="7" x2="25" y1="7" y2="25" />
										<line x1="7" x2="25" y1="25" y2="7" />
									</g>
								</svg></button>

							<div class="copy-to-clipboard-license-popup--notice"></div>

							<?php if ( $is_license_active ) { ?>

								<h3><?php esc_html_e( 'License Deactivation', 'copy-to-clipboard' ); ?></h3>

								<p>
									<?php esc_html_e( 'Deactivates an License Key so it can be used on another blog.', 'copy-to-clipboard' ); ?>
								</p>

								<table class="form-table">
									<tbody>
										<tr>
											<th scope="row"><?php esc_html_e( 'License Key', 'copy-to-clipboard' ); ?></th>
											<td><input class="license_key" size="25" type="text" value="<?php echo esc_attr( str_pad( substr( $current_api_key, -10 ), strlen( $current_api_key ), '*', STR_PAD_LEFT ) ); ?>" /></td>
										</tr>
										<tr class="copy-to-clipboard-license-status">
											<th scope="row"><?php esc_html_e( 'License Status', 'copy-to-clipboard' ); ?></th>
											<td><?php echo esc_html( $activation_status ); ?></td>
										</tr>
									</tbody>
								</table>

								<div class="copy-to-clipboard-license-actions">
									<button class="copy-to-clipboard-license-deactivate button button-primary wc-btn-active"><?php esc_html_e( 'Deactivate License', 'copy-to-clipboard' ); ?></button>
									<input type="hidden" name="nonce" value="<?php echo esc_js( wp_create_nonce( self::get_prefixed( 'admin-notice-nonce' ) ) ); ?>">
									<input type="hidden" name="deactivate_action" value="<?php echo esc_attr( $this->plugin_base_key( 'deactivate_license' ) ); ?>">
								</div>

							<?php } else { ?>

								<h3><?php esc_html_e( 'License Key Activation', 'copy-to-clipboard' ); ?></h3>

								<table class="form-table">
									<tbody>
										<tr>
											<th scope="row"><?php esc_html_e( 'License Key', 'copy-to-clipboard' ); ?></th>
											<td><input class="license_key" name="<?php echo esc_attr( $this->data_key ) . '[' . esc_attr( $this->wc_am_api_key_key ) . ']'; ?>" size="25" type="text" value="<?php echo esc_attr( $current_api_key ); ?>" /></td>
										</tr>
										<?php if ( $this->no_product_id ) { ?>
											<tr>
												<th scope="row"><?php esc_html_e( 'Product ID', 'copy-to-clipboard' ); ?></th>
												<td><input class="product_id" name="<?php echo esc_attr( $this->wc_am_product_id ); ?>" size="25" type="text" value="<?php echo esc_attr( $product_id ); ?>" /></td>
											</tr>
										<?php } ?>
										<tr class="copy-to-clipboard-license-status">
											<th scope="row"><?php esc_html_e( 'License Status', 'copy-to-clipboard' ); ?></th>
											<td><?php echo esc_html( $activation_status ); ?></td>
										</tr>
									</tbody>
								</table>

								<div class="copy-to-clipboard-license-actions">
									<button class="copy-to-clipboard-license-activate button button-primary wc-btn-active"><?php esc_html_e( 'Activate License', 'copy-to-clipboard' ); ?></button>
									<input type="hidden" name="nonce" value="<?php echo esc_js( wp_create_nonce( self::get_prefixed( 'admin-notice-nonce' ) ) ); ?>">
									<input type="hidden" name="activate_action" value="<?php echo esc_attr( $this->plugin_base_key( 'activate_license' ) ); ?>">
								</div>

							<?php } ?>

							<div class="copy-to-clipboard-license--loader-wrapper">
								<div class="copy-to-clipboard-license--loader"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
	}
}