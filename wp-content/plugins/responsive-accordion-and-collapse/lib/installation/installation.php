<?php 
add_action('plugins_loaded', 'wpsm_accordion_tr');
function wpsm_accordion_tr() {
	load_plugin_textdomain( wpshopmart_accordion_text_domain, FALSE, dirname( plugin_basename(__FILE__)).'/language/' );
}
// plugin front script loading
function wpsm_ac_front_script() {
    
		wp_enqueue_script('jquery');
		
		//font awesome css
		wp_enqueue_style('wpsm_ac-font-awesome-front', wpshopmart_accordion_directory_url.'css/font-awesome/css/font-awesome.min.css');
		wp_enqueue_style('wpsm_ac_bootstrap-front', wpshopmart_accordion_directory_url.'css/bootstrap-front.css');
		
		//wp_enqueue_script( 'wpsm_ac_bootstrap-js-front', wpshopmart_accordion_directory_url.'js/bootstrap.js', array(), '', true );
		wp_enqueue_script( 'call_ac-custom-js-front', wpshopmart_accordion_directory_url.'js/accordion-custom.js', array(), '', true );
		wp_enqueue_script( 'call_ac-js-front', wpshopmart_accordion_directory_url.'js/accordion.js', array(), '', true );

}
add_action( 'wp_enqueue_scripts', 'wpsm_ac_front_script' );

add_filter( 'widget_text', 'do_shortcode');



function wpsm_ac_header_info() {
 	if(get_post_type()=="responsive_accordion") {
		?>
		<style>
		.wpsm_ac_h_i{
			background:url('<?php echo esc_url(wpshopmart_accordion_directory_url.'img/slideshow-01.jpg'); ?>') 50% 0 no-repeat fixed;
			-webkit-box-shadow: 0px 13px 21px -10px rgba(128,128,128,1);
			-moz-box-shadow: 0px 13px 21px -10px rgba(128,128,128,1);
			box-shadow: 0px 13px 21px -10px rgba(128,128,128,1);
			margin-left: -20px;
			font-family: Myriad Pro ;
			cursor: pointer;
			text-align: center;
		}
		.wpsm_ac_h_i .wpsm_ac_h_b{
			color: white;
			font-size: 30px;
			font-weight: bolder;
			padding: 0 0 15px 0;
		}
		.wpsm_ac_h_i .wpsm_ac_h_b .dashicons{
			font-size: 40px;
			position: absolute;
			margin-left: -45px;
			margin-top: -10px;
		}
		 .wpsm_ac_h_small{
			font-weight: bolder;
			color: white;
			font-size: 18px;
			padding: 0 0 15px 15px;
		}
		.wpsm_ac_h_i a{
		text-decoration: none;
		}
		@media screen and ( max-width: 600px ) {
			.wpsm_ac_h_i{ padding-top: 60px; margin-bottom: -50px; }
			.wpsm_ac_h_i .WlTSmall { display: none; }
		}
		.texture-layer {
			background: rgba(0,0,0,0);
			padding-top: 0px;
			padding: 0px 0 23px 0;
		}
		.wpsm_ac_h_i  ul{
			padding:0px 20px 0px 20px;
		}
		.wpsm_ac_h_i  li {
			text-align:left;
			color:#fff;
			font-size: 20px;
			line-height: 1.3;
			font-weight: 600;
			
		}
		.wpsm_ac_h_i  li i{
			margin-right:10px ;
			margin-bottom:10px;		
		}
		.wpsm_ac_h_i .btn-danger{
			font-size: 29px;
			background-color: #000;
			border-radius:1px;
			margin-right:10px;
			margin-top: 0px;
			border-color:#000;		 
		}
		.wpsm_ac_h_i .btn-success{
			font-size: 28px;
			border-radius:1px;
			background-color: #fff;
			border-color: #fff;
			color:#000;
		}
		.pro-div-wrapper{
			overflow:hidden;
			/*display:block;*/
			width:100%;
			margin-left: 0;
		}
		.wpsm_ac_h_b .btn-danger{
			color: #fff;
		}
		</style>
		<div class="wpsm_ac_h_i ">
			<div class="texture-layer">
				
				<div class="wpsm_ac_h_b"><a class="btn btn-danger btn-lg " href="https://wpshopmart.com/plugins/accordion-pro/" target="_blank"><?php esc_html_e('Try Accordion/FAQ Pro Now',wpshopmart_accordion_text_domain); ?></a><a class="btn btn-success btn-lg " href="http://demo.wpshopmart.com/accordion-pro/" target="_blank"><?php esc_html_e('View Demo',wpshopmart_accordion_text_domain); ?></a></div>
				<div style="overflow:hidden;display:block;width:100%;text-align:center">
					<h1 style="color:#fff;font-size:30px;text-transform:uppercase"><?php esc_html_e('Unlock More Features In Pro version',wpshopmart_accordion_text_domain); ?></h1>
				</div>
				<a href="https://wpshopmart.com/plugins/accordion-pro/" target="_blank">
					<div class="row pro-div-wrapper">
						<div class="col-md-3">
								<ul>
									<li> <i class="fa fa-check"></i><?php esc_html_e('18+ Design Templates',wpshopmart_accordion_text_domain); ?> </li>
									<li> <i class="fa fa-check"></i><?php esc_html_e('30 Content Animations',wpshopmart_accordion_text_domain); ?> </li>
									<li> <i class="fa fa-check"></i><?php esc_html_e('Individual Color FAQ',wpshopmart_accordion_text_domain); ?> </li>
									<li> <i class="fa fa-check"></i><?php esc_html_e('Add  Custom Image Icon',wpshopmart_accordion_text_domain); ?> </li>
									<li> <i class="fa fa-check"></i><?php esc_html_e('12 Open/Close Icons Sets',wpshopmart_accordion_text_domain); ?> </li>
									
								</ul>
						</div>
						<div class="col-md-3">
								<ul>
									<li> <i class="fa fa-check"></i><?php esc_html_e('4 Overlay Effect',wpshopmart_accordion_text_domain); ?> </li>
									<li> <i class="fa fa-check"></i><?php esc_html_e('500+ Google Fonts',wpshopmart_accordion_text_domain); ?> </li>
									<li> <i class="fa fa-check"></i><?php esc_html_e('Accordion Scroll Effect',wpshopmart_accordion_text_domain); ?> </li>
									<li> <i class="fa fa-check"></i><?php esc_html_e('Set Accordion Height',wpshopmart_accordion_text_domain); ?> </li>
									<li> <i class="fa fa-check"></i><?php esc_html_e('On Hover Accordion',wpshopmart_accordion_text_domain); ?> </li>
									
								</ul>	
						</div>
						<div class="col-md-3">
								<ul>
									<li> <i class="fa fa-check"></i><?php esc_html_e('Widget Option',wpshopmart_accordion_text_domain); ?> </li>
									<li> <i class="fa fa-check"></i><?php esc_html_e('Unlimited Shortcode',wpshopmart_accordion_text_domain); ?> </li>
									<li> <i class="fa fa-check"></i><?php esc_html_e('Unlimited Color Scheme',wpshopmart_accordion_text_domain); ?> </li>
									<li> <i class="fa fa-check"></i><?php esc_html_e('Drag And Drop Builder',wpshopmart_accordion_text_domain); ?> </li>
									<li> <i class="fa fa-check"></i><?php esc_html_e('Preview Option',wpshopmart_accordion_text_domain); ?> </li>
								</ul>
						</div>
						<div class="col-md-3">
								<ul>
									<li> <i class="fa fa-check"></i><?php esc_html_e('Border Customization',wpshopmart_accordion_text_domain); ?> </li>
									<li> <i class="fa fa-check"></i><?php esc_html_e('Collapse Mode',wpshopmart_accordion_text_domain); ?> </li>
									<li> <i class="fa fa-check"></i><?php esc_html_e('Border Color Customization',wpshopmart_accordion_text_domain); ?> </li>
									<li> <i class="fa fa-check"></i><?php esc_html_e('High Priority Support',wpshopmart_accordion_text_domain); ?> </li>
									<li> <i class="fa fa-check"></i><?php esc_html_e('All Browser Compatible',wpshopmart_accordion_text_domain); ?> </li>
								</ul>	
						</div>
						
					</div>
				</a>
					
				
			</div>
			
		</div>
		
		<?php  
	}
}
add_action('in_admin_header','wpsm_ac_header_info'); 

add_action( 'admin_notices', 'wpsm_acc_r_review' );
function wpsm_acc_r_review() {

	// Verify that we can do a check for reviews.
	$review = get_option( 'wpsm_acc_r_review' );
	$time	= time();
	$load	= false;
	if ( ! $review ) {
		$review = array(
			'time' 		=> $time,
			'dismissed' => false
		);
		add_option('wpsm_acc_r_review', $review);
		//$load = true;
	} else {
		// Check if it has been dismissed or not.
		if ( (isset( $review['dismissed'] ) && ! $review['dismissed']) && (isset( $review['time'] ) && (($review['time'] + (DAY_IN_SECONDS * 2)) <= $time)) ) {
			$load = true;
		}
	}
	// If we cannot load, return early.
	if ( ! $load ) {
		return;
	}

	// We have a candidate! Output a review message.
	?>
	<div class="notice notice-info is-dismissible wpsm-acc-r-review-notice">
		<div style="float:left;margin-right:10px;margin-bottom:5px;">
			<img style="width:100%;width: 150px;height: auto;" src="<?php echo esc_url(wpshopmart_accordion_directory_url.'img/icon-show.png'); ?>" />
		</div>
		<p style="font-size:18px;"><?php esc_html_e('Hi! We saw you have been using ',wpshopmart_accordion_text_domain); ?><strong><?php esc_html_e('Responsive Accordion And Collapse plugin',wpshopmart_accordion_text_domain); ?></strong><?php esc_html_e(' for a few days and wanted to ask for your help to ',wpshopmart_accordion_text_domain); ?><strong><?php esc_html_e('make the plugin better',wpshopmart_accordion_text_domain); ?></strong><?php esc_html_e(' .We just need a minute of your time to rate the plugin. Thank you!',wpshopmart_accordion_text_domain); ?></p>
		<p style="font-size:18px;"><strong><?php _e( '~ wpshopmart', '' ); ?></strong></p>
		<p style="font-size:19px;"> 
			<a style="color: #fff;background: #ef4238;padding: 5px 7px 4px 6px;border-radius: 4px;" href="https://wordpress.org/support/plugin/responsive-accordion-and-collapse/reviews/?filter=5" class="wpsm-acc-r-dismiss-review-notice wpsm-acc-r-review-out" target="_blank" rel="noopener"><?php esc_html_e('Rate the plugin',wpshopmart_accordion_text_domain); ?></a>&nbsp; &nbsp;
			<a style="color: #fff;background: #27d63c;padding: 5px 7px 4px 6px;border-radius: 4px;" href="#"  class="wpsm-acc-r-dismiss-review-notice wpsm-rate-later" target="_self" rel="noopener"><?php _e( 'Nope, maybe later', '' ); ?></a>&nbsp; &nbsp;
			<a style="color: #fff;background: #31a3dd;padding: 5px 7px 4px 6px;border-radius: 4px;" href="#" class="wpsm-acc-r-dismiss-review-notice wpsm-rated" target="_self" rel="noopener"><?php _e( 'I already did', '' ); ?></a>

			</p>
	</div>
	<script type="text/javascript">
		jQuery(document).ready( function($) {
			$(document).on('click', '.wpsm-acc-r-dismiss-review-notice, .wpsm-acc-r-dismiss-notice .notice-dismiss', function( event ) {
				if ( $(this).hasClass('wpsm-acc-r-review-out') ) {
					var wpsm_rate_data_val = "1";
				}
				if ( $(this).hasClass('wpsm-rate-later') ) {
					var wpsm_rate_data_val =  "2";
					event.preventDefault();
				}
				if ( $(this).hasClass('wpsm-rated') ) {
					var wpsm_rate_data_val =  "3";
					event.preventDefault();
				}

				$.post( ajaxurl, {
					action: 'wpsm_acc_r_dismiss_review',
					wpsm_rate_data_acc_r : wpsm_rate_data_val
				});
				
				$('.wpsm-acc-r-review-notice').hide();
				//location.reload();
			});
		});
	</script>
	<?php
}

add_action( 'wp_ajax_wpsm_acc_r_dismiss_review', 'wpsm_acc_r_dismiss_review' );
function wpsm_acc_r_dismiss_review() {
	if ( ! $review ) {
		$review = array();
	}
	
	if($_POST['wpsm_rate_data_acc_r']=="1"){
		$review['time'] 	 = time();
		$review['dismissed'] = false;
		
	}
	if($_POST['wpsm_rate_data_acc_r']=="2"){
		$review['time'] 	 = time();
		$review['dismissed'] = false;
		
	}
	if($_POST['wpsm_rate_data_acc_r']=="3"){
		$review['time'] 	 = time();
		$review['dismissed'] = true;
		
	}
	update_option( 'wpsm_acc_r_review', $review );
	die;
}
?>