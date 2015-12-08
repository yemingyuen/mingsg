<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}
/**
 * Youxi Post Page Class
 *
 * This class is a helper wrapper class for easily registering post subpages.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */

if( ! class_exists( 'Youxi_Post_Page' ) ) {

	abstract class Youxi_Post_Page {

		/**
		 * @access protected
		 * @var array The page title for the submenu page
		 */
		protected $page_title;

		/**
		 * @access protected
		 * @var array The menu title for the submenu page
		 */
		protected $menu_title;

		/**
		 * @access protected
		 * @var array The menu slug for the submenu page
		 */
		protected $menu_slug;

		/**
		 * @access protected
		 * @var array The capability required to view the submenu page
		 */
		protected $capability;

		/**
		 * @access protected
		 * @var array The callback to render the submenu page contents
		 */
		protected $callback;

		/**
		 * @access private
		 * @var string The page hook this post type page is registered to
		 */
		private $page_hook;

		/**
		 * Constructor.
		 */
		public function __construct( $page_title, $menu_title, $menu_slug, $capability = 'edit_posts', $callback = null ) {

			$this->page_title = $page_title;
			$this->menu_title = $menu_title;
			$this->menu_slug = $menu_slug;
			$this->capability = $capability;
			$this->callback = is_callable( $callback ) ? $callback : array( $this, 'page_callback' );

			if( is_admin() ) {

				add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
			}
		}

		/**
		 * Register the submenu page on a post type
		 */
		public function register( $post_type ) {

			$this->post_type_object = get_post_type_object( $post_type );

			if( is_object( $this->post_type_object ) ) {

				$this->page_hook = add_submenu_page(
					"edit.php?post_type={$this->post_type_object->name}", 
					$this->page_title, 
					$this->menu_title, 
					$this->capability, 
					$this->menu_slug, 
					$this->callback
				);
			}
		}

		/**
		 * Abstract method to render the contents of the page
		 */
		abstract public function page_callback();

		/**
		 * Parent method for enqueing the scripts
		 *
		 * @return bool Value indicating the user is on this page
		 */
		public function admin_enqueue_scripts( $hook ) {
			return $hook == $this->page_hook;
		}
	}
}
