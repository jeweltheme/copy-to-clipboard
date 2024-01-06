<?php
namespace COPYTOCLIPBOARD\Inc\Classes\Notifications\Base;

// No, Direct access Sir !!!
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait User_Data {

	/**
	 * Get plugins list
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function get_plugins_list() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugins             = get_plugins();
		$activated_plugins   = '==== Activated Plugins List ====' . PHP_EOL;
		$deactivated_plugins = PHP_EOL . PHP_EOL . '==== Deactivated Plugins List ====' . PHP_EOL;

		$active_plugins_keys = get_option( 'active_plugins', array() );
		$inactive_counter    = array();
		$active_counter      = array();

		foreach ( $plugins as $key => $plugin ) {
			$network_plugins = ! empty( $plugin['Network'] ) ? $plugin['Network'] : 'n/a';
			$PluginURI       = ! empty( $plugin['PluginURI'] ) ? $plugin['PluginURI'] : 'n/a';
			$new_plugin      = $plugin['Name'] . '- v' . $plugin['Version'] . ', URL: ' . $PluginURI . ', Network: ' . $network_plugins;

			if ( is_plugin_inactive( $key ) ) {
				$deactivated_plugins .= $new_plugin . PHP_EOL;
			} else {
				$activated_plugins .= $new_plugin . PHP_EOL;
			}

			if ( in_array( $key, $active_plugins_keys ) ) {
				// Remove active plugins from list so we can show active and inactive separately .
				unset( $plugins[ $key ] );
				$inactive_counter[ $key ] = $key;
			} else {
				$active_counter[ $key ] = $key;
			}
		}

		return array(
			'active_plugins'            => $activated_plugins,
			'active_plugins_count'      => count( $active_counter ),
			'deactivated_plugins'       => $deactivated_plugins,
			'deactivated_plugins_count' => count( $inactive_counter ),
		);
	}

	/**
	 * Get Server Info
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function get_server_info() {
		global $wpdb;

		$server_data = array();

		$server_software = ( isset( $_SERVER['SERVER_SOFTWARE'] ) && ! empty( $_SERVER['SERVER_SOFTWARE'] ) ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '';
		if ( $server_software ) {
			$server_data['software'] = $server_software;
		}

		if ( function_exists( 'phpversion' ) ) {
			$server_data['php_version'] = phpversion();
		}

		$server_data['mysql_version'] = $wpdb->db_version();

		$server_data['php_max_upload_size']  = size_format( wp_max_upload_size() );
		$server_data['php_default_timezone'] = date_default_timezone_get();
		$server_data['php_soap']             = class_exists( 'SoapClient' ) ? 'Yes' : 'No';
		$server_data['php_fsockopen']        = function_exists( 'fsockopen' ) ? 'Yes' : 'No';
		$server_data['php_curl']             = function_exists( 'curl_init' ) ? 'Yes' : 'No';

		return $server_data;
	}

	/**
	 * Get WP Info
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function get_wp_info() {
		$wp_data = array();

		$wp_data['memory_limit'] = WP_MEMORY_LIMIT;
		$wp_data['debug_mode']   = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? 'Yes' : 'No';
		$wp_data['locale']       = get_locale();
		$wp_data['version']      = get_bloginfo( 'version' );
		$wp_data['multisite']    = is_multisite() ? 'Yes' : 'No';
		$wp_data['theme_slug']   = get_stylesheet();

		$theme = wp_get_theme( $wp_data['theme_slug'] );

		$wp_data['theme_name']    = $theme->get( 'Name' );
		$wp_data['theme_version'] = $theme->get( 'Version' );
		$wp_data['theme_uri']     = $theme->get( 'ThemeURI' );
		$wp_data['theme_author']  = $theme->get( 'Author' );

		return $wp_data;
	}

	/**
	 * Get User Counts
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function get_user_counts() {
		$user_count          = array();
		$user_count_data     = count_users();
		$user_count['total'] = $user_count_data['total_users'];

		// Get user count based on user role .
		foreach ( $user_count_data['avail_roles'] as $role => $count ) {
			if ( ! $count ) {
				continue;
			}

			$user_count[ $role ] = $count;
		}

		return $user_count;
	}

	/**
	 * Get Site Name
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function get_site_name() {
		$site_name = get_bloginfo( 'name' );

		if ( empty( $site_name ) ) {
			$site_name = get_bloginfo( 'description' );
			$site_name = wp_trim_words( $site_name, 3, '' );
		}

		if ( empty( $site_name ) ) {
			$site_name = esc_url( home_url() );
		}

		return $site_name;
	}

	/**
	 * Check if Local Server
	 *
	 * @return boolean
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function is_local_server() {
		$host     = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : 'localhost';
		$ip       = isset( $_SERVER['SERVER_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_ADDR'] ) ) : '127.0.0.1';
		$is_local = false;

		if (
			in_array( $ip, array( '127.0.0.1', '::1' ) )
			|| ! strpos( $host, '.' )
			|| in_array( strrchr( $host, '.' ), array( '.test', '.testing', '.local', '.localhost', '.localdomain' ) )
		) {
			$is_local = true;
		}

		return apply_filters( 'copy_to_clipboard_is_local', $is_local );
	}

	/**
	 * Get IP Address
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function get_user_ip_address() {
		$response = wp_remote_get( 'https://icanhazip.com/' );

		if ( is_wp_error( $response ) ) {
			return '';
		}

		$ip_address = trim( wp_remote_retrieve_body( $response ) );

		if ( ! filter_var( $ip_address, FILTER_VALIDATE_IP ) ) {
			return '';
		}

		return $ip_address;
	}

	/**
	 * Get Collection Data
	 *
	 * @return void
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function get_collect_data( $user_id, $arr = [] ){

		$billing_phone     = '';
		$billing_company   = '';
		$billing_address_1 = '';
		$billing_address_2 = '';
		$billing_city      = '';
		$billing_postcode  = '';
		$billing_country   = '';
		$billing_state     = '';

		// WooCommerce .
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			$billing_phone     = get_user_meta( $user_id, 'billing_phone', true );
			$billing_company   = get_user_meta( $user_id, 'billing_company', true );
			$billing_address_1 = get_user_meta( $user_id, 'billing_address_1', true );
			$billing_address_2 = get_user_meta( $user_id, 'billing_address_2', true );
			$billing_city      = get_user_meta( $user_id, 'billing_city', true );
			$billing_postcode  = get_user_meta( $user_id, 'billing_postcode', true );
			$billing_country   = get_user_meta( $user_id, 'billing_country', true );
			$billing_state     = get_user_meta( $user_id, 'billing_state', true );
		}

		$all_plugins               = $this->get_plugins_list();
		$active_deactivate_plugins = $all_plugins['active_plugins'] . $all_plugins['deactivated_plugins'];

		$data = array(
			'phone'                   => $billing_phone,
			'company'                 => $billing_company,
			'address_1'               => $billing_address_1,
			'address_2'               => $billing_address_2,
			'city'                    => $billing_city,
			'postcode'                => $billing_postcode,
			'country'                 => $billing_country,
			'state'                   => $billing_state,
			'list_id'                 => [3],
			'tag_id'                  => [35, 40, ],
			'server'                  => $this->get_server_info(),
			'wp'                      => $this->get_wp_info(),
			'number_of_users'         => $this->get_user_counts(),
			'site_language'           => \get_bloginfo( 'language' ),
			'active_inactive_plugins' => $active_deactivate_plugins,
			'site_name'               => $this->get_site_name(),
			'site_source'             => 'https://jeweltheme.com',
			'is_local'                => $this->is_local_server(),
			'ip_address'              => $this->get_user_ip_address(),
			'site_url'                => \get_site_url(), // custom field end / need to be create this in .
			'is_active_product'		  => 1,
			'installed_product_slug'  => COPYTOCLIPBOARD_SLUG,
		);

		$payload_data =  array_merge( $arr, $data );
		$endpoint_url = \COPYTOCLIPBOARD\Libs\Helper::crm_endpoint();
		if ( 'local' === wp_get_environment_type() ) {
			$response     = wp_remote_post(
				$endpoint_url,
				array(
					'body'      => $payload_data,
					'timeout'   => 100,
					'sslverify' => false,
				)
			);
		} else {
			// 'production' === wp_get_environment_type() .
			$response     = wp_safe_remote_post(
				$endpoint_url,
				array(
					'body'      => $payload_data,
					'timeout'   => 100,
				)
			);
		}

		return $response;
	}
}