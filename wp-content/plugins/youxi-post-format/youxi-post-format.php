<?php
/*
Plugin Name: Youxi Post Format
Plugin URI: http://www.themeforest.net/user/nagaemas
Description: This plugin extends WordPress blog posts with very customizable post format related content.
Version: 1.1.2
Author: YouxiThemes
Author URI: http://www.themeforest.net/user/nagaemas
License: Envato Marketplace Licence

Changelog:
1.1.2 - 12/03/2015
- Updated translation files

1.1.1 - 09/03/2015
- Use the gallery form field for gallery post type

1.1 - 07/11/2014
- Rename post format meta keys
- Added wpml-config.xml

*/

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

function youxi_post_format_plugins_loaded() {

	if( ! defined( 'YOUXI_CORE_VERSION' ) ) {

		if( ! class_exists( 'Youxi_Admin_Notice' ) ) {
			require( plugin_dir_path( __FILE__ ) . 'class-admin-notice.php' );
		}
		Youxi_Admin_Notice::instance()->add_error( __FILE__, __( 'This plugin requires you to install and activate the Youxi Core plugin.', 'youxi' ) );

		return;
	}

	define( 'YOUXI_POST_FORMAT_VERSION', '1.1.2' );

	define( 'YOUXI_POST_FORMAT_DIR', plugin_dir_path( __FILE__ ) );

	define( 'YOUXI_POST_FORMAT_URL', plugin_dir_url( __FILE__ ) );

	define( 'YOUXI_POST_FORMAT_LANG_DIR', plugin_dir_path( __FILE__ ) . 'languages' . DIRECTORY_SEPARATOR );

	/* Load Language File */
	load_plugin_textdomain( 'youxi', false, YOUXI_POST_FORMAT_LANG_DIR );

	if( ! class_exists( 'Youxi_Post_Formats_Manager' ) ) {
		require_once( YOUXI_POST_FORMAT_DIR . 'class-manager.php' );
	}
	new Youxi_Post_Formats_Manager();
}
add_action( 'plugins_loaded', 'youxi_post_format_plugins_loaded' );

/**
 * Return list of supported post types
 */
function youxi_post_format_post_types() {
	return apply_filters( 'youxi_post_format_post_types', array( 'post' ) );
}

/**
 * Return a particular post format ID
 */
function youxi_post_format_id( $post_format ) {

	switch( $post_format ) {
		case 'aside':
			return apply_filters( 'youxi_post_format_aside_id', 'youxi_post_format_meta_aside' );
		case 'image':
			return apply_filters( 'youxi_post_format_image_id', 'youxi_post_format_meta_image' );
		case 'video':
			return apply_filters( 'youxi_post_format_video_id', 'youxi_post_format_meta_video' );
		case 'audio':
			return apply_filters( 'youxi_post_format_audio_id', 'youxi_post_format_meta_audio' );
		case 'quote':
			return apply_filters( 'youxi_post_format_quote_id', 'youxi_post_format_meta_quote' );
		case 'link':
			return apply_filters( 'youxi_post_format_link_id', 'youxi_post_format_meta_link' );
		case 'gallery':
			return apply_filters( 'youxi_post_format_gallery_id', 'youxi_post_format_meta_gallery' );
	}

	return apply_filters( 'youxi_post_format_standard_id', 'youxi_post_format_meta_standard' );
}

/**
 * Link Post Format Metabox
 */
function youxi_post_format_link_metabox() {

	return apply_filters( 'youxi_post_format_link_metabox', array(
		'title' => __( 'Post Format: Link', 'youxi' ), 
		'fields' => array(
			'link_url' => array(
				'type' => 'url', 
				'label' => __( 'Link URL', 'youxi' ), 
				'description' => __( 'Enter here the link URL for this post.', 'youxi' )
			)
		)
	));
}

/**
 * Video Post Format Metabox
 */
function youxi_post_format_video_metabox() {

	return apply_filters( 'youxi_post_format_video_metabox', array(
		'title' => __( 'Post Format: Video', 'youxi' ), 
		'fields' => array(
			'type' => array(
				'type' => 'select', 
				'label' => __( 'Type', 'youxi' ), 
				'description' => __( 'Choose here the video type for this post.', 'youxi' ), 
				'choices' => array(
					'embed' => __( 'Embedded', 'youxi' ), 
					'hosted' => __( 'Hosted', 'youxi' )
				), 
				'std' => 'hosted'
			), 
			'embed' => array(
				'type' => 'textarea', 
				'label' => __( 'Embed Code', 'youxi' ), 
				'description' => __( 'Enter here the video embed code for this post.', 'youxi' ), 
				'std' => '', 
				'criteria' => 'type:is(embed)'
			), 
			'src' => array(
				'type' => 'upload', 
				'label' => __( 'Source', 'youxi' ), 
				'library_type' => 'video', 
				'description' => __( 'Choose here the hosted video source for this post.', 'youxi' ), 
				'criteria' => 'type:is(hosted)'
			), 
			'poster' => array(
				'type' => 'image', 
				'multiple' => false, 
				'label' => __( 'Poster', 'youxi' ), 
				'description' => __( 'Upload here an image that will be used either as the poster or fallback for unsupported devices.', 'youxi' ), 
				'criteria' => 'type:is(hosted)'
			)
		)
	));
}

/**
 * Audio Post Format Metabox
 */
function youxi_post_format_audio_metabox() {

	return apply_filters( 'youxi_post_format_audio_metabox', array(
		'title' => __( 'Post Format: Audio', 'youxi' ), 
		'fields' => array(
			'type' => array(
				'type' => 'select', 
				'label' => __( 'Type', 'youxi' ), 
				'description' => __( 'Choose here the audio type for this post.', 'youxi' ), 
				'choices' => array(
					'embed' => __( 'Embedded', 'youxi' ), 
					'hosted' => __( 'Hosted', 'youxi' )
				), 
				'std' => 'hosted'
			), 
			'embed' => array(
				'type' => 'textarea', 
				'label' => __( 'Embed Code', 'youxi' ), 
				'description' => __( 'Enter here the audio embed code for this post.', 'youxi' ), 
				'std' => '', 
				'criteria' => 'type:is(embed)'
			), 
			'src' => array(
				'type' => 'upload', 
				'label' => __( 'Source', 'youxi' ), 
				'library_type' => 'audio', 
				'description' => __( 'Choose here the hosted audio source for this post.', 'youxi' ), 
				'criteria' => 'type:is(hosted)'
			)
		)
	));
}

/**
 * Quote Post Format Metabox
 */
function youxi_post_format_quote_metabox() {

	return apply_filters( 'youxi_post_format_quote_metabox', array(
		'title' => __( 'Post Format: Quote', 'youxi' ), 
		'fields' => array(
			'text' => array(
				'type' => 'textarea', 
				'label' => __( 'Text', 'youxi' ), 
				'description' => __( 'Enter here the quote text.', 'youxi' )
			), 
			'author' => array(
				'type' => 'text', 
				'label' => __( 'Author', 'youxi' ), 
				'description' => __( 'Enter here the quote author for this post.', 'youxi' )
			), 
			'source' => array(
				'type' => 'text', 
				'label' => __( 'Source', 'youxi' ), 
				'description' => __( 'Enter here the quote source for this post.', 'youxi' )
			), 
			'source_url' => array(
				'type' => 'url', 
				'label' => __( 'Source URL', 'youxi' ), 
				'description' => __( 'Enter here the quote source URL for this post.', 'youxi' ), 
				'std' => '#'
			)
		)
	));
}

/**
 * Gallery Post Format Metabox
 */
function youxi_post_format_gallery_metabox() {

	return apply_filters( 'youxi_post_format_gallery_metabox', array(
		'title' => __( 'Post Format: Gallery', 'youxi' ), 
		'fields' => array(
			'images' => array(
				'type' => version_compare( YOUXI_CORE_VERSION, '1.4', '<' ) ? 'image' : 'gallery', 
				'multiple' => true, 
				'label' => __( 'Images', 'youxi' ), 
				'description' => __( 'Upload here the gallery images to use for this post.', 'youxi' )
			)
		)
	));
}
