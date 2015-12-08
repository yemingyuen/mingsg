<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

/* ==========================================================================
	Accent Color
============================================================================= */

if( ! function_exists( 'helium_default_accent_color' ) ):

function helium_default_accent_color() {
	return apply_filters( 'helium_default_accent_color', '#3dc9b3' );
}
endif;
