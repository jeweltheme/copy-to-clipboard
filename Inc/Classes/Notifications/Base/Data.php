<?php
namespace COPYTOCLIPBOARD\Inc\Classes\Notifications\Base;

// No, Direct access Sir !!!
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Data abstract class
 *
 * Jewel Theme <support@jeweltheme.com>
 */
abstract class Data {

	use Date;

	public $notifications = array();

	/**
	 * Get Notifications
	 *
	 * @param [type] $instance_key .
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function get_notification( $instance_key ) {
		if ( array_key_exists( $instance_key, $this->notifications ) ) {
			return $this->notifications[ $instance_key ];
		}

		return null;
	}

	/**
	 * Get Notificaiton
	 *
	 * @param [type] $type .
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function get_notifications( $type = null ) {
		$notifications = $this->notifications;

		if ( $type ) {
			$notifications = wp_list_filter( $notifications, array( 'type' => $type ) );
		}

		return $notifications;
	}

	/**
	 * Get Active notifications
	 *
	 * @param [type] $type .
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function get_active_notifications( $type = null ) {
		$notifications = wp_list_filter( $this->get_notifications( $type ), array( 'is_active' => true ) );

		return wp_list_sort( $notifications, 'next_exec_time' );
	}

	/**
	 * Get executable notifications
	 *
	 * @param [type] $date .
	 * @param [type] $type .
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function get_exec_notifications( $date = null, $type = null ) {
		if ( ! $date ) {
			$date = $this->current_time();
		}

		$notifications = $this->get_active_notifications( $type );

		$_notifications = array();

		foreach ( $notifications as $notification ) {
			if ( empty( $notification->next_exec_time ) ) {
				continue;
			}

			if ( $this->date_is_current_or_prev( $notification->next_exec_time, $date ) ) {
				$_notifications[] = $notification;
			}
		}

		return $_notifications;
	}

	/**
	 * Get Upcoming Notificaitons
	 *
	 * @param [type] $date .
	 * @param [type] $type .
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function get_upcoming_notifications( $date = null, $type = null ) {
		if ( ! $date ) {
			$date = $this->current_time();
		}

		$notifications = $this->get_active_notifications( $type );

		$_notifications = array();

		foreach ( $notifications as $notification ) {
			if ( empty( $notification->next_exec_time ) ) {
				continue;
			}

			if ( $this->date_is_next( $notification->next_exec_time, $date ) ) {
				$_notifications[] = $notification;
			}
		}

		return $_notifications;
	}

	/**
	 * Register Instance
	 *
	 * @param [type] $instance .
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function register( $instance ) {
		if ( ! array_key_exists( $instance->get_id(), $this->notifications ) ) {
			$this->notifications[ $instance->get_id() ] = $instance;
		}
	}
}