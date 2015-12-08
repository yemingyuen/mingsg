<?php
/*
Plugin Name: Youxi Page Builder
Plugin URI: http://www.themeforest.net/user/nagaemas
Description: This plugin adds the ability to build pages using a user friendly drag and drop interface that's tightly integrated into the WordPress editor. This plugin is completely independent, and only requires Youxi Shortcodes plugin to work. All content generated with the help of this plugin can still work as long the shortcode plugin is active.
Version: 2.4.1
Author: YouxiThemes
Author URI: http://www.themeforest.net/user/nagaemas
License: Envato Marketplace Licence

Changelog:
2.4.1 - 26/08/2015
- Fixed a bug causing WordPress below version 4.3 to crash

2.4 - 20/08/2015
- Rewrite WordPress editor integration code for WordPress 4.3
- Requires at least WordPress 4.3

2.3 - 09/03/2015
- Fix Integration on WordPress 4.1+
- Update: Translation files
- Update: FontAwesome v4.3
- Improvement: Remove explicit builder icon definitions

2.2- 14/09/2014
- Fix TinyMCE integration bug caused by WordPress 4.0 changes
- Reduce dependency on jQuery-UI to prevent styling conflicts if jQuery-UI CSS is enqueud
- Updated for Youxi Shortcode 3.1 new features compatibility
- Minor bug fixes and improvements

2.1.1 - 31/08/2014
- FontAwesome is now loaded from MaxCDN

2.1 - 04/06/2014
- Improvement: Containers can now either be a fullwidth container, or a grid container.
- Improvement: Cleaner rules and shortcode configuration parameters
- Update: Configuration parameters for Youxi Shortcodes 2.1
- Update: Validation rules to be based on shortcode tags instead of levels
- Update: Backbone.Courier v0.6.1

2.0 - 14/04/2014
- Addition: Filters and settings for UI icons
- Addition: Option to set [container] as highest level element instead of [row]
- Addition: Element cloning functionality
- Addition: Removal animation of elements
- Update: Backbone 1.1 compatibility
- Update: WP 3.8 UI integration
- Update: Use dashicons by default
- Update: FontAwesome 4.1
- Update: Compatibility with the new Youxi Shortcode 2.0 columns
- Improvement: Only one [row] shortcode is now allowed to keep things simple
- Improvement: Elements are now dragged using the header instead a handle
- Improvement: Element controls are now dynamically added based on conditions
- Improvement: Code structure now allows editing of all element with attribute/content
- Improvement: Better drop position calculation resulting in easier element positioning when dropping a new element
- Improvement: JS rewrite and restructure resulting in smaller code size
- Improvement: CSS rewrite

1.0.1 - 17/10/2013
- For some odd reasons something happened when compressing the JavaScripts, so the minified version wasn't working.

1.0
- Initial release
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

/* No need to initialize builder if not on admin page */
if( ! is_admin() ) {
	return;
}

function youxi_builder_plugins_loaded() {

	global $wp_version;
	
	if( ! defined( 'YOUXI_SHORTCODE_VERSION' ) ) {

		if( ! class_exists( 'Youxi_Admin_Notice' ) ) {
			require( plugin_dir_path( __FILE__ ) . 'class-admin-notice.php' );
		}
		Youxi_Admin_Notice::instance()->add_error( __FILE__, __( 'This plugin requires you to install and activate the Youxi Shortcodes plugin.', 'youxi' ) );
		return;

	} else {

		if( version_compare( YOUXI_SHORTCODE_VERSION, '3.1', '<' ) ) {
			if( ! class_exists( 'Youxi_Admin_Notice' ) ) {
				require( plugin_dir_path( __FILE__ ) . 'class-admin-notice.php' );
			}
			Youxi_Admin_Notice::instance()->add_error( __FILE__, __( 'The current version of this plugin requires at least Youxi Shortcode 3.1 to work.', 'youxi' ) );
			return;
		}
	}

	if( version_compare( $wp_version, '4.3', '<' ) ) {
		if( ! class_exists( 'Youxi_Admin_Notice' ) ) {
			require( plugin_dir_path( __FILE__ ) . 'class-admin-notice.php' );
		}
		Youxi_Admin_Notice::instance()->add_error( __FILE__, __( 'The current version of this plugin requires at least WordPress 4.3.', 'youxi' ) );
		return;
	}

	define( 'YOUXI_BUILDER_VERSION', '2.4.1' );

	define( 'YOUXI_BUILDER_DIR', plugin_dir_path( __FILE__ ) );
	define( 'YOUXI_BUILDER_URL', plugin_dir_url( __FILE__ ) );
	define( 'YOUXI_BUILDER_LANG_DIR', dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	/* Load Language File */
	load_plugin_textdomain( 'youxi', false, YOUXI_BUILDER_LANG_DIR );

	/* Instantiate the manager */
	if( ! class_exists( 'Youxi_Builder_Manager' ) ) {
		require( YOUXI_BUILDER_DIR . 'classes/class-manager.php' );
	}
	Youxi_Builder_Manager::instance();
}
add_action( 'plugins_loaded', 'youxi_builder_plugins_loaded', 11 );
