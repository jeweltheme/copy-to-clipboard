<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * @version       1.0.0
 * @package       Copy_To_Clipboard
 * @license       Copyright Copy_To_Clipboard
 */

if ( ! function_exists( 'copy_to_clipboard_option' ) ) {
	/**
	 * Get setting database option
	 *
	 * @param string $section default section name copy_to_clipboard_general .
	 * @param string $key .
	 * @param string $default .
	 *
	 * @return string
	 */
	function copy_to_clipboard_option( $section = 'copy_to_clipboard_general', $key = '', $default = '' ) {
		$settings = get_option( $section );

		return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
	}
}

if ( ! function_exists( 'copy_to_clipboard_exclude_pages' ) ) {
	/**
	 * Get exclude pages setting option data
	 *
	 * @return string|array
	 *
	 * @version 1.0.0
	 */
	function copy_to_clipboard_exclude_pages() {
		return copy_to_clipboard_option( 'copy_to_clipboard_triggers', 'exclude_pages', array() );
	}
}

if ( ! function_exists( 'copy_to_clipboard_exclude_pages_except' ) ) {
	/**
	 * Get exclude pages except setting option data
	 *
	 * @return string|array
	 *
	 * @version 1.0.0
	 */
	function copy_to_clipboard_exclude_pages_except() {
		return copy_to_clipboard_option( 'copy_to_clipboard_triggers', 'exclude_pages_except', array() );
	}
}