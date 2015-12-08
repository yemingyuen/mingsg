<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

/* ==========================================================================
	Load Third Party Classes
============================================================================= */

if( ! class_exists( 'TGM_Plugin_Activation' ) ) {
	require_once( get_template_directory() . '/lib/vendor/class-tgm-plugin-activation.php' );
}

/* ==========================================================================
	Setup Global Vars
============================================================================= */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if( ! isset( $content_width ) ) {
	$content_width = 1140;
}

/* ==========================================================================
	Option Tree Setup
============================================================================= */

/**
 * Optional: set 'ot_show_pages' filter to false.
 * This will hide the settings & documentation pages.
 */
add_filter( 'ot_show_pages', defined( 'WP_DEBUG' ) && WP_DEBUG ? '__return_true' : '__return_false' );

/**
 * Optional: set 'ot_show_new_layout' filter to false.
 * This will hide the "New Layout" section on the Theme Options page.
 */
add_filter( 'ot_show_new_layout', '__return_false' );

/**
 * Optional: set 'ot_theme_options_parent_slug' filter to null.
 * This will move the Theme Options menu to the top level menu
 */
add_filter( 'ot_theme_options_parent_slug', '__return_null' );

/**
 * This will determine the Theme Options menu position
 */
add_filter( 'ot_theme_options_position', create_function( '', 'return 50;' ) );

/**
 * Optional: set 'ot_meta_boxes' filter to false.
 * This will disable the inclusion of OT_Meta_Box
 */
add_filter( 'ot_meta_boxes', '__return_false' );

/**
 * Required: set 'ot_theme_mode' filter to true.
 */
add_filter( 'ot_theme_mode', '__return_true' );

/**
 * Required: include OptionTree.
 */
require_once( get_template_directory() . '/option-tree/ot-loader.php' );

/**
 * Include OptionTree Theme Options.
 */
require_once( get_template_directory() . '/theme-options.php' );

/* ==========================================================================
	TGMPA Setup
============================================================================= */

add_action( 'tgmpa_register', 'helium_tgmpa_register' );

/**
 * Register the required plugins for this theme.
 *
 */
function helium_tgmpa_register() {

	/**
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(
		array(
			'name'     => 'Youxi Builder', 
			'slug'     => 'youxi-builder', 
			'source'   => get_template_directory() . '/plugins/youxi-builder.zip', 
			'required' => false, 
			'version'  => '2.4.1'
		), 
		array(
			'name'     => 'Youxi Core', 
			'slug'     => 'youxi-core', 
			'source'   => get_template_directory() . '/plugins/youxi-core.zip', 
			'required' => true, 
			'version'  => '1.4.3'
		), 
		array(
			'name'     => 'Youxi Portfolio', 
			'slug'     => 'youxi-portfolio', 
			'source'   => get_template_directory() . '/plugins/youxi-portfolio.zip', 
			'required' => true, 
			'version'  => '1.3'
		), 
		array(
			'name'     => 'Youxi Post Format', 
			'slug'     => 'youxi-post-format', 
			'source'   => get_template_directory() . '/plugins/youxi-post-format.zip', 
			'required' => false, 
			'version'  => '1.1.2'
		), 
		array(
			'name'     => 'Youxi Shortcode', 
			'slug'     => 'youxi-shortcode', 
			'source'   => get_template_directory() . '/plugins/youxi-shortcode.zip', 
			'required' => false, 
			'version'  => '3.2'
		), 
		array(
			'name'     => 'Youxi Widgets', 
			'slug'     => 'youxi-widgets', 
			'source'   => get_template_directory() . '/plugins/youxi-widgets.zip', 
			'required' => false, 
			'version'  => '1.5.2'
		), 
		array(
			'name'     => 'Contact Form 7',
			'slug'     => 'contact-form-7',
			'required' => false
		), 
		array(
			'name'     => 'Easy Digital Downloads',
			'slug'     => 'easy-digital-downloads',
			'required' => false
		)
	);

	/**
	 * Array of configuration settings. Amend each line as needed.
	 * If you want the default strings to be available under your own theme domain,
	 * leave the strings uncommented.
	 * Some of the strings are added into a sprintf, so see the comments at the
	 * end of each line for what each argument will be.
	 */
	$config = array(
		'is_automatic' => true
	);

	tgmpa( $plugins, $config );
}

/* ==========================================================================
	Include Framework Classes
============================================================================= */

require_once( get_template_directory() . '/lib/framework/core/class-core.php' );

require_once( get_template_directory() . '/lib/framework/font/class-font.php' );

require_once( get_template_directory() . '/lib/importer/class-importer.php' );

/* ==========================================================================
	Include Plugin Configurations
============================================================================= */

require_once( get_template_directory() . '/plugins-config/config-contact-form-7.php' );

require_once( get_template_directory() . '/plugins-config/config-youxi-core.php' );

require_once( get_template_directory() . '/plugins-config/config-easy-digital-downloads.php' );

require_once( get_template_directory() . '/plugins-config/config-youxi-page-builder.php' );

require_once( get_template_directory() . '/plugins-config/config-youxi-portfolio.php' );

require_once( get_template_directory() . '/plugins-config/config-youxi-post-format.php' );

require_once( get_template_directory() . '/plugins-config/config-youxi-shortcodes.php' );

require_once( get_template_directory() . '/plugins-config/config-youxi-widgets.php' );

/* ==========================================================================
	Include Theme Functions
============================================================================= */

require_once( get_template_directory() . '/includes/helium-addthis.php' );

require_once( get_template_directory() . '/includes/helium-ajax.php' );

require_once( get_template_directory() . '/includes/helium-comments.php' );

require_once( get_template_directory() . '/includes/helium-customizer.php' );

require_once( get_template_directory() . '/includes/helium-demo.php' );

require_once( get_template_directory() . '/includes/helium-edd.php' );

require_once( get_template_directory() . '/includes/helium-entries.php' );

require_once( get_template_directory() . '/includes/helium-filters.php' );

require_once( get_template_directory() . '/includes/helium-fonts.php' );

require_once( get_template_directory() . '/includes/helium-icons.php' );

require_once( get_template_directory() . '/includes/helium-layout.php' );

require_once( get_template_directory() . '/includes/helium-media.php' );

require_once( get_template_directory() . '/includes/helium-nav-menu.php' );

require_once( get_template_directory() . '/includes/helium-portfolio.php' );

require_once( get_template_directory() . '/includes/helium-post.php' );

require_once( get_template_directory() . '/includes/helium-theme-options.php' );

require_once( get_template_directory() . '/includes/helium-wp.php' );

/* EOF */
