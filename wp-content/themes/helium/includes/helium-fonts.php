<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

if( ! function_exists( 'helium_font_settings' ) ):

function helium_font_settings() {
	return array(
		'body_font' => array(
			'default' => 'Roboto:300', 
			'include_all_styles' => true, 
			'additional_weights' => array( 700 )
		), 
		'headings_1234_font' => array(
			'default' => 'Roboto:700', 
			'include_all_styles' => true, 
			'additional_weights' => array( 400 )
		), 
		'headings_56_font' => array(
			'default' => 'Roboto:500', 
			'include_all_styles' => true, 
			'additional_weights' => array( 400 )
		), 
		'menu_font' => array(
			'default' => 'Roboto:700'
		), 
		'blockquote_font' => array(
			'default' => 'Vollkorn:italic'
		), 
		'gridlist_filter_font' => array(
			'default' => 'Roboto:300', 
			'inherits' => 'body_font'
		), 
		'gridlist_title_font' => array(
			'default' => 'Roboto:700', 
			'inherits' => 'headings_1234_font'
		), 
		'gridlist_subtitle_font' => array(
			'default' => 'Roboto:300', 
			'inherits' => 'body_font'
		), 
		'content_title_font' => array(
			'default' => 'Roboto:700', 
			'inherits' => 'headings_1234_font'
		), 
		'content_nav_font' => array(
			'default' => 'Roboto:700', 
			'include_all_styles' => true
		), 
		'widget_title_font' => array(
			'default' => 'Roboto:700', 
			'inherits' => 'headings_1234_font'
		)
	);
}
endif;
add_filter( 'youxi_font_settings', 'helium_font_settings' );

function helium_font_options() {
	return Youxi()->option->get_all();
}
add_filter( 'youxi_font_options', 'helium_font_options' );

if( ! function_exists( 'helium_typekit_kit_id' ) ):

function helium_typekit_kit_id( $kit_id ) {
	return Youxi()->option->get( 'typekit_kit_id' );
}
endif;;
add_filter( 'youxi_typekit_kit_id', 'helium_typekit_kit_id' );

if( ! function_exists( 'helium_typekit_enable_cache' ) ):

function helium_typekit_enable_cache( $enabled ) {
	return (bool) Youxi()->option->get( 'typekit_cache' );
}
endif;
add_filter( 'youxi_typekit_enable_cache', 'helium_typekit_enable_cache' );
