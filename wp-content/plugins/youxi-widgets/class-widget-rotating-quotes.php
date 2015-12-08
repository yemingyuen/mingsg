<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

class Youxi_Rotating_Quotes_Widget extends Youxi_WP_Widget {

	public function __construct() {

		$widget_opts  = array( 'classname' => 'youxi-rotating-quotes-widget', 'description' => __( 'Use this widget to display a set of rotating quotes.', 'youxi' ) );
		$control_opts = array( 'width' => '400px' );

		// Initialize WP_Widget
		parent::__construct( 'rotating-quotes-widget', __( 'Youxi &raquo; Rotating Quotes', 'youxi' ), $widget_opts, $control_opts );
	}

	public function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		$instance = wp_parse_args( (array) $instance, array(
			'title' => '', 
			'duration' => 6000, 
			'quotes' => array()
		));

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
			'duration' => 6000, 
			'quotes' => array()
		));

		extract( $vars );

		?><p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'youxi' ); ?>:</label> 
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'duration' ) ); ?>"><?php _e( 'Duration', 'youxi' ); ?>:</label> 
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'duration' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'duration' ) ); ?>" type="number" min="100" value="<?php echo esc_attr( $duration ); ?>">
		</p>
		<div class="youxi-repeater" data-tmpl="<?php echo esc_attr( $this->id ) ?>">
			<script id="tmpl-<?php echo esc_attr( $this->id ) ?>" type="text/html">
			<?php echo $this->get_template() ?>
			</script>
			<label for="<?php echo esc_attr( $this->get_field_id( 'quotes' ) ); ?>"><?php _e( 'Quotes', 'youxi' ); ?>:</label>
			<div class="youxi-repeater-items-wrap">
			<?php if( is_array( $quotes ) ) : ?>
				<?php foreach( $quotes as $index => $quote ): 
					$quote = wp_parse_args( $quote, array( 'text' => '', 'author' => '', 'source' => '' ) );
				?>
				<?php echo $this->get_template( $index, $quote ); ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</div>
			<br>
			<button type="button" class="button button-small button-repeater-add"><?php echo _e( 'Add Quote', 'youxi' ) ?></button>
		</div>
		<?php 
	}

	public function update( $new_instance, $old_instance ) {

		foreach( $new_instance['quotes'] as &$quote ) {
			$quote['text']   = strip_tags( $quote['text'] );
			$quote['author'] = strip_tags( $quote['author'] );
			$quote['source'] = strip_tags( $quote['source'] );
		}

		$instance = array(
			'title'    => strip_tags( $new_instance['title'] ), 
			'duration' => absint( $new_instance['duration'] ), 
			'quotes'   => array_values( $new_instance['quotes'] )
		);

		return apply_filters( "youxi_widgets_{$this->id_base}_new_instance", $instance, $this->id );
	}

	protected function get_template( $index = '{{ data.index }}', $values = array() ) {
		$values = wp_parse_args( $values, array(
			'text' => '', 
			'author' => '', 
			'source' => ''
		));

		ob_start(); ?>
		<table class="widefat youxi-repeater-item">
			<tr>
				<td colspan="2">
					<p>
						<strong><?php _e( 'Quote', 'youxi' ) ?></strong>
						<span style="float: right;">
							<a href="#" class="button-repeater-remove">&times;</a>
						</span>
					</p>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( "quotes-$index-text" ) ) ?>"><?php _e( 'Text', 'youxi' ) ?>:</label>
						<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( "quotes-$index-text" ) ) ?>" rows="4" name="<?php echo esc_attr( $this->get_field_name( "quotes][$index][text" ) ) ?>"><?php echo esc_textarea( $values['text'] ) ?></textarea>
					</p>
				</td>
			</tr>
			<tr>
				<td>
					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( "quotes-$index-author" ) ) ?>"><?php _e( 'Author', 'youxi' ) ?>:</label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( "quotes-$index-author" ) ) ?>" name="<?php echo esc_attr( $this->get_field_name( "quotes][$index][author" ) ) ?>" type="text" value="<?php echo esc_attr( $values['author'] ) ?>">
					</p>
				</td>
				<td>
					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( "quotes-$index-source" ) ) ?>"><?php _e( 'Source', 'youxi' ) ?>:</label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( "quotes-$index-source" ) ) ?>" name="<?php echo esc_attr( $this->get_field_name( "quotes][$index][source" ) ) ?>" type="text" value="<?php echo esc_attr( $values['source'] ) ?>">
					</p>
				</td>
			</tr>
		</table>
		<?php return ob_get_clean();
	}
}