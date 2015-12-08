<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

class Youxi_Recent_Posts_Widget extends Youxi_WP_Widget {

	public function __construct() {

		$widget_opts  = array( 'classname' => 'youxi-recent-posts-widget', 'description' => __( 'Use this widget to display your recent posts', 'youxi' ) );
		$control_opts = array();

		// Initialize WP_Widget
		parent::__construct( 'recent-posts-widget', __( 'Youxi &raquo; Recent Posts', 'youxi' ), $widget_opts, $control_opts );
	}

	public function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		$instance = wp_parse_args( (array) $instance, array(
			'title' => '', 
			'number' => 3, 
			'category__not_in' => array(), 
			'tag__not_in' => array()
		));

		$instance = apply_filters( "youxi_widgets_{$this->id_base}_instance", $instance, $this->id );

		extract( $instance, EXTR_SKIP );

		echo $before_widget;

		if( ! empty( $title ) ) {
			echo $before_title . apply_filters( 'widget_title', $title ) . $after_title;
		}

		global $post;
		$tmp_post = $post;

		// Setup the query
		$posts = get_posts(array(
			'posts_per_page' => $number, 
			'category__not_in' => (array) $category__not_in, 
			'tag__not_in' =>  (array) $tag__not_in, 
			'suppress_filters' => false
		));

		$this->maybe_load_template( $id, compact( 'posts' ) );

		$post = $tmp_post;
		if( is_a( $post, 'WP_Post' ) ) {
			setup_postdata( $post );
		}

		echo $after_widget;
	}

	public function form( $instance ) {

		$vars = wp_parse_args( (array) $instance, array(
			'title' => '', 
			'number' => 3, 
			'category__not_in' => array(), 
			'tag__not_in' => array()
		));

		extract( $vars );
		
		?><p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'youxi' ); ?>:</label> 
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php _e( 'Number of Posts', 'youxi' ); ?>:</label> 
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>">
		</p>
		<?php if( $categories = get_categories() ): ?>
		<p>
			<label><?php _e( 'Categories to Exclude', 'youxi' ); ?>:</label> 
			<br>
			<?php foreach( $categories as $index => $term ): ?>
				<input id="<?php echo esc_attr( $this->get_field_id( 'category__not_in' ) . "_{$index}" ) ?>" type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'category__not_in' ) ); ?>[]" value="<?php echo esc_attr( $term->term_id ) ?>" <?php checked( in_array( $term->term_id, (array) $category__not_in ) ) ?>>
				<label for="<?php echo esc_attr( $this->get_field_id( 'category__not_in' ) . "_{$index}" ) ?>"><?php echo esc_html( $term->name ) ?></label>
				<br>
			<?php endforeach; ?>
		</p>
		<?php endif;
		if( $tags = get_tags() ): ?>
		<p>
			<label><?php _e( 'Tags to Exclude', 'youxi' ); ?>:</label> 
			<br>
			<?php foreach( $tags as $index => $term ): ?>
				<input id="<?php echo esc_attr( $this->get_field_id( 'tag__not_in' ) . "_{$index}" ) ?>" type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'tag__not_in' ) ); ?>[]" value="<?php echo esc_attr( $term->term_id ) ?>" <?php checked( in_array( $term->term_id, (array) $tag__not_in ) ) ?>>
				<label for="<?php echo esc_attr( $this->get_field_id( 'tag__not_in' ) . "_{$index}" ) ?>"><?php echo esc_html( $term->name ) ?></label>
				<br>
			<?php endforeach; ?>
		</p>
		<?php endif;
	}

	public function update( $new_instance, $old_instance ) {

		$new_instance = wp_parse_args( $new_instance, array(
			'title'            => '', 
			'number'           => 3, 
			'category__not_in' => array(), 
			'tag__not_in'      => array()
		));
		$valid_categories = wp_list_pluck( get_categories(), 'term_id' );
		$valid_tags       = wp_list_pluck( get_tags(), 'term_id' );

		$instance = array(
			'title'            => strip_tags( $new_instance['title'] ), 
			'number'           => intval( strip_tags( $new_instance['number'] ) ), 
			'category__not_in' => array_intersect( $valid_categories, (array) $new_instance['category__not_in'] ), 
			'tag__not_in'      => array_intersect( $valid_tags, (array) $new_instance['tag__not_in'] )
		);

		return apply_filters( "youxi_widgets_{$this->id_base}_new_instance", $instance, $this->id );
	}
}