<?php
	$attributes = array();
	$attributes['data-widget']      = 'gmap';
	$attributes['data-zoom']        = intval( $zoom );
	$attributes['data-markers']     = json_encode( $markers );
	$attributes['data-center']      = implode( ',', array( $center_lat, $center_lng ) );
	$attributes['data-map-type-id'] = $map_type;
	$attributes['data-monochrome']  = json_encode( $monochrome );

	foreach( (array) $controls as $id => $control ) {
		$attributes['data-' . $id . '-control'] = $control;
	}

	$html = '';
	foreach( (array) $attributes as $key => $val ) {
		$html .= " {$key}=\"" . esc_attr( $val ) . "\"";
	}

	$aspect_ratio = 100.0 * ( max( 1, intval( $aspect_ratio['h'] ) ) / max( 1, intval( $aspect_ratio['w'] ) ) );

?><div class="google-maps-container" style="padding-bottom: <?php echo esc_attr( $aspect_ratio ) ?>%;">
	<div class="google-maps"<?php echo $html ?>></div>
</div>