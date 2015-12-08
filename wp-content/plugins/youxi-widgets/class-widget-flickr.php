<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

class Youxi_Flickr_Widget extends Youxi_WP_Widget {

	public function __construct() {

		$widget_opts  = array( 'classname' => 'youxi-flickr-widget', 'description' => __( 'Use this widget to display your Flickr feed.', 'youxi' ) );
		$control_opts = array();

		// Initialize WP_Widget
		parent::__construct( 'flickr-widget', __( 'Youxi &raquo; Flickr', 'youxi' ), $widget_opts, $control_opts );
	}

	public function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		$instance = wp_parse_args( (array) $instance, array(
			'title'     =>'', 
			'flickr_id' =>'', 
			'limit'     =>8
		) );

		$instance = apply_filters( "youxi_widgets_{$this->id_base}_instance", $instance, $this->id );

		echo $before_widget;

		if( isset( $instance['title'] ) && ! empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title', $instance['title'] ) . $after_title;

		$this->maybe_load_template( $id, $instance );

		echo $after_widget;
	}

	public function form( $instance ) {

		$vars = wp_parse_args( (array) $instance, array(
			'title'     => __( 'My Flickr Feed', 'youxi' ), 
			'flickr_id' => '', 
			'limit'     => 8
		));

		extract( $vars );

		?><p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'youxi' ); ?>:</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'flickr_id' ) ); ?>"><?php _e( 'Flickr ID', 'youxi' ); ?>:</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'flickr_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'flickr_id' ) ); ?>" type="text" value="<?php echo esc_attr( $flickr_id ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"><?php _e( 'Maximum Photos', 'youxi' ); ?>:</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" type="number" value="<?php echo esc_attr( $limit ); ?>" min="1">
		</p>
		<?php 
	}

	public function update( $new_instance, $old_instance ) {

		$instance = array();

		$instance['title']     = strip_tags( $new_instance['title'] );
		$instance['flickr_id'] = strip_tags( $new_instance['flickr_id'] );
		$instance['limit']     = absint( strip_tags( $new_instance['limit'] ) );

		return apply_filters( "youxi_widgets_{$this->id_base}_new_instance", $instance, $this->id );
	}

	public function enqueue() {

		if( parent::enqueue() ) {

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			if( ! wp_script_is( 'jflickrfeed' ) ) {
				wp_enqueue_script( 'jflickrfeed', self::frontend_plugins_url( "jflickrfeed/jflickrfeed{$suffix}.js" ), array( 'jquery' ), '1.0', true );
			}
		}
	}

	public function get_defaults() {

		$widget_name = preg_replace( array('/\W/', '/_?widget_?/' ), '', $this->id_base );
				
		return apply_filters( "youxi_widgets_{$widget_name}_defaults", array(
			'itemTemplate' => '<li><a href="{{link}}" title="{{title}}" target="_blank"><img width="150" height="150" src="{{image_q}}" alt="{{title}}"></a></li>'
		));
	}
}