<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}
/**
 * Youxi Post Type Class
 *
 * This class is a helper wrapper class for easily registering post types.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */

if( ! class_exists( 'Youxi_Post_Type' ) ) {

	class Youxi_Post_Type {

		/**
		 * @access private
		 * @var string The name of the post type 
		 */
		private $post_type;

		/**
		 * @access private
		 * @var array The arguments of the post type 
		 */
		private $post_type_args;

		/**
		 * @access private
		 * @var array The meta_boxes to attach on the post type edit screen 
		 */
		private $meta_boxes = array();

		/**
		 * @access private
		 * @var array The taxonomies to add on the post type
		 */
		private $taxonomies = array();

		/**
		 * @access private
		 * @var array The post type submenu pages
		 */
		private $submenu_pages = array();

		/**
		 * @access private
		 * @var array The post types already initialized through this class
		 */
		private static $__post_types = array();

		/**
		 * Constructor
		 *
		 * @param string The ID of the post type
		 * @param array The arguments to pass to register_post_type
		 */
		private function __construct( $post_type, $args = array() ) {

			$this->post_type      = $post_type;
			$this->post_type_args = $args;

			if( is_admin() ) {

				/* Register Save Hook on a lower priority for WPML compatibility */
				add_action( 'save_post', array( $this, 'save' ), 9 );

				/* Force displaying the post type on nav-menus */
				add_action( 'admin_init', array( $this, 'display_post_type_nav_box' ) );

				/* Add the registered submenu pages */
				add_action( 'admin_menu', array( $this, 'add_submenu_pages' ) );

				/* Register the metaboxes */
				add_action( "add_meta_boxes_{$this->post_type}", array( $this, 'add_meta_boxes' ) );

				/* Register manage columns actions */
				if( is_post_type_hierarchical( $this->post_type ) ) {
					add_filter( "manage_pages_columns", array( $this, 'manage_columns' ) );
					add_action( "manage_pages_custom_column", array( $this, 'manage_custom_columns' ), 10, 2 );
				} else {
					add_filter( "manage_{$this->post_type}_posts_columns", array( $this, 'manage_columns' ) );
					add_action( "manage_{$this->post_type}_posts_custom_column", array( $this, 'manage_custom_columns' ), 10, 2 );
				}
			}
		}

		/**
		 * Get a post type object, makes sure that each post type only has one object
		 *
		 * @param string The ID of the post type
		 * @param array The arguments to pass to register_post_type
		 */
		public static function get( $post_type, $args = array() ) {

			// Check if wrapper object already exists
			if( isset( self::$__post_types[ $post_type ] ) && is_a( self::$__post_types[ $post_type ], get_class() ) ) {
				return self::$__post_types[ $post_type ];
			}

			// Do not allow modifying these core post types
			if( in_array( $post_type, array( 'attachment', 'revision', 'nav_menu_item' ) ) ) {
				return;
			}

			// Do not allow modifying core post type arguments
			if( in_array( $post_type, array( 'post', 'page' ) ) ) {
				$args = array();
			}

			return ( self::$__post_types[ $post_type ] = new Youxi_Post_Type( $post_type, $args ) );
		}

		/**
		 * Register the post type, this should be called on the `init` hook
		 */
		public function register() {

			/* Register Post Type */
			if( ! post_type_exists( $this->post_type ) ) {
				register_post_type( $this->post_type, $this->post_type_args );
			}

			/* Register Taxonomies */
			foreach( $this->taxonomies as $taxonomy ) {
				if( is_a( $taxonomy, 'Youxi_Taxonomy' ) ) {
					$taxonomy->register( $this->post_type );
				}
			}
		}

		/**
		 * Register the attached meta boxes
		 */
		public function add_meta_boxes( $post ) {
			foreach( (array) $this->meta_boxes as $meta_box ) {
				if( is_a( $meta_box, 'Youxi_Metabox' ) ) {
					$meta_box->register( $post->post_type );
				}
			}
		}

		/**
		 * Register the attached submenu pages
		 */
		public function add_submenu_pages() {
			foreach( (array) $this->submenu_pages as $submenu_page ) {
				if( is_a( $submenu_page, 'Youxi_Post_Page' ) ) {
					$submenu_page->register( $this->post_type );
				}
			}
		}

		/**
		 * Attach a taxonomy on this post type
		 *
		 * @param Youxi_Taxonomy The taxonomy object to attach on this post type
		 *
		 * @return $this
		 */
		public function add_taxonomy( $taxonomy ) {
			if( is_a( $taxonomy, 'Youxi_Taxonomy' ) ) {
				$this->taxonomies[] = $taxonomy;
			}
			return $this;
		}

		/**
		 * Add a metabox on this post type
		 *
		 * @param Youxi_Metabox The metabox object to attach on this post type
		 *
		 * @return $this
		 */
		public function add_meta_box( $metabox ) {
			if( is_a( $metabox, 'Youxi_Metabox' ) ) {
				$this->meta_boxes[] = $metabox;
			}
			return $this;
		}

		/**
		 * Add a submenu page on this post type
		 *
		 * @param Youxi_Post_Type_Page The post type page object to attach on this post type
		 *
		 * @return $this
		 */
		public function add_submenu_page( $submenu_page ) {
			if( is_a( $submenu_page, 'Youxi_Post_Page' ) ) {
				$this->submenu_pages[] = $submenu_page;
			}
			return $this;
		}

		/**
		 * Save the post type data on submit
		 *
		 * @param string The ID of the post to save
		 *
		 * @return string The ID of the saved post
		 */
		public function save( $post_id ) {

			if( empty( $this->meta_boxes ) ) {
				return $post_id;
			}

			if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post_id;
			}

			if( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}

			if( isset( $_POST[ $this->post_type ] ) && get_post_type( $post_id ) === $this->post_type ) {
				foreach( (array) $this->meta_boxes as $metabox ) {
					if( is_a( $metabox, 'Youxi_Metabox' ) ) {
						$metabox->save_fields( $post_id, $_POST[ $this->post_type ] );
					}
				}
			}

			return $post_id;
		}

		/**
		 * Filter the manage post screen table headers
		 *
		 * @param array The array of table headers
		 *
		 * @return array The filtered table headers
		 */
		public function manage_columns( $columns ) {

			$new_columns = array();
			foreach( (array) $this->meta_boxes as $metabox ) {
				if( is_a( $metabox, 'Youxi_Metabox' ) ) {
					$new_columns = array_merge( $new_columns, $metabox->get_visible_field_list() );
				}
			}

			/* If the theme and post support post-thumbnails, insert it right behind the custom fields */
			if( get_theme_support( 'post-thumbnails' ) && has_post_thumbnail() ) {
				$new_columns['thumbnail'] = __( 'Featured Image', 'youxi' );
			}

			/* Make sure to insert it before the date column */
			$new = array();
			foreach( $columns as $key => $column ) {
				if( 'date' == $key ) {
					foreach( $new_columns as $nkey => $ncolumn ) {
						$new[ $nkey ] = $ncolumn;
					}
				}
				$new[ $key ] = $column;
			}

			return $new;
		}

		/**
		 * Outputs the content of a table column on the manage post screen
		 *
		 * @param string The name of the column to manage
		 * @param string The ID of the current post
		 */
		public function manage_custom_columns( $column_name, $post_id ) {

			if( 'thumbnail' === $column_name ) {
				the_post_thumbnail( apply_filters( "youxi_{$this->post_type}_post_thumbnail_size", array( 100, 100 ) ) );
				return;
			}
			foreach( (array) $this->meta_boxes as $metabox ) {
				if( is_a( $metabox, 'Youxi_Metabox' ) ) {
					$value = $metabox->get_field_value( $column_name, $post_id );
					if( '' !== $value ) {
						echo $value;
						break;
					}
				}
			}
		}

		/**
		 * Hack to force displaying the custom post type on wp-admin/nav-menus.php
		 */
		public function display_post_type_nav_box() {

			$post_type_object = get_post_type_object( $this->post_type );
			
			if( ! $post_type_object || ! (bool) $post_type_object->show_in_nav_menus ) {
				return;
			}

			$hidden_nav_boxes = get_user_option( 'metaboxhidden_nav-menus' );
			$post_type_nav_box = 'add-' . $this->post_type;

			if( in_array( $post_type_nav_box, (array) $hidden_nav_boxes ) ) {
				foreach( $hidden_nav_boxes as $i => $nav_box ) {
					if( $nav_box == $post_type_nav_box ) {
						unset( $hidden_nav_boxes[ $i ] );
					}
				}
				update_user_option( get_current_user_id(), 'metaboxhidden_nav-menus', $hidden_nav_boxes );
			}
		}
	}
}