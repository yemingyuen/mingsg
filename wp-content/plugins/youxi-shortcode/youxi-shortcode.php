<?php
/*
Plugin Name: Youxi Shortcode
Plugin URI: http://www.themeforest.net/user/nagaemas
Description: This plugin functions as a shortcode manager that by default registers a set of shortcodes, while not restricting the possibilities to extend and alter the shortcodes as you need through another plugin/theme. This plugin also provides a TinyMCE shortcode plugin to use on the WordPress editor.
Version: 3.2
Author: YouxiThemes
Author URI: http://www.themeforest.net/user/nagaemas
License: Envato Marketplace Licence

Changelog:
3.2 - 12/03/2015
- Added `icon` attribute to shortcodes
- Added utility function to dump registered shortcodes
- Improvement: Optimize serializer/deserializer scripts
- Improvement: Don't minify serializers/deserializers when SCRIPT_DEBUG is enabled
- Update: Specify the `icon` attribute on shortcode definitions
- Update: Translation files

3.1 - 07/11/2014
- Added `shortcode_exists` method to the shortcode manager
- Added several static methods to Youxi_Shortcode class to get shortcode definitions
- Added shortcode prefixing functionality
- Prefixed shortcode editor modal class names to fix conflicts with WPEngine styles
- Fixed a compatibility bug with Contact Form 7 3.9 and up

3.0.1 - 09/08/2014
- Recognized widget areas filter name change
- Compress all shortcode editor inline JS
- Rename shortcode attribute encoders/decoders to serializers/deserializers
- Added Youxi_Shortcode::uniqid() method to get a shortcode unique identifier
- Added 'behavior' attribute to [accordion]

3.0 - 05/07/2014
- Addition: [icon_box] shortcode in content
- Addition: [fullwidth] shortcode in layout
- Addition: [video] shortcode integration in media
- Addition: [audio] shortcode integration in media
- Addition: [twitter] shortcode in media
- Addition: [counter] shortcode in statistic
- Addition: [testimonials] shortcode in content
- Addition: [posts] shortcode in content
- Addition: Media category
- Addition: Statistic category
- Addition: Youxi_Shortcode::get_defaults() method
- Addition: Youxi_Shortcode::get_default_columns() method
- Addition: Youxi_Shortcode_Manager::get_shortcode_fields() method
- Addition: Conditional loading of shortcode assets based on the current post
- Addition: Shortcode Animation class for shortcode animation functionality
- Improvement: Shortcode parsers are now dynamically registered via shortcode configuration parameters
- Improvement: Moved slider to media category
- Improvement: Moved Google maps to media category
- Improvement: Renamed [slider_content] shortcode to [slide]
- Improvement: Merged content and widget categories
- Improvement: Removed 'adv_mode' attribute from column shortcode
- Improvement: Implementation of advanced column shortcode
- Improvement: Tidy up shortcodes definitions
- Improvement: Youxi_Shortcode_Manager::remove_shortcode now returns the removed shortcode
- Update: Removed [skill] shortcode as [progressbar] shortcode is enough
- Update: Added 'label' attribute to [progressbar] shortcode
- Update: Rename 'youxi_shortcodes_columns_backcompat' filter to 'youxi_shortcode_simple_columns'
- Update: Rename all action and filter hook names from 'youxi_shortcodes_*' to 'youxi_shortcode_'
- Update: Bootstrap 3.2

2.0 - 14/04/2014
- Updated to Bootstrap 3.1.1
- Updated to serializeJSON 1.2.3
- Updated several shortcodes to use ios7 switch
- Updated to be compatible with TinyMCE 4
- Added [container] shortcode
- Added [slider] shortcode
- Columns is now a single shortcode with Bootstrap 3 features (push, pull, xs, sm, md, lg). 
	Note: Use 'youxi_shortcodes_columns_backcompat' filter to use the legacy columns.

1.0.3 - 06/03/2014
- WP 3.8 UI Fixes
- Added option to show/hide price and button on pricing table

1.0.2 - 06/11/2013
- Shortcodes registrations occurs now during 'init' hook instead of 'after_setup_theme'

1.0.1 - 19/10/2013
- Removed 'wp_kses_post' from text_widget shortcode as it should've been called by WordPress
- Added .accordion class to accordion shortcode
- Added monochrome attribute to google_map

1.0
- Initial release
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

function youxi_shortcode_plugins_loaded() {

	if( ! defined( 'YOUXI_CORE_VERSION' ) ) {

		if( ! class_exists( 'Youxi_Admin_Notice' ) ) {
			require( plugin_dir_path( __FILE__ ) . 'class-admin-notice.php' );
		}
		Youxi_Admin_Notice::instance()->add_error( __FILE__, __( 'This plugin requires you to install and activate the Youxi Core plugin.', 'youxi' ) );

		return;
	}

	define( 'YOUXI_SHORTCODE_VERSION', '3.2' );

	define( 'YOUXI_SHORTCODE_DIR', plugin_dir_path( __FILE__ ) );

	define( 'YOUXI_SHORTCODE_URL', plugin_dir_url( __FILE__ ) );

	define( 'YOUXI_SHORTCODE_LANG_DIR', dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	/* Load Language File */
	load_plugin_textdomain( 'youxi', false, YOUXI_SHORTCODE_LANG_DIR );

	/* Include the default shortcodes */
	require_once( YOUXI_SHORTCODE_DIR . 'definitions/layout.php' );
	require_once( YOUXI_SHORTCODE_DIR . 'definitions/content.php' );
	require_once( YOUXI_SHORTCODE_DIR . 'definitions/statistic.php' );
	require_once( YOUXI_SHORTCODE_DIR . 'definitions/media.php' );
	require_once( YOUXI_SHORTCODE_DIR . 'definitions/uncategorized.php' );

	/* Instantiate the manager */
	if( ! class_exists( 'Youxi_Shortcode_Manager' ) ) {
		require( YOUXI_SHORTCODE_DIR . 'classes/class-manager.php' );
	}
	Youxi_Shortcode_Manager::get();
}
add_action( 'plugins_loaded', 'youxi_shortcode_plugins_loaded' );
