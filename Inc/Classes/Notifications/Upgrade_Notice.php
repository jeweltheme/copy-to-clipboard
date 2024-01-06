<?php
namespace COPYTOCLIPBOARD\Inc\Classes\Notifications;

use COPYTOCLIPBOARD\Inc\Classes\Notifications\Model\Popup;
use COPYTOCLIPBOARD\Inc\Classes\Pro_Upgrade;
use COPYTOCLIPBOARD\Libs\Helper;

if ( ! class_exists( 'Upgrade_Notice' ) ) {
	/**
	 * Upgrade notice class
	 *
	 * Jewel Theme <support@jeweltheme.com>
	 */
	class Upgrade_Notice extends Popup {


		protected $data = array();

		/**
		 * Constructor method
		 */
		public function __construct() {
			$this->init_data();

			// On sheet data update, remove the popup data, to auto rebuid the data.
			add_action(
				'copy_to_clipboard_sheet_promo_data_reset',
				function () {
					$this->delete();
				}
			);

			if ( ! empty( $this->data ) ) {
				parent::__construct();
			}
		}

		/**
		 * Get Contents
		 *
		 * @param [type] $key .
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function get_content( $key ) {
			return $this->data[ $key ];
		}

		/**
		 * Set Intervals
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function intervals() {
			$end_date   = $this->get_content( 'end_date' );
			$end_offset = $this->date_diff( $end_date );

			if ( $end_offset < 0 ) {
				return array();
			} // Already Expired .

			if ( 0 === $end_offset ) {
				return array( 0 );
			} // Only Today is Left .

			$intervals = array();

			$start_date   = $this->get_content( 'start_date' );
			$start_offset = $this->date_diff( $start_date );

			// Start Done .
			$start       = $start_offset <= 0 ? 0 : abs( $start_offset );
			$intervals[] = $start;

			// End Calculated .
			$end = $end_offset - $start;

			// Middle Done .
			if ( $end > 3 ) {
				$middle      = round( $end / 2 );
				$end         = $end - $middle;
				$intervals[] = $middle;
			}

			// End Done .
			if ( $end > 0 ) {
				$intervals[] = $end;
			}

			return $intervals;
		}

		/**
		 * Init Data
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function init_data() {
			$sheet_data = Pro_Upgrade::get_data();

			if ( empty( $sheet_data ) ) {
				return;
			}

			$today = $this->current_time();

			$this->data = Helper::get_merged_data( $sheet_data, $today, $this->date_increment( $today, 10 ) );

			$this->is_active = wp_validate_boolean( $this->data['is_campaign'] );
		}
	}
}