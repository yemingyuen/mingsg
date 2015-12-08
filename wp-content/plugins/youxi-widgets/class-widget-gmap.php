<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

class Youxi_Google_Maps_Widget extends Youxi_WP_Widget {

	public function __construct() {

		$widget_opts  = array( 'classname' => 'youxi-google-maps-widget', 'description' => __( 'Use this widget to display a Google Map.', 'youxi' ) );
		$control_opts = array( 'width' => '400px' );

		// Initialize WP_Widget
		parent::__construct( 'google-maps-widget', __( 'Youxi &raquo; Google Maps', 'youxi' ), $widget_opts, $control_opts );
	}

	public function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		$instance = wp_parse_args( (array) $instance, array(
			'title'        => '', 
			'center_lng'   => 0.0, 
			'center_lat'   => 0.0, 
			'zoom'         => 0, 
			'map_type'     => 'ROADMAP', 
			'monochrome'   => false, 
			'controls'     => array(), 
			'markers'      => array(), 
			'aspect_ratio' => array( 'w' => 16, 'h' => 9 )
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
			'title'        => '', 
			'center_lng'   => 0.0, 
			'center_lat'   => 0.0, 
			'zoom'         => 0, 
			'map_type'     => 'ROADMAP', 
			'monochrome'   => false, 
			'controls'     => array(), 
			'markers'      => array(), 
			'aspect_ratio' => array( 'w' => 16, 'h' => 9 )
		));

		$available_controls = array(
			'pan' => 'Pan', 
			'zoom' => 'Zoom', 
			'map-type' => 'Map Type', 
			'scale' => 'Scale', 
			'street-view' => 'Street View', 
			'overview-map' => 'Overview Map'
		);

		extract( $vars );

		?><p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'youxi' ); ?>:</label> 
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'center_lat' ) ); ?>"><?php _e( 'Center Latitude', 'youxi' ); ?>:</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'center_lat' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'center_lat' ) ); ?>" type="text" value="<?php echo esc_attr( $center_lat ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'center_lng' ) ); ?>"><?php _e( 'Center Longitude', 'youxi' ); ?>:</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'center_lng' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'center_lng' ) ); ?>" type="text" value="<?php echo esc_attr( $center_lng ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'zoom' ) ); ?>"><?php _e( 'Zoom', 'youxi' ); ?>:</label>
			<input type="number" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'zoom' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'zoom' ) ); ?>" min="0" max="20" value="<?php echo esc_attr( $zoom ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'map_type' ) ); ?>"><?php _e( 'Map Type', 'youxi' ); ?>:</label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'map_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'map_type' ) ); ?>">
				<?php foreach( array( 'HYBRID', 'ROADMAP', 'SATELLITE', 'TERRAIN' ) as $t ): ?>
				<option value="<?php echo $t ?>" <?php selected( $map_type, $t ) ?>><?php echo $t ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'aspect_ratio' ) ); ?>"><?php _e( 'Aspect Ratio (Width : Height)', 'youxi' ); ?>:</label><br>
			<input name="<?php echo esc_attr( $this->get_field_name( 'aspect_ratio' ) ); ?>[w]" type="text" value="<?php echo esc_attr( $aspect_ratio['w'] ); ?>"> : 
			<input name="<?php echo esc_attr( $this->get_field_name( 'aspect_ratio' ) ); ?>[h]" type="text" value="<?php echo esc_attr( $aspect_ratio['h'] ); ?>">
		</p>
		<p>
			<input type="hidden" name="<?php echo esc_attr( $this->get_field_name( 'monochrome' ) ) ?>" value="0">
			<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'monochrome' ) ) ?>" name="<?php echo esc_attr( $this->get_field_name( 'monochrome' ) ) ?>" value="1" <?php checked( $monochrome, 1 ) ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'monochrome' ) ) ?>"><?php _e( 'Display in grayscale', 'youxi' ) ?></label>
		</p>
		<p>
			<?php foreach( $available_controls as $id => $name ): ?>
			<input type="hidden" name="<?php echo esc_attr( $this->get_field_name( 'controls][' . $id ) ); ?>" value="0">
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'controls[' . $id . ']' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'controls][' . $id ) ); ?>" <?php checked( isset( $controls[ $id ] ) ? $controls[ $id ] : false, true ) ?> value="1">
			<label for="<?php echo esc_attr( $this->get_field_id( 'controls[' . $id . ']' ) ); ?>"><?php printf( esc_html__( 'Display %s Control', 'youxi' ), $name ) ?></label><br>
			<?php endforeach; ?>
		</p>
		<div class="youxi-repeater" data-tmpl="<?php echo esc_attr( $this->id ) ?>">
			<script id="tmpl-<?php echo esc_attr( $this->id ) ?>" type="text/html">
			<?php echo $this->get_template() ?>
			</script>
			<label for="<?php echo esc_attr( $this->get_field_id( 'markers' ) ); ?>"><?php _e( 'Markers', 'youxi' ); ?>:</label>
			<div class="youxi-repeater-items-wrap">
			<?php if( is_array( $markers ) ) : ?>
				<?php foreach( $markers as $index => $marker ): 
					$marker = wp_parse_args( $marker, array( 'lat' => '', 'lng' => '' ) );
				?>
				<?php echo $this->get_template( $index, $marker ); ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</div>
			<button type="button" class="button button-small button-repeater-add"><?php echo _e( 'Add Marker', 'youxi' ) ?></button>
		</div>
		<?php
	}

	public function update( $new_instance, $old_instance ) {

		foreach( $new_instance['markers'] as &$marker ) {
			$marker['lat'] = floatval( $marker['lat'] );
			$marker['lng'] = floatval( $marker['lng'] );
		}

		$new_instance['aspect_ratio']['w'] = max( 1, floatval( $new_instance['aspect_ratio']['w'] ) );
		$new_instance['aspect_ratio']['h'] = max( 1, floatval( $new_instance['aspect_ratio']['h'] ) );

		$instance = array(
			'title'        => strip_tags( $new_instance['title'] ), 
			'center_lat'   => floatval( $new_instance['center_lat'] ), 
			'center_lng'   => floatval( $new_instance['center_lng'] ), 
			'zoom'         => absint( $new_instance['zoom'] ), 
			'monochrome'   => (bool) $new_instance['monochrome'], 
			'map_type'     => strip_tags( $new_instance['map_type'] ), 
			'controls'     => $new_instance['controls'], 
			'markers'      => array_values( $new_instance['markers'] ), 
			'aspect_ratio' => $new_instance['aspect_ratio']
		);

		return apply_filters( "youxi_widgets_{$this->id_base}_new_instance", $instance, $this->id );
	}

	public function enqueue() {

		if( parent::enqueue() ) {

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			if( ! wp_script_is( 'gmap3', 'registered' ) ) {
				wp_register_script( 'gmap3', self::frontend_plugins_url( "gmap/gmap3{$suffix}.js" ), array( 'jquery' ), '6.0.0', true );
			}

			if( ! wp_script_is( 'youxi-gmap' ) ) {
				wp_enqueue_script( 'youxi-gmap', self::frontend_scripts_url( "youxi.gmap{$suffix}.js" ), array( 'gmap3' ), '1.0', true );
			}
		}
	}

	protected function get_template( $index = '{{ data.index }}', $values = array() ) {

		$values = wp_parse_args( $values, array(
			'lat' => 0.0, 
			'lng' => 0.0
		));

		ob_start(); ?>
		<table class="widefat youxi-repeater-item">
			<tr>
				<td colspan="2">
					<p>
						<strong><?php _e( 'Marker', 'youxi' ) ?></strong>
						<span style="float: right;">
							<a href="#" class="button-repeater-remove">&times;</a>
						</span>
					</p>
				</td>
			</tr>
			<tr>
				<td>
					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( "markers-$index-lat" ) ) ?>"><?php _e( 'Latitude', 'youxi' ) ?>:</label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( "markers-$index-lat" ) ) ?>" name="<?php echo esc_attr( $this->get_field_name( "markers][$index][lat" ) ) ?>" type="text" value="<?php echo esc_attr( $values['lat'] ) ?>">
					</p>
				</td>
				<td>
					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( "markers-$index-lng" ) ) ?>"><?php _e( 'Longitude', 'youxi' ) ?>:</label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( "markers-$index-lng" ) ) ?>" name="<?php echo esc_attr( $this->get_field_name( "markers][$index][lng" ) ) ?>" type="text" value="<?php echo esc_attr( $values['lng'] ) ?>">
					</p>
				</td>
			</tr>
		</table>
		<?php return ob_get_clean();
	}
}