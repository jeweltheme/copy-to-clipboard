<?php
namespace COPYTOCLIPBOARD\Inc\Classes;

use COPYTOCLIPBOARD\Libs\Helper;
use COPYTOCLIPBOARD\Inc\Classes\Notifications\Base\Date;


// No, Direct access Sir !!!
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Upgrade to Pro Class
 *
 * Jewel Theme <support@jeweltheme.com>
 */
class Pro_Upgrade {

	use Date;

	public $slug;

	protected $data = array();

	protected $modes = array(
		'development' => array(
			'sheet_id' => '1VLpfKspHHNM6JIFOQtohqDRyHR85J3KR5RLF4jqlz0Q',
			'tab_id'   => 0,
		),
		'production'  => array(
			'sheet_id' => '1VLpfKspHHNM6JIFOQtohqDRyHR85J3KR5RLF4jqlz0Q',
			'tab_id'   => 0,
		),
	);

	/**
	 * Construct method
	 */
	public function __construct() {
		$this->slug = Helper::copy_to_clipboard_slug_cleanup();

		$this->maybe_sync_remote_data();
		$this->register_sync_hook();
		$this->set_data();

		add_action( 'admin_footer', array( $this, 'display_popup' ) );

		add_action( 'wp_dashboard_setup', array( $this, 'dashboard_widget' ) );
	}

	/**
	 * Register Dashboard widget
	 *
	 * @return void
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function dashboard_widget() {
		wp_add_dashboard_widget(
			'copy_to_clipboard_dashboard_widget',                          // Widget slug.
			esc_html__( 'copy-to-clipboard News & Updates', 'copy-to-clipboard' ), // Title.
			array( $this, 'dashboard_widget_render' )                    // Display function.
		);

		// Globalize the metaboxes array, this holds all the widgets for wp-admin.
		global $wp_meta_boxes;

		// Get the regular dashboard widgets array
		// (which already has our new widget but appended at the end).
		$default_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];

		// Backup and delete our new dashboard widget from the end of the array.
		$example_widget_backup = array( 'copy_to_clipboard_dashboard_widget' => $default_dashboard['copy_to_clipboard_dashboard_widget'] );
		unset( $default_dashboard['copy_to_clipboard_dashboard_widget'] );

		// Merge the two arrays together so our widget is at the beginning.
		$sorted_dashboard = array_merge( $example_widget_backup, $default_dashboard );

		// Save the sorted array back into the original metaboxes .
		$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
	}

	/**
	 * Render dashboard widget
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function dashboard_widget_render() {
		include_once ABSPATH . WPINC . '/feed.php';

		$feed_url = 'https://jeweltheme.com/feed.xml';

		// Get a SimplePie feed object from the specified feed source .
		$rss      = fetch_feed( $feed_url );
		$maxitems = 0;

		if ( ! is_wp_error( $rss ) ) { // Checks that the object is created correctly .
			// Figure out how many total items there are, and choose a limit .
			$maxitems = $rss->get_item_quantity( 5 );

			// Build an array of all the items, starting with element 0 (first element).
			$rss_items = $rss->get_items( 0, $maxitems );

			// Get RSS title .
			$rss_title = '<a href="' . $rss->get_permalink() . '" target="_blank">' . strtoupper( $rss->get_title() ) . '</a>';
		}

		// Display the container .
		echo '<div class="copy-to-clipboard-rss-widget">';

		if ( wp_validate_boolean( $this->get_content( 'is_campaign' ) ) ) { ?>

			<div class="copy-to-clipboard-dashboard-promo" style="--copy-to-clipboard-popup-color: <?php echo esc_attr( $this->get_content( 'btn_color' ) ); ?>;">
				<a target="_blank" href="<?php echo esc_url( $this->get_content( 'button_url' ) ); ?>">
					<img src="<?php echo esc_url( $this->get_content( 'image_url' ) ); ?>" alt="copy-to-clipboard Promo Image" style="width: 100%; height: auto;">
				</a>

				<a class="copy-to-clipboard-popup-button" target="_blank" href="<?php echo esc_url( $this->get_content( 'button_url' ) ); ?>">
					<?php echo esc_html( $this->get_content( 'button_text' ) ); ?>
				</a>
			</div>

			<?php
		}

		// Starts items listing within <ul> tag
		// Check items .
		if ( ! empty( $maxitems ) ) {
			echo '<ul>';
			// Loop through each feed item and display each item as a hyperlink.
			foreach ( $rss_items as $item ) {
				// Uncomment line below to display non human date
				// $item_date = $item->get_date( get_option('date_format').' @ '.get_option('time_format') ); .

				// Get human date (comment if you want to use non human date) .
				// $item_date = human_time_diff( $item->get_date( 'U' ), current_time( 'timestamp' ) ) . ' ' . esc_html__( 'ago', 'copy-to-clipboard' );

				// Start displaying item content within a <li> tag .
				echo '<li>';
				// create item link .
				echo '<a href="' . esc_url( $item->get_permalink() ) . '" title="' . esc_attr( $item->get_title() ) . '" target="_blank">';
				// Get item title .
				echo esc_html( $item->get_title() );
				echo '</a>';
				// Display date .
				//echo ' <span class="rss-date">' . esc_html( $item_date ) . '</span><br />';
				// Get item content .
				$content = $item->get_content();
				// Shorten content .
				$content = wp_html_excerpt( $content, 120 ) . ' ';
				// Display content .
				echo esc_html( $content );
				// End <li> tag .
				echo '</li>';
			}
			echo '</ul>';
			// End <ul> tag .
		}
		?>

		<div class="copy-to-clipboard-dashboard_footer">
			<ul>
				<li class="copy-to-clipboard-overview__blog">
                    <a href="https://jeweltheme.com/blog" target="_blank">
                        Blog
                        <span class="screen-reader-text">
                            <?php echo esc_html__( '(opens in a new window)', 'copy-to-clipboard' ); ?>
                        </span>
                        <span aria-hidden="true" class="dashicons dashicons-external"></span>
                    </a>
                </li>
                <li class="copy-to-clipboard-overview__help">
                    <a href="https://jeweltheme.com/docs" target="_blank">
                        Help
                        <span class="screen-reader-text">
                            <?php echo esc_html__( '(opens in a new window)', 'copy-to-clipboard' ); ?>
                        </span>
                        <span aria-hidden="true" class="dashicons dashicons-external"></span>
                    </a>
                </li>
                <li class="copy-to-clipboard-overview__upgrade">
                    <a href="https://jeweltheme.com" target="_blank">
                        Upgrade
                        <span class="screen-reader-text">
                            <?php echo esc_html__( '(opens in a new window)', 'copy-to-clipboard' ); ?>
                        </span>
                        <span aria-hidden="true" class="dashicons dashicons-external"></span>
                    </a>
                </li>
                
			</ul>
		</div>
		<style>
			/* News Dashboard Widget */
			.copy-to-clipboard-rss-widget .hndle.ui-sortable-handle img {
				margin: -5px 10px -5px 0;
			}

			.copy-to-clipboard-rss-widget .copy-to-clipboard-dashboard_footer {
				margin: 0 -12px -12px;
				padding: 0 12px;
				border-top: 1px solid #eee;
			}

			.copy-to-clipboard-rss-widget .copy-to-clipboard-dashboard_footer ul {
				display: flex;
				list-style: none;
			}

			.copy-to-clipboard-rss-widget .copy-to-clipboard-dashboard_footer ul li:first-child {
				padding-left: 0;
				border: none;
			}

			.copy-to-clipboard-rss-widget .copy-to-clipboard-dashboard_footer li {
				padding: 0 10px;
				margin: 0;
				border-left: 1px solid #ddd;
			}

			.copy-to-clipboard-rss-widget .copy-to-clipboard-overview__go-pro a {
				color: #FCB92C;
				font-weight: 500;
			}
		</style>

		<?php
		echo '</div>';
	}


	/**
	 * Set merged data
	 *
	 * @return void
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function set_data() {
		$this->data = Helper::get_merged_data( self::get_data() );
	}

	/**
	 * Get Sheet data
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public static function get_data() {
		return get_option( 'copy_to_clipboard_sheet_promo_data' );
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
	 * Get Option has data
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function get_data_hash() {
		return get_option( 'copy_to_clipboard_sheet_promo_data_hash' );
	}

	/**
	 * Sync to remote data
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function maybe_sync_remote_data() {
		$data = self::get_data();

		if ( empty( $data ) ) {
			$this->sheet_data_remote_sync();
		}
	}

	/**
	 * Register Sync hook
	 *
	 * @return void
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function register_sync_hook() {
		$hook_action = 'copy_to_clipboard_sheet_promo_data_remote_sync';
		add_action( $hook_action, array( $this, 'sheet_data_remote_sync' ) );

		if ( ! wp_next_scheduled( $hook_action ) ) {
			wp_schedule_event( time(), 'daily', $hook_action );
		}

		register_deactivation_hook( COPYTOCLIPBOARD_FILE, array( $this, 'clear_register_sync_hook' ) );
	}

	/**
	 * Clear register sync hook
	 *
	 * @return void
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function clear_register_sync_hook() {
		wp_clear_scheduled_hook( 'copy_to_clipboard_sheet_promo_data_remote_sync' );
	}

	/**
	 * Data sync with remote
	 *
	 * @return void
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function sheet_data_remote_sync() {
		$data  = self::get_data();
		$force = false;

		if ( empty( $data ) ) {
			$force = true;
		}

		$sheet_hash_data = $this->get_data_hash();
		$remote_data     = $this->get_sheet_promo_remote_data();
		$sheet_data_hash = base64_encode( json_encode( $remote_data ) );

		if ( $force || $sheet_hash_data !== $sheet_data_hash ) {
			update_option( 'copy_to_clipboard_sheet_promo_data', $remote_data );
			update_option( 'copy_to_clipboard_sheet_promo_data_hash', $sheet_data_hash );
			do_action( 'copy_to_clipboard_sheet_promo_data_reset' );
		}
	}

	/**
	 * Get Environment mode
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function get_mode() {
		return defined( 'WP_DEBUG' ) && WP_DEBUG ? 'development' : 'production';
	}

	/**
	 * Get Sheet URL
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function get_sheet_url() {
		$sheet_id = $this->modes[ $this->get_mode() ]['sheet_id'];
		$tab_id   = $this->modes[ $this->get_mode() ]['tab_id'];

		return "https://docs.google.com/spreadsheets/export?format=csv&id={$sheet_id}&gid={$tab_id}";
	}

	/**
	 * Promotional remote data
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function get_sheet_promo_remote_data() {

		$transient_key = $this->slug . '_sheet_promo_data';

		$data = get_transient($transient_key);
		if ($data !== false) return $data;

		$url = $this->get_sheet_url();

		$response = wp_remote_get($url);

		if (is_wp_error($response)) {
			return false;
		}

		$response = wp_remote_retrieve_body($response);

		if (!$response) {
			return false;
		}

		$data = array_map('str_getcsv', explode("
", $response));

		$header = array_shift($data);

		$data = array_map(function (array $row) use ($header) {
			return array_combine($header, $row);
		}, $data);

		// filter plugin is not empty .
		$data = array_filter($data, function ($row) {
			return !empty($row['name']);
		});

		$plugin_slug = Helper::copy_to_clipboard_slug_cleanup();
		$data        = wp_list_filter($data, array('product_slug' => $plugin_slug));

		if (!empty($data)) {
			$data = array_values($data)[0];
		}

		set_transient($transient_key, $data, HOUR_IN_SECONDS);

		return $data;
	}

	/**
	 * Display popup contents
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function display_popup() {
		$image_url = $this->get_content( 'image_url' );

		?>

		<div class="copy-to-clipboard-popup copy-to-clipboard-upgrade-popup" id="copy-to-clipboard-popup" data-plugin="<?php echo esc_attr( $this->slug ); ?>" tabindex="1" style="display: none;">

			<div class="copy-to-clipboard-popup-overlay"></div>

			<div class="copy-to-clipboard-popup-modal" style="background-image: url('<?php echo esc_url( $image_url ); ?>'); --copy-to-clipboard-popup-color: <?php echo esc_attr( $this->get_content( 'btn_color' ) ); ?>;">

				<!-- close  -->
				<div class="copy-to-clipboard-popup-modal-close popup-dismiss">×</div>

				<!-- content section  -->
				<div class="copy-to-clipboard-popup-modal-footer">

					<!-- countdown  -->
					<div class="copy-to-clipboard-popup-countdown" style="display: none;">
						<?php if(!empty( $this->get_content( 'notice' ) )){ ?>
							<span data-counter="notice" style="color:#F4B740; font-size:14px; padding-bottom:20px; font-style:italic;">
								<?php echo esc_html__( 'Notice:', 'copy-to-clipboard' );?> <?php echo $this->get_content( 'notice' );?>
							</span>
						<?php } ?>
						<span class="copy-to-clipboard-popup-countdown-text"><?php echo esc_html__( 'Offer Ends In', 'copy-to-clipboard' );?></span>
						<div class="copy-to-clipboard-popup-countdown-time">
							<div>
								<span data-counter="days">00</span>
								<span><?php echo esc_html__( 'Days', 'copy-to-clipboard' );?></span>
							</div>
							<span>:</span>
							<div>
								<span data-counter="hours">00</span>
								<span><?php echo esc_html__( 'Hours', 'copy-to-clipboard' );?></span>
							</div>
							<span>:</span>
							<div>
								<span data-counter="minutes">00</span>
								<span><?php echo esc_html__( 'Minutes', 'copy-to-clipboard' );?></span>
							</div>
							<span>:</span>
							<div>
								<span data-counter="seconds">00</span>
								<span><?php echo esc_html__( 'Seconds', 'copy-to-clipboard' );?></span>
							</div>
						</div>
					</div>

					<!-- button  -->
					<a class="copy-to-clipboard-popup-button" target="_blank" href="<?php echo esc_url( $this->get_content( 'button_url' ) ); ?>"><?php echo esc_html( $this->get_content( 'button_text' ) ); ?></a>
				</div>
			</div>
		</div>

		<script>
			var $container = jQuery('#copy-to-clipboard-popup'),
				plugin_data = <?php echo json_encode( $this->get_sheet_promo_remote_data(), true ); ?>,
				events = {}; //Events

			// Update Counter
			function updateCounter(seconds) {
				const $counter = $container.find(".copy-to-clipboard-popup-countdown-time");
				const $days = $counter.find("[data-counter='days']");
				const $hours = $counter.find("[data-counter='hours']");
				const $minutes = $counter.find("[data-counter='minutes']");
				const $seconds = $counter.find("[data-counter='seconds']");
				const days = Math.floor(seconds / (3600 * 24));
				seconds -= days * 3600 * 24;
				const hrs = Math.floor(seconds / 3600);
				seconds -= hrs * 3600;
				const mnts = Math.floor(seconds / 60);
				seconds -= mnts * 60;

				$days.text(days);
				$hours.text(hrs);
				$minutes.text(mnts);
				$seconds.text(seconds);
			}

			// Trigger Event
			function trigger(event, args = []) {
				if (typeof(events[event]) !== 'undefined') {
					events[event].forEach(callback => {
						callback.apply(this, args);
					});
				}
			}

			// initCounter
			function initCounter(last_date) {
				$container.find(".copy-to-clipboard-popup-countdown-time").show();

				const countdown = () => {

					// system time
					const now = new Date().getTime();

					// set end time to 11:59:59 PM
					const endDate = new Date(last_date);
					endDate.setHours(23);
					endDate.setMinutes(59);
					endDate.setSeconds(59);

					const seconds = Math.floor((endDate.getTime() - now) / 1000);

					if (seconds < 0) {
						return false;
					}

					updateCounter(seconds);

					return true;
				}

				let result = countdown();


				if (result) {
					trigger("countdownStart", [plugin_data]);
					$container.find(".copy-to-clipboard-popup-countdown").show(0);
				} else {
					trigger("countdownFinish", [plugin_data]);
					$container.find(".copy-to-clipboard-popup-countdown").hide(0);
				}

				// update counter every 1 second
				const counter = setInterval(() => {

					const result = countdown();

					if (!result) {
						clearInterval(counter);
						trigger("counter_end", [plugin_data]);
						$container.find(".copy-to-clipboard-popup-countdown").hide(0);
					}

				}, 1000);
			}

			initCounter( '<?php echo esc_attr( $this->counter_date() ); ?>' );
		</script>

		<?php
	}

	/**
	 * Counter Date
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function counter_date() {
		$endDate = $this->get_content( 'end_date' );

		$is_active = $this->date_is_current_or_next( $endDate );

		if ( $is_active ) {
			return $endDate;
		}

		return $this->date_increment( $this->current_time(), 3 );
	}
}