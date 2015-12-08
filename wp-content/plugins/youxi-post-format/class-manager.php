<?php
class Youxi_Post_Formats_Manager {

	/**
	 * Array of post format metabox configurations
	 *
	 * @access private
	 * @var array
	 */
	private $metaboxes = array();

	/**
	 * Constructor.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ), 999 );
	}

	/**
	 * Initialize by constructing metaboxes based on supported post formats
	 *
	 * @since 1.0
	 */
	public function init() {

		if( $post_formats_support = get_theme_support( 'post-formats' ) ) {

			/* Get supported post formats */
			if( ! isset( $post_formats_support[0] ) || ! is_array( $post_formats_support[0] ) ) {
				$post_formats_support = get_post_format_slugs();
			} else {
				$post_formats_support = $post_formats_support[0];
			}

			/* Prepare post format metaboxes */
			$this->metaboxes = array();
			foreach( $post_formats_support as $post_format ) {
				if( is_callable( "youxi_post_format_{$post_format}_metabox" ) ) {
					$metabox_id = youxi_post_format_id( $post_format );
					$metabox = call_user_func( "youxi_post_format_{$post_format}_metabox" );
					$this->metaboxes[ $post_format ] = array_merge(
						array(
							'id' => $metabox_id, 
							'html_classes' => array( 'youxi-metabox' )
						), 
						$metabox
					);
				}
			}

			/* Attach metabox on each supported post types */
			foreach( (array) youxi_post_format_post_types() as $post_type ) {

				/* Make sure it's an existing post type */
				if( ! post_type_exists( $post_type ) ) {
					continue;
				}

				/* Get the post type wrapper object */
				$post_type_object = Youxi_Post_Type::get( $post_type );

				/* Add the metaboxes */
				foreach( $this->metaboxes as $metabox ) {
					$post_type_object->add_meta_box( new Youxi_Metabox( $metabox['id'], $metabox ) );
				}
			}

			/* Prepare on the 'add_meta_boxes' hook */
			add_action( 'add_meta_boxes', array( $this, 'prepare' ) );
		}
	}

	/**
	 * Prepare scripts inclusion by checking post type
	 *
	 * @since 1.0
	 */
	public function prepare( $post_type ) {
		if( in_array( $post_type, youxi_post_format_post_types() ) ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		}
	}

	/**
	 * Enqueue necessary post format scripts
	 *
	 * @since 1.0
	 */
	public function admin_enqueue_scripts( $hook ) {

		if( in_array( $hook, array( 'post.php', 'post-new.php' ) ) ) {

			if( $post_formats_support = get_theme_support( 'post-formats' ) ) {

				/* Get supported post formats */
				if( ! isset( $post_formats_support[0] ) || ! is_array( $post_formats_support[0] ) ) {
					$post_formats_support = get_post_format_slugs();
				} else {
					$post_formats_support = $post_formats_support[0];
				}

				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
				wp_enqueue_script(
					'youxi-post-formats', 
					YOUXI_POST_FORMAT_URL . "/assets/js/youxi.post-formats{$suffix}.js", 
					array( 'jquery' ), 
					YOUXI_POST_FORMAT_VERSION, 
					true
				);

				$metabox_id_map = array();
				foreach( $this->metaboxes as $id => $metabox ) {
					$metabox_id_map[ $id ] = $metabox['id'];
				}

				wp_localize_script( 'youxi-post-formats', 'YouxiPostFormatsConfig', array(
					'metaboxes' => $metabox_id_map
				));
			}
		}
	}
}