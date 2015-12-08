<?php

if( ! function_exists( 'helium_wpcf7_form_class_attr' ) ) {

	function helium_wpcf7_form_class_attr( $class ) {
		return $class . ' form-horizontal';
	}
}
add_filter( 'wpcf7_form_class_attr', 'helium_wpcf7_form_class_attr' );

if( ! function_exists( 'helium_wpcf7_enqueue_scripts' ) ) {

	function helium_wpcf7_enqueue_scripts() {

		$wp_theme = wp_get_theme();
		$theme_version = $wp_theme->exists() ? $wp_theme->get( 'Version' ) : false;

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'helium-contact-form-7', 
			get_template_directory_uri() . "/assets/js/helium.wpcf7{$suffix}.js", 
			array( 'contact-form-7' ), 
			$theme_version, true 
		);
	}
}
add_action( 'wpcf7_enqueue_scripts', 'helium_wpcf7_enqueue_scripts' );