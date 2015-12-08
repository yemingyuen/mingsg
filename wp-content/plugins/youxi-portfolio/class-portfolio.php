<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

if( ! class_exists( 'Youxi_Portfolio' ) ) {

	class Youxi_Portfolio {

		private static $__registered = false;

		/* Register portfolio post type */
		public static function register() {

			/* Make sure to register only once */
			if( self::$__registered ) {
				return;
			}
			self::$__registered = true;

			/* Get the post type settings */
			$settings = wp_parse_args( self::post_type_args(), array(
				'args' => array(), 
				'labels' => array(), 
				'metaboxes' => array(), 
				'taxonomies' => array()
			));

			extract( $settings, EXTR_SKIP );

			/* Merge the labels into the args */
			$args = array_merge( $args, compact( 'labels' ) );

			/* Create the post type object */
			$post_type_object = Youxi_Post_Type::get( self::post_type_name(), $args );

			/* Add the metaboxes */
			foreach( $metaboxes as $metabox_id => $metabox ) {
				$post_type_object->add_meta_box( new Youxi_Metabox( $metabox_id, $metabox ) );
			}

			/* Add the taxonomies */
			foreach( $taxonomies  as $tax_id => $taxonomy ) {
				$post_type_object->add_taxonomy( new Youxi_Taxonomy( $tax_id, $taxonomy ) );
			}

			if( is_admin() ) {

				/* Attach post type ordering page */
				$ordering_page = new Youxi_Post_Order_Page(
					__( 'Order Portfolio', 'youxi' ), 
					__( 'Order Portfolio', 'youxi' ), 
					'youxi-portfolio-order-page'
				);
				$post_type_object->add_submenu_page( $ordering_page );
			}

			/* Register the post type */
			$post_type_object->register();
		}

		/* The post type name for portfolio */
		public static function post_type_name() {
			return apply_filters( 'youxi_portfolio_post_type_name', 'portfolio' );
		}

		/* The default taxonomy name for portfolio */
		public static function taxonomy_name() {
			return apply_filters( 'youxi_portfolio_taxonomy_name', 'portfolio-category' );
		}

		/* The one page post type arguments */
		public static function post_type_args() {

			$taxonomies = array();
			$taxonomies[ self::taxonomy_name() ] = array(
				'labels' => array(
					'name'                      =>__( 'Portfolio Categories', 'youxi' ), 
					'singular_name'             =>__( 'Portfolio Category', 'youxi' ), 
					'all_items'                 =>__( 'All Portfolio Categories', 'youxi' ), 
					'edit_item'                 =>__( 'Edit Portfolio Category', 'youxi' ), 
					'view_item'                 =>__( 'View Portfolio Category', 'youxi' ), 
					'update_item'               =>__( 'Update Portfolio Category', 'youxi' ), 
					'add_new_item'              =>__( 'Add New Portfolio Category', 'youxi' ), 
					'new_item_name'             =>__( 'New Portfolio Category Name', 'youxi' ), 
					'parent_item'               =>__( 'Parent Portfolio Category', 'youxi' ), 
					'parent_item_colon'         =>__( 'Parent Portfolio Category: ', 'youxi' ), 
					'search_items'              =>__( 'Search Portfolio Categories', 'youxi' ), 
					'popular_items'             =>__( 'Popular Portfolio Categories', 'youxi' ), 
					'separate_items_with_commas' => __( 'Separate portfolio categories with commas', 'youxi' ), 
					'add_or_remove_items'       =>__( 'Add or remove portfolio categories', 'youxi' ), 
					'choose_from_most_used'     =>__( 'Choose from most used portfolio categories', 'youxi' ), 
					'not_found'                 =>__( 'No portfolio categories found.', 'youxi' )
				), 
				'show_tagcloud' => false, 
				'show_admin_column' => true, 
				'show_in_nav_menus' => false
			);

			/* Return the settings for the portfolio cpt */
			return array(

				'args' => apply_filters( 'youxi_portfolio_cpt_args', array(
					'description' => __( 'This post type is used to save your portfolio.', 'youxi' ), 
					'capability_type' => 'post', 
					'public' => true, 
					'menu_icon' => 'dashicons-portfolio', 
					'has_archive' => true, 
					'show_in_nav_menus' => true, 
					'supports' => array( 'title', 'editor', 'thumbnail', 'page-attributes' )
				) ), 

				'labels' => apply_filters( 'youxi_portfolio_cpt_labels', array(
					'name'               => __( 'Portfolio', 'youxi' ), 
					'singular_name'      => __( 'Portfolio', 'youxi' ), 
					'all_items'          => __( 'All Portfolio', 'youxi' ), 
					'add_new'            => __( 'Add New Portfolio', 'youxi' ),
					'add_new_item'       => __( 'Add New Portfolio', 'youxi' ),
					'edit_item'          => __( 'Edit Portfolio', 'youxi' ),
					'view_item'          => __( 'View Portfolio', 'youxi' ),
					'search_items'       => __( 'Search Portfolio', 'youxi' ),
					'not_found'          => __( 'Portfolio not found', 'youxi' ),
					'not_found_in_trash' => __( 'Portfolio not found in trash', 'youxi' ),
					'parent_item_colon'  => __( 'Portfolio: ', 'youxi' )
				) ), 

				'metaboxes' => apply_filters( 'youxi_portfolio_cpt_metaboxes', array() ), 

				'taxonomies' => apply_filters( 'youxi_portfolio_cpt_taxonomies', $taxonomies )
			);
		}
	}
}

function youxi_portfolio_cpt_name() {
	return apply_filters( 'youxi_portfolio_cpt_name', Youxi_Portfolio::post_type_name() );
}

function youxi_portfolio_tax_name() {
	return apply_filters( 'youxi_portfolio_tax_name', Youxi_Portfolio::taxonomy_name() );
}

function youxi_portfolio_settings() {
	return Youxi_Portfolio::post_type_args();
}

add_action( 'init', array( 'Youxi_Portfolio', 'register' ) );
