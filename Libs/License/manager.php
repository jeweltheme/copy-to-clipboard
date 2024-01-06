<?php

if ( ! function_exists( 'copy_to_clipboard_license_client' ) ) {
	/**
	 * License Client function
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	function copy_to_clipboard_license_client() {
		global $copy_to_clipboard_license_client;

		if ( ! isset( $copy_to_clipboard_license_client ) ) {
			// Include SDK.
			require_once COPYTOCLIPBOARD_LIBS . '/License/Loader.php';

			$copy_to_clipboard_license_client = new \COPYTOCLIPBOARD\Libs\License\Loader(
				array(
					'plugin_root'      => COPYTOCLIPBOARD_FILE,
					'software_version' => COPYTOCLIPBOARD_VER,
					'software_title'   => 'copy-to-clipboard',
					'product_id'       => 1234,
					'redirect_url'     => admin_url( 'admin.php?page=' . COPYTOCLIPBOARD_SLUG . '-license-activation' ),
					'software_type'    => 'plugin', // theme/plugin .
					'api_end_point'    => \COPYTOCLIPBOARD\Libs\Helper::api_endpoint(),
					'text_domain'      => 'copy-to-clipboard',
					'license_menu'     => array(
						'icon_url'    => 'dashicons-image-filter',
						'position'    => 40,
						'menu_type'   => 'add_submenu_page', // 'add_submenu_page',
                        'parent_slug' => '-settings',
						'menu_title'  => esc_html__( 'License', 'copy-to-clipboard' ),
						'page_title'  => esc_html__( 'License Activation', 'copy-to-clipboard' ),
					),
				)
			);
		}

		return $copy_to_clipboard_license_client;
	}

	// Init Copy_To_Clipboard_Wc_Client.
	copy_to_clipboard_license_client();

	// Signal that Copy_To_Clipboard_Wc_Client was initiated.
	do_action( 'copy_to_clipboard_license_client_loaded' );
}