<?php

/* WPML compatibility */
if( function_exists( 'icl_register_string' ) && is_admin() ) {
	require( YOUXI_WIDGETS_DIR . 'admin/wpml.php' );
}
