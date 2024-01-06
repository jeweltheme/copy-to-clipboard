<?php
namespace COPYTOCLIPBOARD\Inc\Classes;

use COPYTOCLIPBOARD\Libs\Upgrader;

if ( ! class_exists( 'Upgrade_Plugin' ) ) {
	/**
	 * Upgrade Plugin Class
	 *
	 * Jewel Theme <support@jeweltheme.com>
	 */
	class Upgrade_Plugin extends Upgrader {

		/**
		 * Lists of upgrades
		 *
		 * @var string[]
		 */
		protected $upgrades = array(
			'1.0.2' => 'Inc/Upgrades/upgrade-1.0.2.php', // path should be from root except trailingslash of the beginning.
		);
	}
}