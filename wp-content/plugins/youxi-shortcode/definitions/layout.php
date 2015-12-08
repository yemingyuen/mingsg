<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

/**
 * Container Shortcode Handler
 */
function youxi_shortcode_container_cb( $atts, $content, $tag ) {
	return '<div class="container">' . do_shortcode( $content ) . '</div>';
}

/**
 * Fullwidth Shortcode Handler
 */
function youxi_shortcode_fullwidth_cb( $atts, $content, $tag ) {
	return do_shortcode( $content );
}

/**
 * Row Shortcode Handler
 */
function youxi_shortcode_row_cb( $atts, $content, $tag ) {
	return '<div class="row">' . do_shortcode( $content ) . '</div>';
}

/**
 * New Columns Shortcode Handler
 */
function youxi_shortcode_columns_cb( $atts, $content, $tag ) {

	extract( $atts, EXTR_SKIP );

	$size = max( min( $size, count( Youxi_Shortcode::get_default_columns() ) ), 1 );

	$classes = array( "col-{$type}-{$size}" );

	if( intval( $push ) > 0 ) {
		$classes[] = "col-{$type}-push-{$push}";
	}
	if( intval( $pull ) > 0 ) {
		$classes[] = "col-{$type}-pull-{$pull}";
	}

	return '<div class="' . join( ' ', $classes ) . '">' . do_shortcode( $content ) . '</div>';
}

/**
 * Old Columns Shortcode Handler
 */
function youxi_shortcode_grid_full_cb( $atts, $content, $tag ) {
	return '<div class="col-md-12">' . do_shortcode( $content ) . '</div>';
}

function youxi_shortcode_grid_eleven_twelfth_cb( $atts, $content, $tag ) {
	return '<div class="col-md-11">' . do_shortcode( $content ) . '</div>';
}

function youxi_shortcode_grid_five_sixth_cb( $atts, $content, $tag ) {
	return '<div class="col-md-10">' . do_shortcode( $content ) . '</div>';
}

function youxi_shortcode_grid_three_fourth_cb( $atts, $content, $tag ) {
	return '<div class="col-md-9">' . do_shortcode( $content ) . '</div>';
}

function youxi_shortcode_grid_two_thirds_cb( $atts, $content, $tag ) {
	return '<div class="col-md-8">' . do_shortcode( $content ) . '</div>';
}

function youxi_shortcode_grid_seven_twelfth_cb( $atts, $content, $tag ) {
	return '<div class="col-md-7">' . do_shortcode( $content ) . '</div>';
}

function youxi_shortcode_grid_half_cb( $atts, $content, $tag ) {
	return '<div class="col-md-6">' . do_shortcode( $content ) . '</div>';
}

function youxi_shortcode_grid_one_fifth_cb( $atts, $content, $tag ) {
	return '<div class="col-md-5">' . do_shortcode( $content ) . '</div>';
}

function youxi_shortcode_grid_one_third_cb( $atts, $content, $tag ) {
	return '<div class="col-md-4">' . do_shortcode( $content ) . '</div>';
}

function youxi_shortcode_grid_one_fourth_cb( $atts, $content, $tag ) {
	return '<div class="col-md-3">' . do_shortcode( $content ) . '</div>';
}

function youxi_shortcode_grid_one_sixth_cb( $atts, $content, $tag ) {
	return '<div class="col-md-2">' . do_shortcode( $content ) . '</div>';
}

function youxi_shortcode_grid_one_twelfth_cb( $atts, $content, $tag ) {
	return '<div class="col-md-1">' . do_shortcode( $content ) . '</div>';
}

/**
 * Separator Shortcode Handler
 */
function youxi_shortcode_separator_cb( $atts, $content, $tag ) {
	return '<hr>';
}

/**
 * Shortcode Definitions Callback
 */
function define_layout_shortcodes( $manager ) {

	/********************************************************************************
	 * Layout category
	 ********************************************************************************/
	$manager->add_category( 'layout', array(
		'label' => __( 'Layout Shortcodes', 'youxi' ), 
		'priority' => 10
	));

	/********************************************************************************
	 * Container shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'container', array(
		'label' => __( 'Container', 'youxi' ), 
		'category' => 'layout', 
		'priority' => 20, 
		'icon' => 'fa fa-align-justify', 
		'callback' => 'youxi_shortcode_container_cb'
	));

	/********************************************************************************
	 * Fullwidth shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'fullwidth', array(
		'label' => __( 'Fullwidth', 'youxi' ), 
		'category' => 'layout', 
		'priority' => 30, 
		'icon' => 'fa fa-arrows-h', 
		'callback' => 'youxi_shortcode_fullwidth_cb'
	));

	/********************************************************************************
	 * Columns shortcode
	 ********************************************************************************/
	if( ! Youxi_Shortcode::use_simple_columns() ) {

		$manager->add_shortcode( 'col', array(
			'label' => __( 'Column', 'youxi' ), 
			'category' => 'layout', 
			'priority' => 40, 
			'icon' => 'fa fa-th', 
			'callback' => 'youxi_shortcode_columns_cb', 
			'atts' => array(
				'size' => array(
					'type' => 'uislider', 
					'label' => __( 'Size', 'youxi' ), 
					'description' => __( 'Enter the column size.', 'youxi' ), 
					'widgetopts' => array(
						'min' => 1, 
						'max' => 12
					), 
					'std' => 1
				), 
				'type' => array(
					'type' => 'select', 
					'label' => __( 'Type', 'youxi' ), 
					'description' => __( 'Choose the column type.', 'youxi' ), 
					'choices' => array(
						'xs' => 'col-xs-*', 
						'sm' => 'col-sm-*', 
						'md' => 'col-md-*', 
						'lg' => 'col-lg-*'
					), 
					'std' => 'md'
				), 
				'push' => array(
					'type' => 'uislider', 
					'label' => __( 'Push', 'youxi' ), 
					'description' => __( 'Enter the amount of push.', 'youxi' ), 
					'widgetopts' => array(
						'min' => 0, 
						'max' => 12
					), 
					'std' => 0
				), 
				'pull' => array(
					'type' => 'uislider', 
					'label' => __( 'Pull', 'youxi' ), 
					'description' => __( 'Enter the amount of pulls.', 'youxi' ), 
					'widgetopts' => array(
						'min' => 0, 
						'max' => 12
					), 
					'std' => 0
				)
			)
		));

	} else {

		$column_names = Youxi_Shortcode::get_simple_columns();
		$column_sizes = Youxi_Shortcode::get_column_sizes();
		
		foreach( $column_sizes as $i => $size ) {
			
			if( isset( $column_names[ $size ] ) ) {

				$args = $column_names[ $size ];
				$tag = Youxi_Shortcode::unprefix( $args['tag'] );

				$manager->add_shortcode( $tag, array(
					'label' => $args['label'], 
					'category' => 'layout', 
					'priority' => 40 + ( $i * 10 ), 
					'icon' => 'fa fa-th', 
					'callback' => "youxi_shortcode_{$tag}_cb"
				));
			}
		}

	}

	/********************************************************************************
	 * Row shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'row', array(
		'label' => __( 'Row', 'youxi' ), 
		'category' => 'layout', 
		'priority' => 160, 
		'icon' => 'fa fa-align-justify', 
		'callback' => 'youxi_shortcode_row_cb'
	));

	/********************************************************************************
	 * Separator shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'separator', array(
		'label' => __( 'Separator', 'youxi' ), 
		'category' => 'layout', 
		'priority' => 170, 
		'icon' => 'fa fa-arrows-v', 
		'callback' => 'youxi_shortcode_separator_cb'
	));
}

/**
 * Hook to 'youxi_shortcode_register'
 */
add_action( 'youxi_shortcode_register', 'define_layout_shortcodes', 1 );