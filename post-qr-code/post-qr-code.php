<?php 
/**
 * Plugin Name:						post qr code
 * Plugin URI:						
 * Description:						Displays QR code
 * Version:						1.0
 * Requires at Least:					5.2
 * Requires PHP:					7.2
 * Author:						Abdur Rahman
 * Author URI:						https://devabdurrahman.com/
 * License:						GPL2
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

function pqrc_load_textdomain() {
    load_plugin_textdomain('post-qr-code', false, dirname(__FILE__) . "/languages");
}
add_action("plugins_loaded", "pqrc_load_textdomain");

function pqrc_display_qr_code($content) {
    $current_post_id = get_the_id();
    $current_post_title = get_the_title($current_post_id);
    $current_post_url = urlencode(get_the_permalink($current_post_id));
    $current_post_type = get_post_type($current_post_id);

    // Post type check
    $excluded_post_types = apply_filters('pqrc_excluded_post_types', array());
    if (!is_array($excluded_post_types)) {
        $excluded_post_types = array();
    }
    if (in_array($current_post_type, $excluded_post_types)) {
        return $content;
    }

    // Dimensions hook
    $height = get_option('pqrc_height', 180);
    $width = get_option('pqrc_width', 180);
    $dimensions = apply_filters('pqrc_qrcode_dimensions', "{$width}x{$height}");

    $image_src = sprintf('https://api.qrserver.com/v1/create-qr-code/?size=%s&data=%s', $dimensions, $current_post_url);
    $content .= sprintf(
        "<img src='%s' alt='%s' width='%d' height='%d'/>",
        esc_url($image_src),
        esc_attr($current_post_title),
        $width,
        $height
    );

    return $content;
}
add_filter('the_content', 'pqrc_display_qr_code');

function pqrc_settings_init() {
    add_settings_field('pqrc_height', __('QR Code Height', 'post-qr-code'), 'pqrc_display_height', 'general');
    add_settings_field('pqrc_width', __('QR Code Width', 'post-qr-code'), 'pqrc_display_width', 'general');

    register_setting('general', 'pqrc_height', array('sanitize_callback' => 'absint'));
    register_setting('general', 'pqrc_width', array('sanitize_callback' => 'absint'));
}

function pqrc_display_height() {
    $height = get_option('pqrc_height', 180);
    printf("<input type='text' id='%s' name='%s' value='%s'>", 'pqrc_height', 'pqrc_height', esc_attr($height));
}

function pqrc_display_width() {
    $width = get_option('pqrc_width', 180);
    printf("<input type='text' id='%s' name='%s' value='%s'>", 'pqrc_width', 'pqrc_width', esc_attr($width));
}
add_action('admin_init', 'pqrc_settings_init');
