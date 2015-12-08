<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

class Youxi_Quote_Widget extends Youxi_WP_Widget {

	public function __construct() {

		$widget_opts  = array( 'classname' => 'youxi-quote-widget', 'description' => __( 'Use this widget to display a quote', 'youxi' ) );
		$control_opts = array( 'width' => '400px' );

		// Initialize WP_Widget
		parent::__construct( 'quote-widget', __( 'Youxi &raquo; Quote', 'youxi' ), $widget_opts, $control_opts );
	}

	public function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		$instance = wp_parse_args( (array) $instance, array(
			'title' => '', 
			'content' => '', 
			'name' => '', 
			'source' => ''
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
			'title' => '', 
			'content' => '', 
			'name' => '', 
			'source' => ''
		));

		extract( $vars );

		?><p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'youxi' ); ?>:</label> 
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'content' ) ); ?>"><?php _e( 'Content', 'youxi' ); ?>:</label> 
			<textarea class="widefat" rows="10" id="<?php echo esc_attr( $this->get_field_id( 'content' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'content' ) ); ?>"><?php echo esc_textarea( $content ); ?></textarea>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'name' ) ); ?>"><?php _e( 'Name', 'youxi' ); ?>:</label> 
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'name' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'name' ) ); ?>" type="text" value="<?php echo esc_attr( $name ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'source' ) ); ?>"><?php _e( 'Source', 'youxi' ); ?>:</label> 
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'source' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'source' ) ); ?>" type="text" value="<?php echo esc_attr( $source ); ?>">
		</p>
		<?php 
	}

	public function update( $new_instance, $old_instance ) {
		
		$instance = array();

		$instance['title']   = strip_tags( $new_instance['title'] );
		$instance['content'] = strip_tags( $new_instance['content'] );
		$instance['name']    = strip_tags( $new_instance['name'] );
		$instance['source']  = strip_tags( $new_instance['source'] );

		return apply_filters( "youxi_widgets_{$this->id_base}_new_instance", $instance, $this->id );
	}
}