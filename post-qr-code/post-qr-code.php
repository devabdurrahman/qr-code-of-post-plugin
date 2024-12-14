<?php 
/**
 * Plugin Name:						post qr code
 * Plugin URI:						
 * Description:						Displays QR code
 * Version:							1.0
 * Requires at Least:				5.2
 * Requires PHP:					7.2
 * Author:							Abdur Rahman
 * Author URI:						https://devabdurrahman.com/
 * License:							GPL2
 * License URI:						https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:						post-qr-code
 */

// Prevent direct access to the file
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// register_activation_hook( __FILE__, "wordcount_activation_hook" );
// function wordcount_activation_hook(){

// }
// register_deactivation_hook( __FILE__, "wordcount_deactivation_hook" );
// function wordcount_deactivation_hook(){

// }

function pqrc_load_textdomain(){
	load_plugin_textdomain('post-qr-code', false, dirname(__FILE__)."/languages");
}
add_action("plugins_loaded" , "pqrc_load_textdomain");

function pqrc_display_qr_code($content){
	$current_post_id = get_the_id();
	$current_post_title = get_the_title($current_post_id);
	$current_post_url = urlencode(get_the_permalink($current_post_id));
	$current_post_type = get_post_type($current_post_id);

	// post type check
	$excluded_post_types = apply_filters('pqrc_excluded_post_types', array());
	if(in_array($current_post_type, $excluded_post_types)){
		return $content;
	}

	// dimensions hook
	$dimensions = apply_filters('pqrc_qrcode_dimensions', '150x150');


	$image_src = sprintf('https://api.qrserver.com/v1/create-qr-code/?size=%s&data=%s', $dimensions, $current_post_url);
	$content .= sprintf("<img src='%s' alt='%s'/>", $image_src, $current_post_title);

	return $content;
}

add_filter('the_content', 'pqrc_display_qr_code');