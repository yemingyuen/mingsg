<?php
/*
Plugin Name: Youxi Core
Plugin URI: http://www.themeforest.net/user/nagaemas
Description: Youxi Themes core plugin that is required by most Youxi Themes plugins to work. The plugin basically provides classes for form creation, post types, metaboxes and custom fields registration.
Version: 1.4.3
Author: YouxiThemes
Author URI: http://www.themeforest.net/user/nagaemas
License: Envato Marketplace Licence

Changelog:
1.4.3 - 11/18/2015
- When using WPML, the post order page displays the posts in the default language

1.4.2 - 02/04/2015
- Fix: Bug with image form field returning empty array when multiple is false
- Improvement: Pass post meta key name through a filter for Youxi Likes class
- Improvement: `$liked_attr` argument on `Youxi_Likes::get_link_attributes()`

1.4.1 - 20/03/2015
- Fix: Bug with page templates metabox toggling if not located in theme root

1.4 - 24/01/2015
- Addition: Gallery form field
- Addition: Toggle metabox based on selected page template
- Addition: Youxi_Likes class to add `Like This` functionality to posts
- Addition: Ability to deactivate the embed state on upload form field
- Improvement: Form field CSS and JS tweaks
- Fix: Bug on image uploader field caused by criteria checks
- Fix: Bug with TinyMCE on RTL languages
- Update: Translation files
- Update: FontAwesome v4.3
- Update: Switchery v0.7.0
- Update: Select2 v3.5.2
- Update: CodeMirror v5.0.0
- Update: jQuery.mousewheel v3.1.12

1.3.3 - 07/01/2015
- Fix: Bug on image uploader field that prevents saving when no image(s) are selected

1.3.2 - 07/11/2014
- Update: Switchery v0.6.3
- Improvement: Metabox fields can now be saved as a scalar value
- Fix: jQuery-ui widget styling conflicts when jQuery-ui style is enqueued by other plugins

1.3.1 - 31/08/2014
- Minor WordPress 4.0 compatibility fix
- FontAwesome is now loaded from MaxCDN

1.3 - 15/08/2014
- Addition: Code editor form field
- Addition: JavaScript helpers class
- Fix: Bug preventing icon chooser form field from saving
- Improvement: Refactor `Youxi_Post_Type` class
- Improvement: Upload form field now checks if the selected file is an attachment or an external file
- Improvement: Allow a post type to only have one wrapper object
- Update: Modified `Youxi_Post_Type` `save_post` hook priority to 9 for WPML compatibility
- Update: Select2 v3.5.1

1.2 - 12/07/2014
- Addition: Form fields can now be displayed in tabs using 'fieldset' option
- Addition: Post pages abstract class
- Addition: Post order page to re-order posts by menu_order
- Fix: Bug with conditional checks
- Fix: Bug causing uislider not to accept decimal values
- Improvement: All CSS rewritten using LESS
- Improvement: UI Slider form field restyled
- Improvement: Merged image upload and file upload scripts into on file
- Improvement: Functionality to specify library type for file upload field
- Improvement: Functionality to insert from URL on image and file upload fields
- Improvement: Form field 'choices' and 'fields' can be supplied a callback with arguments
- Update: Select2 v3.5.0
- Update: Switchery v0.6.1

1.1.1 - 25/05/2014
- Update: Switchery v0.6
- Removed: URL validation from URL fields
- Improvement: Work around for disabled Switchery

1.1 - 30/03/2014
- Addition: Allows grouping of icons on the iconchooser form field
- Addition: Multiselect form field
- Addition: Upload form field
- Addition: Switch form field
- Addition: New conditions for toggling fields (credits to OptionTree)
- Update: WP 3.8 UI integration
- Update: Richtext form field init code for TinyMCE 4
- Update: FontAwesome 4.1
- Update: Select2 3.4.8
- Improvement: Post metabox fields can now be saved in a single array or as single fields
- Improvement: Image fields now saves the data in a single array or single data based on the 'multiple' option.
- Improvement: Image Uploader scripts
- Improvement: Handling of attachments without valid image size
- Improvement: Remove CPT icons, use dashicons for CPT
- Improvement: Metabox default context is now 'normal' instead of 'advanced'

1.0.3 - 06/11/2013
- Allow callbacks to be specified as checkbox/radio button field choices

1.0.2 - 24/10/2013
- Changed empty() to is_null() for checking empty form fields

1.0.1 - 19/10/2013
- Fixed a CSS issue that causes the select2 dropdown to fall behind the popup
- Fixed an issue causing '0' to be set to the default value on form fields

1.0
- Initial release
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

define( 'YOUXI_CORE_VERSION', '1.4.3' );

define( 'YOUXI_CORE_DIR', plugin_dir_path( __FILE__ ) );

define( 'YOUXI_CORE_URL', plugin_dir_url( __FILE__ ) );

define( 'YOUXI_CORE_LANG_DIR', dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

require_once( YOUXI_CORE_DIR . '/class-metabox.php' );
require_once( YOUXI_CORE_DIR . '/class-post-type.php' );
require_once( YOUXI_CORE_DIR . '/class-taxonomy.php' );
require_once( YOUXI_CORE_DIR . '/class-likes.php' );
require_once( YOUXI_CORE_DIR . '/admin/class-js.php' );

if( is_admin() ) {
	require_once( YOUXI_CORE_DIR . '/class-post-page.php' );
	require_once( YOUXI_CORE_DIR . '/class-post-order-page.php' );
	require_once( YOUXI_CORE_DIR . '/admin/class-form.php' );
	require_once( YOUXI_CORE_DIR . '/admin/class-form-field.php' );
}

function youxi_core_plugins_loaded() {

	/* Load Language File */
	load_plugin_textdomain( 'youxi', false, YOUXI_CORE_LANG_DIR );
}
add_action( 'plugins_loaded', 'youxi_core_plugins_loaded' );
