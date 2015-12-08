<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

/* ==========================================================================
	Portfolio Grid Defaults
============================================================================= */

if( ! function_exists( 'helium_portfolio_grid_defaults' ) ):

function helium_portfolio_grid_defaults() {

	return wp_parse_args( array(
		'show_filter'               => Youxi()->option->get( 'portfolio_grid_show_filter' ), 
		'pagination'                => Youxi()->option->get( 'portfolio_grid_pagination' ), 
		'ajax_button_text'          => Youxi()->option->get( 'portfolio_grid_ajax_button_text' ), 
		'ajax_button_complete_text' => Youxi()->option->get( 'portfolio_grid_ajax_button_complete_text' ), 
		'posts_per_page'            => Youxi()->option->get( 'portfolio_grid_posts_per_page' ), 
		'include'                   => Youxi()->option->get( 'portfolio_grid_include' ), 
		'behavior'                  => Youxi()->option->get( 'portfolio_grid_behavior' ), 
		'orderby'                   => Youxi()->option->get( 'portfolio_grid_orderby' ), 
		'order'                     => Youxi()->option->get( 'portfolio_grid_order' ), 
		'layout'                    => Youxi()->option->get( 'portfolio_grid_layout' ), 
		'columns'                   => Youxi()->option->get( 'portfolio_grid_columns' )
	), array(
		'show_filter'               => true, 
		'pagination'                => 'ajax', 
		'ajax_button_text'          => esc_html__( 'Load More', 'helium' ), 
		'ajax_button_complete_text' => esc_html__( 'No More Items', 'helium' ), 
		'posts_per_page'            => get_option( 'posts_per_page' ), 
		'include'                   => array(), 
		'behavior'                  => 'lightbox', 
		'orderby'                   => 'date', 
		'order'                     => 'DESC', 
		'layout'                    => 'masonry', 
		'columns'                   => 4
	));
}
endif;

/* ==========================================================================
	Portfolio Body Class
============================================================================= */

if( ! function_exists( 'helium_portfolio_body_class' ) ):

function helium_portfolio_body_class( $classes ) {

	if( function_exists( 'youxi_portfolio_cpt_name' ) && is_singular( youxi_portfolio_cpt_name() ) ) {

		$post = get_queried_object();
		if( is_a( $post, 'WP_Post' ) ) {

			/* Layout metadata */
			$layout = wp_parse_args( $post->layout, array(
				'media_position'   => 'top', 
				'details_position' => 'left'
			));

			/* Validate layout positions */
			if( ! preg_match( '/^top|(lef|righ)t$/', $layout['media_position'] ) ) {
				$layout['media_position'] = 'top';
			}

			if( ! preg_match( '/^hidden|(lef|righ)t$/', $layout['details_position'] ) ) {
				$layout['details_position'] = 'left';
			}

			/* Media metadata */
			$media = wp_parse_args( $post->media, array(
				'type' => 'featured-image'
			));

			/* Validate media type */
			if( ! preg_match( '/^(featur|stack|justifi)ed(-(image|grids))?|slider|(vide|audi)o$/', $media['type'] ) ) {
				$media['type'] = 'featured-image';
			}

			$classes = array_merge( $classes, array(
				"single-{$post->post_type}-media-" . $layout['media_position'], 
				"single-{$post->post_type}-media-" . $media['type'], 
				"single-{$post->post_type}-details-" . $layout['details_position']
			));
		}
	}

	return $classes;
}
endif;
add_filter( 'body_class', 'helium_portfolio_body_class' );

/* ==========================================================================
	Portfolio Pages
============================================================================= */

if( ! function_exists( 'helium_portfolio_pages' ) ):

function helium_portfolio_pages() {

	$choices = array(
		'default' => esc_html__( 'Default Archive', 'helium' )
	);
	
	$pages = get_posts(array(
		'post_type'        => 'page', 
		'meta_key'         => '_wp_page_template', 
		'meta_value'       => 'archive-portfolio.php', 
		'suppress_filters' => false
	));

	if( $pages ) {
		foreach( $pages as $page ) {
			$choices[ $page->ID ] = $page->post_title;
		}
	}

	return $choices;
}
endif;
