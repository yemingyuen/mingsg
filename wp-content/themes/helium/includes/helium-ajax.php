<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

/* ==========================================================================
	AJAX Navigation
============================================================================= */

if( ! function_exists( 'helium_ajax_js_vars' ) ):

function helium_ajax_js_vars( $vars ) {

	$vars = is_array( $vars ) ? $vars : array();
	if( Youxi()->option->get( 'ajax_navigation' ) ) {

		$excludeUrls = explode( PHP_EOL, Youxi()->option->get( 'ajax_exclude_urls' ) );
		$excludeUrls = array_filter( $excludeUrls );
		$excludeUrls = array_merge( array( includes_url(), content_url(), wp_login_url(), plugins_url(), admin_url() ), $excludeUrls );

		$vars['ajax'] = array(
			'enabled'      => true, 
			'scrollTop'    => Youxi()->option->get( 'ajax_navigation_scroll_top' ), 
			'loadingText'  => Youxi()->option->get( 'ajax_navigation_loading_text' ), 
			'excludeUrls'  => $excludeUrls
		);
	}

	return $vars;
}
endif;
add_filter( 'helium_js_vars', 'helium_ajax_js_vars' );
