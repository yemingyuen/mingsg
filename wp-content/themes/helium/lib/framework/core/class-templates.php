<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

class Youxi_Templates {

	protected $base_dir = '';

	protected $layouts = array();

	public function __construct() {

		$this->base_dir = apply_filters( 'youxi_templates_base_dir', 'templates/' );
		$this->layouts  = apply_filters( 'youxi_templates_layouts', array() );
	}

	public function get( $slug, $name = null, $post_type = null, $layout = null ) {

		if( isset( $post_type ) ) {

			$dir_name = trailingslashit( $this->base_dir ) . trailingslashit( $post_type );

			if( isset( $layout, $this->layouts[ $post_type ] ) && is_array( $this->layouts[ $post_type ] ) ) {

				if( in_array( $layout, $this->layouts[ $post_type ] ) ) {

					$templates = array();
					$name = (string) $name;

					if( '' !== $name ) {
						$templates[] = $dir_name . trailingslashit( $layout ) . "{$slug}-{$name}.php";
					}
					$templates[] = $dir_name . trailingslashit( $layout ) . "{$slug}.php";

					if( '' !== $name ) {
						$templates[] = $dir_name . "{$slug}-{$name}.php";
					}
					$templates[] = $dir_name . "{$slug}.php";

					locate_template( $templates, true, false );

				} else {

					get_template_part( $dir_name . $slug, $name );

				}

			} else {

				get_template_part( $dir_name . $slug, $name );

			}

		} else {

			get_template_part( trailingslashit( $this->base_dir ) . $slug, $name );

		}

	}

}
