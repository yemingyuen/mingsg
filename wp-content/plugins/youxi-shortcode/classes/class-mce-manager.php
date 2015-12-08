<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

class Youxi_Shortcode_TinyMCE_Manager {

	/**
	 * Constructor.
	 */
	public function __construct() {

		/* Bail early if the user settings doesn't editing posts / pages */
		if( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) )
			return;

		/* Also bail if rich_editing is not allowed */
		if( get_user_option( 'rich_editing' ) != 'true' )
			return;

		/* Call the prepare function on add_meta_boxes hook */
		add_action( 'add_meta_boxes', array( $this, 'prepare' ) );
	}
	
	/**
	 * Check if displaying the tinymce button on a post type is allowed
	 */
	public function allow_post_type( $post_type ) {
		$allowed = apply_filters( 'youxi_shortcode_tinymce_post_types', array( 'page' ) );
		foreach( $allowed as $key => $allow ) {
			$check = ! is_numeric( $key ) ? $key : $allow;
			if( $post_type == $check ) {
				return true;
			}
		}

		return false;
	}
	
	/**
	 * Pre-initialize the TinyMCE plugin if the current post type is whitelisted.
	 */
	public function prepare( $post_type ) {

		if( $this->allow_post_type( $post_type ) ) {

			/* Add the tinymce shortcode plugin for the default WP_Editor */
			add_filter( 'mce_external_plugins', array( $this, 'mce_external_plugins' ) );

			/* Add the tinymce shortcode button for the default WP_Editor */
			add_filter( 'mce_buttons', array( $this, 'mce_buttons' ) );

			/* Filter the Youxi_Shortcode_Manager JS Vars */
			add_filter( 'youxi_shortcode_js_vars', array( $this, 'shortcode_js_vars') );

			/* Tell the shortcodes plugin to enqueue the admin assets */
			add_filter( 'youxi_shortcode_admin_enqueue_scripts', '__return_true' );

			/* Add hook to enqueue external assets for use by the TinyMCE plugin */
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		}
	}
	
	/**
	 * Enqueue TinyMCE plugin required assets.
	 * Since this method is called via admin_enqueue_scripts, we're 100% sure that the scripts 
	 * are added before the wp_editor initialization script that uses admin_print_footer_scripts.
	 *
	 * @param string The current admin page name
	 *
	 */
	public function admin_enqueue_scripts( $hook ) {
		/* 
			If we're not on the post edit screen or the current post type does not support editor, 
			we can assume that TinyMCE is not present on the page.
			This currently does not take account if other plugins/function calls the wp_editor, in that case just do not display the plugin.
		*/
		if( ! in_array( $hook, array( 'post.php', 'post-new.php' ) ) || ! post_type_supports( get_post_type(), 'editor' ) ) {
			return;
		}

		wp_enqueue_script( 'youxi-shortcode' );
		wp_enqueue_style( 'youxi-shortcode' );
	}

	/**
	 * Registers the shortcode TinyMCE plugin
	 *
	 * @param array The current wp_editor registered TinyMCE plugins
	 * @param array The registered TinyMCE plugins
	 */
	public function mce_external_plugins( $plugins ) {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$plugins['youxishortcode'] = YOUXI_SHORTCODE_URL . "admin/assets/tinymce/plugin{$suffix}.js";
		return $plugins;
	}

	/**
	 * Adds the shortcode button to TinyMCE toolbar
	 *
	 * @param array The current wp_editor registered TinyMCE buttons
	 * @param array The registered TinyMCE buttons
	 */
	public function mce_buttons( $buttons ) {
		array_push( $buttons, '|', 'youxi_shortcode_menu' );
		return $buttons;
	}

	/**
	 * Filters for the shortcode manager Javascript vars
	 *
	 * @param array The current variables to be registered
	 * @param array The altered variables
	 */
	public function shortcode_js_vars( $vars ) {
		$vars['tinyMCEL10N'] = array(
			'shortcodeButtonTitle' => __( 'Insert Shortcode', 'youxi' )
		);	
		$vars['defaultContent'] = __( 'Content here', 'youxi' );
		$vars['tinyMceMode'] = 'full';

		if( $post_type = get_post_type() ) {
			$allowed = apply_filters( 'youxi_shortcode_tinymce_post_types', array( 'page' ) );
			$vars['tinyMceMode'] = isset( $allowed[ $post_type ] ) && 'tiny' == $allowed[ $post_type ] ? $allowed[ $post_type ] : 'full';
		}

		return $vars;
	}
}