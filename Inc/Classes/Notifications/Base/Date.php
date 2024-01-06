<?php
namespace COPYTOCLIPBOARD\Inc\Classes\Notifications\Base;

// No, Direct access Sir !!!
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait Date {

	/**
	 * Current time
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function current_time() {
		return current_time( 'Y-m-d' );
	}

	/**
	 * Compare dates
	 *
	 * @param [type] $date_1 .
	 * @param [type] $date_2 .
	 * @param [type] $compare .
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function date_compare( $date_1, $date_2 = null, $compare = null ) {
		if ( ! $compare ) {
			$compare = '==';
		}

		if ( ! $date_2 ) {
			$date_2 = $this->current_time();
		}

		if ( '<' === $compare ) {
			return strtotime( $date_1 ) < strtotime( $date_2 );
		} elseif ( '>' === $compare ) {
			return strtotime( $date_1 ) > strtotime( $date_2 );
		} elseif ( '<=' === $compare ) {
			return strtotime( $date_1 ) <= strtotime( $date_2 );
		} elseif ( '>=' === $compare ) {
			return strtotime( $date_1 ) >= strtotime( $date_2 );
		} else {
			return strtotime( $date_1 ) === strtotime( $date_2 );
		}
	}

	/**
	 * Date Diff
	 *
	 * @param [type] $date_1 .
	 * @param [type] $date_2 .
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function date_diff( $date_1, $date_2 = null ) {
		if ( ! $date_2 ) {
			$date_2 = $this->current_time();
		}
		$diff = date_diff( date_create( $date_2 ), date_create( $date_1 ) );

		return $diff->format( '%R%a' );
	}


	/**
	 * Check Current date
	 *
	 * @param [type] $date_1 .
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function date_is_current( $date_1 ) {
		return $this->date_compare( $date_1 );
	}

	/**
	 * Check Prev date
	 *
	 * @param [type] $date_1 .
	 * @param [type] $date_2 .
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function date_is_prev( $date_1, $date_2 = null ) {
		return $this->date_compare( $date_1, $date_2, '<' );
	}


	/**
	 * Check current or previous date
	 *
	 * @param [type] $date_1 .
	 * @param [type] $date_2 .
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function date_is_current_or_prev( $date_1, $date_2 = null ) {
		return $this->date_compare( $date_1, $date_2, '<=' );
	}


	/**
	 * Date is next day
	 *
	 * @param [type] $date_1 .
	 * @param [type] $date_2 .
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function date_is_next( $date_1, $date_2 = null ) {
		return $this->date_compare( $date_1, $date_2, '>' );
	}

	/**
	 * Current date or next date
	 *
	 * @param [type] $date_1 .
	 * @param [type] $date_2 .
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function date_is_current_or_next( $date_1, $date_2 = null ) {
		return $this->date_compare( $date_1, $date_2, '>=' );
	}


	/**
	 * Date increament
	 *
	 * @param [type] $date .
	 * @param [type] $days .
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function date_increment( $date, $days ) {
		return gmdate( 'Y-m-d', strtotime( $date . " + $days days" ) );
	}
}