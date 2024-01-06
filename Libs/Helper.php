<?php
namespace COPYTOCLIPBOARD\Libs;

// No, Direct access Sir !!!
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Helper Class
 *
 * Jewel Theme <support@jeweltheme.com>
 */

if ( ! class_exists( 'Helper' ) ) {
	/**
	 * Helper class
	 *
	 * Jewel Theme <support@jeweltheme.com>
	 */
	class Helper {


		/**
		 * Remove spaces from Plugin Slug
		 */
		public static function copy_to_clipboard_slug_cleanup() {
			return str_replace( '-', '_', strtolower( COPYTOCLIPBOARD_SLUG ) );
		}

		/**
		 * Function current_datetime() compability for wp version < 5.3
		 *
		 * @return DateTimeImmutable
		 */
		public static function copy_to_clipboard_current_datetime() {
			if ( function_exists( 'current_datetime' ) ) {
				return current_datetime();
			}

			return new \DateTimeImmutable( 'now', self::copy_to_clipboard_wp_timezone() );
		}

		/**
		 * Function copy_to_clipboard_wp_timezone() compability for wp version < 5.3
		 *
		 * @return DateTimeZone
		 */
		public static function copy_to_clipboard_wp_timezone() {
			if ( function_exists( 'wp_timezone' ) ) {
				return wp_timezone();
			}

			return new \DateTimeZone( self::copy_to_clipboard_wp_timezone_string() );
		}

		/**
		 * API Endpoint
		 *
		 * @return string
		 */
		public static function api_endpoint() {
			$api_endpoint_url = 'https://bo.jeweltheme.com';
			$api_endpoint     = apply_filters( 'copy_to_clipboard_endpoint', $api_endpoint_url );

			return trailingslashit( $api_endpoint );
		}

		/**
		 * CRM Endpoint
		 *
		 * @return string
		 */
		public static function crm_endpoint() {
			$crm_endpoint_url = 'https://bo.jeweltheme.com/wp-json/jlt-api/v1/subscribe'; // Endpoint .
			$crm_endpoint     = apply_filters( 'copy_to_clipboard_crm_crm_endpoint', $crm_endpoint_url );

			return trailingslashit( $crm_endpoint );
		}

		/**
		 * CRM Endpoint
		 *
		 * @return string
		 */
		public static function crm_survey_endpoint() {
			$crm_feedback_endpoint_url = 'https://bo.jeweltheme.com/wp-json/jlt-api/v1/survey'; // Endpoint .
			$crm_feedback_endpoint     = apply_filters( 'copy_to_clipboard_crm_crm_endpoint', $crm_feedback_endpoint_url );

			return trailingslashit( $crm_feedback_endpoint );
		}

		/**
		 * Function copy_to_clipboard_wp_timezone_string() compability for wp version < 5.3
		 *
		 * @return string
		 */
		public static function copy_to_clipboard_wp_timezone_string() {
			$timezone_string = get_option( 'timezone_string' );

			if ( $timezone_string ) {
				return $timezone_string;
			}

			$offset  = (float) get_option( 'gmt_offset' );
			$hours   = (int) $offset;
			$minutes = ( $offset - $hours );

			$sign      = ( $offset < 0 ) ? '-' : '+';
			$abs_hour  = abs( $hours );
			$abs_mins  = abs( $minutes * 60 );
			$tz_offset = sprintf( '%s%02d:%02d', $sign, $abs_hour, $abs_mins );

			return $tz_offset;
		}

		/**
		 * Get Merged Data
		 *
		 * @param [type] $data .
		 * @param string $start_date .
		 * @param string $end_data .
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public static function get_merged_data( $data, $start_date = '', $end_data = '' ) {
			$_data = shortcode_atts(
				array(
					'image_url'        => COPYTOCLIPBOARD_IMAGES . '/promo-image.png',
					'start_date'       => $start_date,
					'end_date'         => $end_data,
					'counter_time'     => '',
					'is_campaign'      => 'false',
					'button_text'      => 'Get Premium',
					'button_url'       => 'https://jeweltheme.com',
					'btn_color'        => '#CC22FF',
					'notice'           => '',
					'notice_timestamp' => '',
				),
				$data
			);

			if ( empty( $_data['image_url'] ) ) {
				$_data['image_url'] = COPYTOCLIPBOARD_IMAGES . '/promo-image.png';
			}

			return $_data;
		}


		/**
		 * wp_kses attributes map
		 *
		 * @param array $attrs .
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public static function wp_kses_atts_map( array $attrs ) {
			return array_fill_keys( array_values( $attrs ), true );
		}

		/**
		 * Custom method
		 *
		 * @param [type] $content .
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public static function wp_kses_custom( $content ) {
			$allowed_tags = wp_kses_allowed_html( 'post' );

			$custom_tags = array(
				'select'         => self::wp_kses_atts_map( array( 'class', 'id', 'style', 'width', 'height', 'title', 'data', 'name', 'autofocus', 'disabled', 'multiple', 'required', 'size' ) ),
				'input'          => self::wp_kses_atts_map( array( 'class', 'id', 'style', 'width', 'height', 'title', 'data', 'name', 'autofocus', 'disabled', 'required', 'size', 'type', 'checked', 'readonly', 'placeholder', 'value', 'maxlength', 'min', 'max', 'multiple', 'pattern', 'step', 'autocomplete' ) ),
				'textarea'       => self::wp_kses_atts_map( array( 'class', 'id', 'style', 'width', 'height', 'title', 'data', 'name', 'autofocus', 'disabled', 'required', 'rows', 'cols', 'wrap', 'maxlength' ) ),
				'option'         => self::wp_kses_atts_map( array( 'class', 'id', 'label', 'disabled', 'label', 'selected', 'value' ) ),
				'optgroup'       => self::wp_kses_atts_map( array( 'disabled', 'label', 'class', 'id' ) ),
				'form'           => self::wp_kses_atts_map( array( 'class', 'id', 'data', 'style', 'width', 'height', 'accept-charset', 'action', 'autocomplete', 'enctype', 'method', 'name', 'novalidate', 'rel', 'target' ) ),
				'svg'            => self::wp_kses_atts_map( array( 'class', 'xmlns', 'viewbox', 'width', 'height', 'fill', 'aria-hidden', 'aria-labelledby', 'role' ) ),
				'rect'           => self::wp_kses_atts_map( array( 'rx', 'width', 'height', 'fill' ) ),
				'path'           => self::wp_kses_atts_map( array( 'd', 'fill' ) ),
				'g'              => self::wp_kses_atts_map( array( 'fill' ) ),
				'defs'           => self::wp_kses_atts_map( array( 'fill' ) ),
				'linearGradient' => self::wp_kses_atts_map( array( 'id', 'x1', 'x2', 'y1', 'y2', 'gradientUnits' ) ),
				'stop'           => self::wp_kses_atts_map( array( 'stop-color', 'offset', 'stop-opacity' ) ),
				'style'          => self::wp_kses_atts_map( array( 'type' ) ),
				'div'            => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'ul'             => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'li'             => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'label'          => self::wp_kses_atts_map( array( 'class', 'for' ) ),
				'span'           => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'h1'             => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'h2'             => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'h3'             => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'h4'             => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'h5'             => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'h6'             => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'a'              => self::wp_kses_atts_map( array( 'class', 'href', 'target', 'rel' ) ),
				'p'              => self::wp_kses_atts_map( array( 'class', 'id', 'style', 'data' ) ),
				'table'          => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'thead'          => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'tbody'          => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'tr'             => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'th'             => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'td'             => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'i'              => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'button'         => self::wp_kses_atts_map( array( 'class', 'id' ) ),
				'nav'            => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'time'           => self::wp_kses_atts_map( array( 'datetime' ) ),
				'br'             => array(),
				'strong'         => array(),
				'style'          => array(),
				'img'            => self::wp_kses_atts_map( array( 'class', 'src', 'alt', 'height', 'width', 'srcset', 'id', 'loading' ) ),
			);

			$allowed_tags = array_merge_recursive( $allowed_tags, $custom_tags );

			return wp_kses( stripslashes_deep( $content ), $allowed_tags );
		}
	}
}