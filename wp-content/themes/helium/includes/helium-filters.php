<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

/* ==========================================================================
	Entry Pages Link
============================================================================= */

if( ! function_exists( 'helium_link_pages_link' ) ):

function helium_link_pages_link( $link ) {
	return '<li>' . $link . '</li>';
}
endif;
add_filter( 'wp_link_pages_link', 'helium_link_pages_link' );

/* ==========================================================================
	`the_content_more_link`
============================================================================= */

if( ! function_exists( 'helium_the_content_more_link' ) ):

function helium_the_content_more_link( $more_link ) {
	$more_link = preg_replace( '/#more-[0-9]+/', '', $more_link );
	return '<div class="more-link-wrap">' . $more_link . '</div>';
}
endif;
add_filter( 'the_content_more_link', 'helium_the_content_more_link' );

/* ==========================================================================
	Excerpt More
============================================================================= */

if( ! function_exists( 'helium_excerpt_more' ) ):

function helium_excerpt_more( $excerpt_more ) {
	return '&hellip;';
}
endif;
add_filter( 'excerpt_more', 'helium_excerpt_more' );

/* ==========================================================================
	Recognized Sidebars
============================================================================= */

if( ! function_exists( 'helium_recognized_sidebars' ) ) {

	function helium_recognized_sidebars( $sidebars ) {
		$recognized = array();
		foreach( $sidebars as $id => $sidebar ) {
			if( ! preg_match( '/^footer_widget_area_\d+$/', $id ) ) {
				$recognized[ $id ] = $sidebar;
			}
		}
		return $recognized;
	}
}
add_filter( 'ot_recognized_sidebars', 'helium_recognized_sidebars' );
add_filter( 'youxi_shortcode_recognized_sidebars', 'helium_recognized_sidebars' );
