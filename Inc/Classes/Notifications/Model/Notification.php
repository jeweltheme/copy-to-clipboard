<?php
namespace COPYTOCLIPBOARD\Inc\Classes\Notifications\Model;

use COPYTOCLIPBOARD\Inc\Classes\Notifications\Base\Date;
use ReflectionClass;

// No, Direct access Sir !!!
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Notification abstract class
 *
 * Jewel Theme <support@jeweltheme.com>
 */
abstract class Notification {

	use Date;

	public $type;
	public $control;
	public $color;
	public $current_interval = 0;
	public $intervals        = array();
	public $is_active        = true;
	public $next_exec_time   = '';

	/**
	 * Construct method
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Init method
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	final public function init() {
		$data = $this->get();

		if ( false !== $data ) { // Found Data from Database.
			$this->is_active        = isset( $data['is_active'] ) ? $data['is_active'] : true;
			$this->intervals        = isset( $data['intervals'] ) ? $data['intervals'] : array();
			$this->current_interval = isset( $data['current_interval'] ) ? $data['current_interval'] : 0;
		} else { // No Data From Database, So Build & Save it.
			$this->build();
			$this->save();
		}

		if ( $this->is_active && ! empty( $this->intervals ) ) {
			$intervals = wp_list_filter( $this->intervals, array( 'fired' => false ) );

			if ( ! empty( $intervals ) ) {
				$first                = array_shift( $intervals );
				$this->next_exec_time = $first['date'];
			}
		}

		return $this;
	}

	/**
	 * build method
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	final public function build() {
		foreach ( $this->get_intervals() as $index => $day ) {
			$data = array(
				'days'  => $day,
				'fired' => false,
			);

			if ( 0 === $index ) {
				$data['date'] = gmdate( 'Y-m-d', time() + ( DAY_IN_SECONDS * $day ) );
			} else {
				$data['date'] = $this->date_increment( $this->intervals[ $index - 1 ]['date'], $day );
			}

			$this->intervals[] = $data;
		}

		return $this->intervals;
	}

	/**
	 * Final method fire
	 *
	 * @param [type] $trigger_time .
	 * @param [type] $notification_type .
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	final public function fire( $trigger_time, $notification_type ) {
		update_option( "copy_to_clipboard_{$notification_type}_last_interact", $trigger_time );

		// Current Interval is completed .
		$this->intervals[ $this->current_interval ]['fired'] = true;

		// Set the next interval as Current Interval .
		$this->current_interval++;

		// Return if notification is inactive .
		if ( ! $this->is_active ) {
			return $this;
		}

		if ( $this->current_interval >= count( $this->intervals ) ) {
			// Stop the Notification if We reach the end interval .
			$this->is_active = false;
		} else {
			// Delay the next intervals if needed .
			$this->maybe_delay( $trigger_time );
		}

		return $this;
	}

	/**
	 * delay method
	 *
	 * @param [type] $trigger_time .
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	final public function maybe_delay( $trigger_time ) {
		$diff = abs( $this->date_diff( $this->next_exec_time, $trigger_time ) );

		if ( ! $diff ) {
			return $this;
		}

		foreach ( $this->intervals as &$interval ) {
			if ( $interval['fired'] ) {
				continue;
			}
			$interval['date'] = $this->date_increment( $interval['date'], $diff );
		}

		return $this;
	}

	/**
	 * Save method
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	final public function save() {
		update_option(
			$this->get_key(),
			array(
				'is_active'        => $this->is_active,
				'intervals'        => $this->intervals,
				'current_interval' => $this->current_interval,
			)
		);
	}

	/**
	 * Get method
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	final public function get() {
		return get_option( $this->get_key() );
	}

	/**
	 * Delete method
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	final public function delete() {
		delete_option( $this->get_key() );
	}

	/**
	 * Get ID
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	final public function get_id() {
		$calss_name = ( new ReflectionClass( $this ) )->getShortName();

		return strtolower( $calss_name );
	}

	/**
	 * Get intervals method
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	final public function get_intervals() {
		$interval = (array) $this->intervals();
		sort( $interval );

		return $interval;
	}

	/**
	 * Get key method
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	abstract public function get_key();

	/**
	 * Get intervals method
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	abstract public function intervals();
}