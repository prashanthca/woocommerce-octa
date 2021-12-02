<?php
/*
Plugin Name: WooCommerce Octa Plugin
Plugin URI: https://github.com/prashanthca/woocommerce-octa
description: WooCommerce Octa Plugin
Version: 1.0
Author: Prashanth
Author URI: https://prashanthca.in
License: MIT
*/

define('WC_OCTA_PLUGIN_URL', plugin_dir_url(__FILE__));

function load_wc_octa_template_file( $template_name ) {
    $file = get_stylesheet_directory().'/woocommerce/'.$template_name;
    if (@file_exists($file)) {
        return $file;
    }
    $file = untrailingslashit(plugin_dir_path(__FILE__)).'/woocommerce/templates/'.$template_name;
    if (@file_exists($file)) {
        return $file;
    }
}

add_action('woocommerce_variation_options_pricing', function($loop, $variation_data, $variation){
	woocommerce_wp_text_input(array(
		'id' => 'custom_color_value',
		'class' => 'short',
		'style' => 'margin-left: 5px',
		'type' => 'color',
		'label' => __( 'Custom T-Shirt Color', 'woocommerce'),
		'value' => get_post_meta($variation->ID, 'custom_color', true)
	));
}, 10, 3);

add_action('woocommerce_save_product_variation', function($variation_id, $i){
	$custom_color = $_POST['custom_color_value'];
	if (isset($custom_color)) update_post_meta($variation_id, 'custom_color', esc_attr($custom_color));
}, 10, 2);

add_filter('woocommerce_available_variation', function($variations){
	$variations['custom_color'] = get_post_meta($variations['variation_id'], 'custom_color', true);
	return $variations;
});

add_filter('woocommerce_template_loader_files', function($templates, $template_name){
    // Capture/cache the $template_name which is a file name like single-product.php
    wp_cache_set('wc_octa_product_template', $template_name);
    return $templates;
}, 10, 2);

add_filter('template_include', function($template){
    if ( $template_name = wp_cache_get('wc_octa_product_template')) {
        wp_cache_delete('wc_octa_product_template');
        if ($file = load_wc_octa_template_file($template_name)) {
            return $file;
        }
    }
    return $template;
}, 11);

add_filter('wc_get_template_part', function($template, $slug, $name){
    $file = load_wc_octa_template_file("{$slug}-{$name}.php");
    return $file ? $file : $template;
}, 10, 3);

add_filter('woocommerce_locate_template', function($template, $template_name ){
    $file = load_wc_octa_template_file( $template_name );
    return $file ? $file : $template;
}, 10, 2);

add_filter( 'wc_get_template', function( $template, $template_name ){
    $file = load_wc_octa_template_file( $template_name );
    return $file ? $file : $template;
}, 10, 2);

?>