<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

abstract class Youxi_WP_Widget extends WP_Widget {

	public function __construct( $id_base, $name, $widget_options, $control_options ) {

		// Call the parent WP_Widget constructor
		parent::__construct( $id_base, $name, $widget_options, $control_options );

		// Register Scripts and Styles
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );

		// Add flag to initialize this widget
		add_filter( 'youxi_widgets_config_vars', array( $this, 'config_vars' ) );
	}

	public function enqueue() {

		$is_active_widget = is_active_widget( false, false, $this->id_base, true );

		if( ! wp_script_is( 'youxi-widgets' ) ) {

			if( apply_filters( "youxi_widgets_allow_{$this->id_base}_setup", true ) && $is_active_widget ) {

				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				wp_enqueue_script( 'youxi-widgets', self::frontend_scripts_url( "youxi.widgets{$suffix}.js" ), array( 'jquery' ), YOUXI_WIDGETS_VERSION, true );
				wp_localize_script( 'youxi-widgets', '_youxiWidgets', apply_filters( 'youxi_widgets_config_vars', array(
					'ajaxUrl' => admin_url( 'admin-ajax.php' )
				)));

			}

		}

		return apply_filters( "youxi_widgets_{$this->id_base}_enqueue_scripts", $is_active_widget );
	}

	protected function maybe_load_template( $sidebar_id, $vars ) {

		/* Determine the sidebar location using youxi_widgets_sidebar_location filter */
		$sidebar_location = apply_filters( 'youxi_widgets_sidebar_location', $sidebar_id );

		/* Get the widgets template directory using youxi_widgets_template_dir filter */
		$template_dir = trailingslashit( apply_filters( 'youxi_widgets_template_dir', '' ) );

		/* Template id */
		$template_id = preg_replace( '/-widget$/', '', $this->id_base );

		$templates = array(
			$template_dir . $template_id . '-' . $sidebar_location . '.php', 
			$template_dir . $template_id . '.php'
		);

		/* Find the template relative to the template directory */
		$template = locate_template( $templates );

		/* If the template is found */
		if( '' !== $template ) {
			extract( $vars );
			include( $template );
		}
	}

	public final function config_vars( $vars ) {

		if( apply_filters( "youxi_widgets_allow_{$this->id_base}_setup", true ) && 
			is_active_widget( false, false, $this->id_base, true ) ) {

			$widget_name = preg_replace( array( '/\W/', '/_?widget_?/' ), '', $this->id_base );
			$vars[ $widget_name ] = (object) $this->get_defaults();
		}

		return $vars;
	}

	public function get_defaults() {
		$widget_name = preg_replace( array( '/\W/', '/_?widget_?/' ), '', $this->id_base );
		return apply_filters( "youxi_widgets_{$widget_name}_defaults", array() );
	}

	public static function frontend_scripts_url( $url ) {
		return YOUXI_WIDGETS_URL . "frontend/assets/js/" . trim( $url, '/' );
	}

	public static function frontend_plugins_url( $url ) {
		return YOUXI_WIDGETS_URL . "frontend/plugins/" . trim( $url, '/' );
	}
}