<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

/* ==========================================================================
	Post Format
============================================================================= */

if( ! function_exists( 'helium_extract_post_format_meta' ) ):

function helium_extract_post_format_meta( $post = null ) {

	$post = get_post( $post );
	if( is_a( $post, 'WP_Post' ) && function_exists( 'youxi_post_format_id' ) ) {

		$post_format = get_post_format( $post->ID );
		$meta_key    = youxi_post_format_id( $post_format );
		$post_meta   = (array) $post->$meta_key;

		switch( $post_format ) {
			case 'video':
				$post_meta = wp_parse_args( $post_meta, array(
					'type' => '', 
					'embed' => '', 
					'src' => '', 
					'poster' => ''
				));
				if( ( 'embed' == $post_meta['type'] && '' !== $post_meta['embed'] ) || 
					( 'hosted' == $post_meta['type'] && '' !== $post_meta['src'] ) ) {
					return $post_meta;
				}
				break;
			case 'audio':
				$post_meta = wp_parse_args( $post_meta, array(
					'type' => '', 
					'embed' => '', 
					'src' => ''
				));
				if( ( 'embed' == $post_meta['type'] && '' !== $post_meta['embed'] ) || 
					( 'hosted' == $post_meta['type'] && '' !== $post_meta['src'] ) ) {
					return $post_meta;
				}
				break;
			case 'gallery':
				$post_meta = wp_parse_args( $post_meta, array( 'images' => array(), 'type' => 'slider' ) );
				if( ! empty( $post_meta['images'] ) && is_array( $post_meta['images'] ) ) {
					return $post_meta;
				}
				break;
			default:
				break;
		}

	}
}
endif;
