<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}
/**
 * Youxi Post Order Page Class
 *
 * This class creates the post order page.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */

if( ! class_exists( 'Youxi_Post_Order_Page' ) ) {

	class Youxi_Post_Order_Page extends Youxi_Post_Page {

		public function __construct( $page_title, $menu_title, $menu_slug, $capability = 'edit_posts', $callback = null ) {

			parent::__construct( $page_title, $menu_title, $menu_slug, $capability, $callback );

			if( is_admin() ) {
				add_action( 'wp_ajax_youxi-post-order-save', array( $this, 'save_order' ) );
			}
		}

		public function page_callback() {

			global $wp_version, $sitepress;

			/* When WPML is enabled, make sure to switch to the default language */
			if( is_a( $sitepress, 'SitePress' ) ) {

				$default_language = $sitepress->get_default_language();
				$current_language = $sitepress->get_current_language();

				if( $current_language != $default_language ) {

					$sitepress->switch_lang( $default_language );
				}
			}

			$posts = get_posts(array(
				'post_type' => $this->post_type_object->name, 
				'posts_per_page' => -1, 
				'orderby' => 'menu_order', 
				'order' => 'ASC', 
				'suppress_filters' => false
			));

			/* Restore the current language */
			if( is_a( $sitepress, 'SitePress' ) ) {

				$sitepress->switch_lang( $current_language );
			}

			?>
			<div class="wrap">
				
				<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

				<p><?php printf( __( 'Manage your %s ordering by using the drag and drop user interface below. Changes will be saved automatically and the order can be retrieved by querying posts by <strong>menu_order</strong>.', 'youxi' ), strtolower( $this->post_type_object->labels->singular_name ) ) ?></p>

				<div class="youxi-post-order">

					<div class="youxi-post-order-items-holder">

					<?php foreach( $posts as $post ): ?>

						<?php  ?>

						<div class="youxi-post-order-item" data-post-id="<?php echo esc_attr( $post->ID ) ?>">
							<div class="youxi-post-order-item-header">
								<div class="item-title">
									<h4><?php echo esc_html( $post->post_title ) ?></h4>
								</div>
							</div>
							<div class="youxi-post-order-item-preview">
								<?php do_action( "youxi_{$post->post_type}_post_order_item_preview", $post ); ?>
							</div>
						</div>
					<?php endforeach; ?>

					</div>

				</div>

			</div>
			<?php
		}

		public function admin_enqueue_scripts( $hook ) {

			if( parent::admin_enqueue_scripts( $hook ) ) {

				wp_enqueue_style(
					'youxi-post-order', 
					YOUXI_CORE_URL . 'admin/assets/css/youxi-post-order.css', 
					array(), 
					YOUXI_CORE_VERSION
				);

				wp_enqueue_script(
					'youxi-post-order', 
					YOUXI_CORE_URL . 'admin/assets/js/youxi.post-order.js', 
					array( 'jquery', 'jquery-ui-sortable', 'wp-util' ), 
					YOUXI_CORE_VERSION, 
					true
				);

				wp_localize_script( 'youxi-post-order', 'YouxiPostOrder', array(
					'nonce' => wp_create_nonce( 'youxi-post-order-nonce' )
				));
			}
		}

		public function save_order() {

			if( ! check_ajax_referer( 'youxi-post-order-nonce', 'nonce', false ) )
				wp_die();

			if( ! current_user_can( 'edit_posts' ) )
				wp_die( __( 'You\'re not allowed to do this action.', 'youxi' ) );

			if( isset( $_POST['menu_order'] ) ) {

				foreach( (array) $_POST['menu_order'] as $menu_order => $ID ) {
					wp_update_post( compact( 'ID', 'menu_order' ) );
				}
			}

			wp_send_json_success();
		}

	}
}