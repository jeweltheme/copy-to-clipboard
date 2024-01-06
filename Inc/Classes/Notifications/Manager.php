<?php
namespace COPYTOCLIPBOARD\Inc\Classes\Notifications;

use COPYTOCLIPBOARD\Inc\Classes\Notifications\Base\Data;

// No, Direct access Sir !!!
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Notice Manager
 *
 * Jewel Theme <support@jeweltheme.com>
 */
class Manager extends Data {

	/**
	 * Constructor method
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function __construct() {

		// Register Ask_For_Rating Notice.
		$this->register( new Ask_For_Rating() );

		// Register Subscribe Notice .
		$this->register( new Subscribe() );

		// Register What we Collect Notice .
		$this->register( new What_We_Collect() );

		// Register Upgrade_Notice Notice for managing all notices .
		$this->register( new Upgrade_Notice() );
	}
}