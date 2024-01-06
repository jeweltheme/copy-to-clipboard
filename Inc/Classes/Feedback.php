<?php
namespace COPYTOCLIPBOARD\Inc\Classes;

use COPYTOCLIPBOARD\Inc\Classes\Notifications\Base\User_Data;

// No, Direct access Sir !!!
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Feedback
 *
 * @author Jewel Theme <support@jeweltheme.com>
 */
class Feedback {

    use User_Data;

	/**
	 * Construct Method
	 *
	 * @return void
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
    public function __construct(){
        add_action( 'admin_enqueue_scripts' , [ $this,'admin_suvery_scripts'] );
        add_action( 'admin_footer' , [ $this , 'deactivation_footer' ] );
        add_action( 'wp_ajax_copy_to_clipboard_deactivation_survey', array( $this, 'copy_to_clipboard_deactivation_survey' ) );

    }


    public function proceed(){

        global $current_screen;
        if(
            isset($current_screen->parent_file)
            && $current_screen->parent_file == 'plugins.php'
            && isset($current_screen->id)
            && $current_screen->id == 'plugins'
        ){
           return true;
        }
        return false;

    }

    public function admin_suvery_scripts($handle){
        if('plugins.php' === $handle){
            wp_enqueue_style( 'copy-to-clipboard-survey' , COPYTOCLIPBOARD_ASSETS . 'css/copy-to-clipboard-survey.css' );
        }
    }

    /**
     * Deactivation Survey
     */
    public function copy_to_clipboard_deactivation_survey(){
        check_ajax_referer( 'copy_to_clipboard_deactivation_nonce' );

        $deactivation_reason  = ! empty( $_POST['deactivation_reason'] ) ? sanitize_text_field( wp_unslash( $_POST['deactivation_reason'] ) ) : '';

        if( empty( $deactivation_reason )){
            return;
        }

        $email = get_bloginfo( 'admin_email' );
        $author_obj = get_user_by( 'email', $email );
        $user_id    = $author_obj->ID;
        $full_name  = $author_obj->display_name;

        $response = $this->get_collect_data( $user_id, array(
            'first_name'              => $full_name,
            'email'                   => $email,
            'deactivation_reason'     => $deactivation_reason,
        ) );

        return $response;
    }


    public function get_survey_questions(){

        return [
			'no_longer_needed' => [
				'title' => esc_html__( 'I no longer need the plugin', 'copy-to-clipboard' ),
				'input_placeholder' => '',
			],
			'found_a_better_plugin' => [
				'title' => esc_html__( 'I found a better plugin', 'copy-to-clipboard' ),
				'input_placeholder' => esc_html__( 'Please share which plugin', 'copy-to-clipboard' ),
			],
			'couldnt_get_the_plugin_to_work' => [
				'title' => esc_html__( 'I couldn\'t get the plugin to work', 'copy-to-clipboard' ),
				'input_placeholder' => '',
			],
			'temporary_deactivation' => [
				'title' => esc_html__( 'It\'s a temporary deactivation', 'copy-to-clipboard' ),
				'input_placeholder' => '',
			],
			'copy_to_clipboard_pro' => [
				'title' => sprintf( esc_html__( 'I have %1$s Pro', 'copy-to-clipboard' ), COPYTOCLIPBOARD ),
				'input_placeholder' => '',
				'alert' => sprintf( esc_html__( 'Wait! Don\'t deactivate %1$s. You have to activate both %1$s and %1$s Pro in order for the plugin to work.', 'copy-to-clipboard' ), COPYTOCLIPBOARD ),
			],
			'need_better_design' => [
				'title' => esc_html__( 'I need better design and presets', 'copy-to-clipboard' ),
				'input_placeholder' => esc_html__( 'Let us know your thoughts', 'copy-to-clipboard' ),
			],
            'other' => [
				'title' => esc_html__( 'Other', 'copy-to-clipboard' ),
				'input_placeholder' => esc_html__( 'Please share the reason', 'copy-to-clipboard' ),
			],
		];
    }


        /**
         * Deactivation Footer
         */
        public function deactivation_footer(){

        if(!$this->proceed()){
            return;
        }

        ?>
        <div class="copy-to-clipboard-deactivate-survey-overlay" id="copy-to-clipboard-deactivate-survey-overlay"></div>
        <div class="copy-to-clipboard-deactivate-survey-modal" id="copy-to-clipboard-deactivate-survey-modal">
            <header>
                <div class="copy-to-clipboard-deactivate-survey-header">
                    <img src="<?php echo esc_url(COPYTOCLIPBOARD_IMAGES . '/menu-icon.png'); ?>" />
                    <h3><?php echo wp_sprintf( '%1$s %2$s', COPYTOCLIPBOARD, esc_html__( '- Feedback', 'copy-to-clipboard' ) ); ?></h3>
                </div>
            </header>
            <div class="copy-to-clipboard-deactivate-info">
                <?php echo wp_sprintf( '%1$s %2$s', esc_html__( 'If you have a moment, please share why you are deactivating', 'copy-to-clipboard' ), COPYTOCLIPBOARD ); ?>
            </div>
            <div class="copy-to-clipboard-deactivate-content-wrapper">
                <form action="#" class="copy-to-clipboard-deactivate-form-wrapper">
                    <?php foreach($this->get_survey_questions() as $reason_key => $reason){ ?>
                        <div class="copy-to-clipboard-deactivate-input-wrapper">
                            <input id="copy-to-clipboard-deactivate-feedback-<?php echo esc_attr($reason_key); ?>" class="copy-to-clipboard-deactivate-feedback-dialog-input" type="radio" name="reason_key" value="<?php echo $reason_key; ?>">
                            <label for="copy-to-clipboard-deactivate-feedback-<?php echo esc_attr($reason_key); ?>" class="copy-to-clipboard-deactivate-feedback-dialog-label"><?php echo esc_html( $reason['title'] ); ?></label>
							<?php if ( ! empty( $reason['input_placeholder'] ) ) : ?>
								<input class="copy-to-clipboard-deactivate-feedback-text" type="text" name="reason_<?php echo esc_attr( $reason_key ); ?>" placeholder="<?php echo esc_attr( $reason['input_placeholder'] ); ?>" />
							<?php endif; ?>
                        </div>
                    <?php } ?>
                    <div class="copy-to-clipboard-deactivate-footer">
                        <button id="copy-to-clipboard-dialog-lightbox-submit" class="copy-to-clipboard-dialog-lightbox-submit"><?php echo esc_html__( 'Submit &amp; Deactivate', 'copy-to-clipboard' ); ?></button>
                        <button id="copy-to-clipboard-dialog-lightbox-skip" class="copy-to-clipboard-dialog-lightbox-skip"><?php echo esc_html__( 'Skip & Deactivate', 'copy-to-clipboard' ); ?></button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            var deactivate_url = '#';

            jQuery(document).on('click', '#deactivate-copy-to-clipboard', function(e) {
                e.preventDefault();
                deactivate_url = e.target.href;
                jQuery('#copy-to-clipboard-deactivate-survey-overlay').addClass('copy-to-clipboard-deactivate-survey-is-visible');
                jQuery('#copy-to-clipboard-deactivate-survey-modal').addClass('copy-to-clipboard-deactivate-survey-is-visible');
            });

            jQuery('#copy-to-clipboard-dialog-lightbox-skip').on('click', function (e) {
                e.preventDefault();
                window.location.replace(deactivate_url);
            });


            jQuery(document).on('click', '#copy-to-clipboard-dialog-lightbox-submit', async function(e) {
                e.preventDefault();

                jQuery('#copy-to-clipboard-dialog-lightbox-submit').addClass('copy-to-clipboard-loading');

                var $dialogModal = jQuery('.copy-to-clipboard-deactivate-input-wrapper'),
                    radioSelector = '.copy-to-clipboard-deactivate-feedback-dialog-input';
                $dialogModal.find(radioSelector).on('change', function () {
                    $dialogModal.attr('data-feedback-selected', jQuery(this).val());
                });
                $dialogModal.find(radioSelector + ':checked').trigger('change');


                // Reasons for deactivation
                var deactivation_reason = '';
                var reasonData = jQuery('.copy-to-clipboard-deactivate-form-wrapper').serializeArray();

                jQuery.each(reasonData, function (reason_index, reason_value) {
                    if ('reason_key' == reason_value.name && reason_value.value != '') {
                        const reason_input_id = '#copy-to-clipboard-deactivate-feedback-' + reason_value.value,
                            reason_title = jQuery(reason_input_id).siblings('label').text(),
                            reason_placeholder_input = jQuery(reason_input_id).siblings('input').val(),
                            format_title_with_key = reason_value.value + ' - '  + reason_placeholder_input,
                            format_title = reason_title + ' - '  + reason_placeholder_input;

                        deactivation_reason = reason_value.value;

                        if ('found_a_better_plugin' == reason_value.value ) {
                            deactivation_reason = format_title_with_key;
                        }

                        if ('need_better_design' == reason_value.value ) {
                            deactivation_reason = format_title_with_key;
                        }

                        if ('other' == reason_value.value) {
                            deactivation_reason = format_title_with_key;
                        }
                    }
                });

                await jQuery.ajax({
                        url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
                        method: 'POST',
                        // crossDomain: true,
                        async: true,
                        // dataType: 'jsonp',
                        data: {
                            action: 'copy_to_clipboard_deactivation_survey',
                            _wpnonce: '<?php echo esc_js( wp_create_nonce( 'copy_to_clipboard_deactivation_nonce' ) ); ?>',
                            deactivation_reason: deactivation_reason
                        },
                        success:function(response){
                            window.location.replace(deactivate_url);
                        }
                });
                return true;
            });

            jQuery('#copy-to-clipboard-deactivate-survey-overlay').on('click', function () {
                jQuery('#copy-to-clipboard-deactivate-survey-overlay').removeClass('copy-to-clipboard-deactivate-survey-is-visible');
                jQuery('#copy-to-clipboard-deactivate-survey-modal').removeClass('copy-to-clipboard-deactivate-survey-is-visible');
            });
        </script>
        <?php
    }

}