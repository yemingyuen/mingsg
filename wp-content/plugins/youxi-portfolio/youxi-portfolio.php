<?php
/*
Plugin Name: Youxi Portfolio
Plugin URI: http://www.themeforest.net/user/nagaemas
Description: This plugin registers a portfolio custom post type and optionally registers a shortcode to ease displaying the portfolio. Through the filters and actions provided, you can freely add, modify or remove any post type argument/field.
Version: 1.3
Author: YouxiThemes
Author URI: http://www.themeforest.net/user/nagaemas
License: Envato Marketplace Licence

Changelog:
1.3 - 12/03/2015
- Move `gallery` post type into separate plugin
- Rename [portfolio] shortcode to [portfolio_entries]
- Remove the `exclude` attr from portfolio shortcode
- Updated translation files

1.2 - 18/12/2014
- Introduce `gallery` post type
- Move all portfolio functions to `Youxi_Portfolio` class

1.1.2 - 07/11/2014
- Add filter to disable portfolio shortcode
- Add wpml-config.xml
- Rename `Portfolios` to `Portfolio`
- Changed the `show_in_nav_menus` attribute to true
- Fix bug that prevents disabling shortcode registration

1.1.1. - 18/06/2014
- Removed shortcode parser scripts

1.1 - 01/06/2014
- Added categories filter on portfolio shortcode

1.0.2 - 04/04/2013
- Minor code optimizations
- Replaced post type icon to WP 3.8 dashicon

1.0.1 - 19/10/2013
- Fixed a function argument that causes an error on PHP < 5.3

1.0
- Initial release
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

function youxi_portfolio_plugins_loaded() {

	if( ! defined( 'YOUXI_CORE_VERSION' ) ) {

		if( ! class_exists( 'Youxi_Admin_Notice' ) ) {
			require( plugin_dir_path( __FILE__ ) . 'class-admin-notice.php' );
		}
		Youxi_Admin_Notice::instance()->add_error( __FILE__, __( 'This plugin requires you to install and activate the Youxi Core plugin.', 'youxi' ) );

		return;
	}

	define( 'YOUXI_PORTFOLIO_VERSION', '1.3' );

	define( 'YOUXI_PORTFOLIO_DIR', plugin_dir_path( __FILE__ ) );

	define( 'YOUXI_PORTFOLIO_URL', plugin_dir_url( __FILE__ ) );

	define( 'YOUXI_PORTFOLIO_LANG_DIR', dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	/* Load Language File */
	load_plugin_textdomain( 'youxi', false, YOUXI_PORTFOLIO_LANG_DIR );

	require_once( YOUXI_PORTFOLIO_DIR . 'class-portfolio.php' );
	
	require_once( YOUXI_PORTFOLIO_DIR . 'portfolio-shortcode.php' );
}
add_action( 'plugins_loaded', 'youxi_portfolio_plugins_loaded' );
