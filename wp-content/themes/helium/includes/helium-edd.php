<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

/* ==========================================================================
	EDD Archive Options
============================================================================= */

if( ! function_exists( 'helium_edd_grid_defaults' ) ):

function helium_edd_grid_defaults() {

	return wp_parse_args( array(
		'show_filter'               => Youxi()->option->get( 'edd_grid_show_filter' ), 
		'pagination'                => Youxi()->option->get( 'edd_grid_pagination' ), 
		'ajax_button_text'          => Youxi()->option->get( 'edd_grid_ajax_button_text' ), 
		'ajax_button_complete_text' => Youxi()->option->get( 'edd_grid_ajax_button_complete_text' ), 
		'posts_per_page'            => Youxi()->option->get( 'edd_grid_posts_per_page' ), 
		'include'                   => Youxi()->option->get( 'edd_grid_include' ), 
		'behavior'                  => Youxi()->option->get( 'edd_grid_behavior' ), 
		'columns'                   => Youxi()->option->get( 'edd_grid_columns' )
	), array(
		'show_filter'               => true, 
		'pagination'                => 'ajax', 
		'ajax_button_text'          => 'Load More', 
		'ajax_button_complete_text' => 'No More Items', 
		'posts_per_page'            => get_option( 'posts_per_page' ), 
		'include'                   => array(), 
		'behavior'                  => 'lightbox', 
		'columns'                   => 4
	));
}
endif;

/* ==========================================================================
	EDD Archive Page
============================================================================= */

if( ! function_exists( 'helium_edd_ot_type_select_choices' ) ):

function helium_edd_ot_type_select_choices( $choices, $field_id ) {

	if( 'edd_archive_page' == $field_id ) {

		$pages = get_posts(array(
			'post_type'        => 'page', 
			'meta_key'         => '_wp_page_template', 
			'meta_value'       => 'archive-download.php', 
			'suppress_filters' => false
		));

		if( $pages ) {
			foreach( $pages as $page ) {
				$choices[] = array(
					'label' => $page->post_title, 
					'value' => $page->ID, 
					'src'   => ''
				);
			}
		}
	}

	return $choices;
}
endif;
add_filter( 'ot_type_select_choices', 'helium_edd_ot_type_select_choices', 10, 2 );

/* ==========================================================================
	EDD External Products Plugin
============================================================================= */

if( ! function_exists( 'helium_edd_external_product_link' ) ):

function helium_edd_external_product_link( $purchase_form, $args ) {

	// If the product has an external URL set
	if( $external_url = get_post_meta( $args['download_id'], '_edd_external_url', true ) ) {

		$purchase_form = '<div class="edd_download_purchase_form">';

			$purchase_form .= '<div class="edd_purchase_submit_wrapper">';

				$class = implode( ' ', array( $args['style'], $args['color'], trim( $args['class'] ) ) );

				$purchase_form .= '<a href="' . esc_url( $external_url ) . '" class="edd-external-product-link ' . esc_attr( $class ) . '">';

					$purchase_form .= '<span class="edd-external-product-link-label">' . $args['text'] . '</span>';

				$purchase_form .= '</a>';

			$purchase_form .= '</div>';

		$purchase_form .= '</div>';
	}

	// Return the possibly modified purchase form
	return $purchase_form;
}
endif;
remove_filter( 'edd_purchase_download_form', 'edd_external_product_link' );
add_filter( 'edd_purchase_download_form', 'helium_edd_external_product_link', 10, 2 );
