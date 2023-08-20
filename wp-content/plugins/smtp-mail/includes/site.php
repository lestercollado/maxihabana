<?php
defined('ABSPATH') or die();

// Stop wp
function smtpmail_shutdown()
{
    // This is our shutdown function, in 
    // here we can do any last operations
    // before the script is complete.

	// echo 'Script executed with success', PHP_EOL;

	// Check wp has send mail 
	if( isset($_SERVER['SMTPMAIL_WP_MAIL_SENDING']) && $_SERVER['SMTPMAIL_WP_MAIL_SENDING'] ) {
		$_SERVER['SMTPMAIL_WP_MAIL_SENDING'] = false;

		smtpmail_update_data( array(
										'status' => 1,
										'modified' => current_time( 'mysql' ),
									) );
	}

}
register_shutdown_function('smtpmail_shutdown');

/**
 * WP Check Html
 *
 * @since 1.1.8
 *
 */
function smtpmail_wp_check_html( $html = '' )
{
	// Development

	return $html;
}

/**
 * Scripts
 *
 * @since 1.2.13
 *
 */
function smtpmail_enqueue_scripts() 
{
	$settings =  shortcode_atts(array(
		'anti_spam_form' => 0,
	), (array)get_option('smtpmail_options'));

	// Scripts
	wp_enqueue_script( 'security', smtpmail_assets_url('security.js'),  array('jquery'), '1.2.13', true );
	wp_localize_script( 'security', 'security_setting', $settings );
}
add_action( 'wp_enqueue_scripts', 'smtpmail_enqueue_scripts', 99 );