<?php if( $post_format_meta = helium_extract_post_format_meta() ): ?>
<section class="post-media post-media-gallery">

	<?php if( 'slider' === $post_format_meta['type'] ): ?>

	<div class="royalSlider rsHelium" data-rs-settings="<?php echo esc_attr( helium_rs_settings( $post_format_meta ) ); ?>"><?php
		foreach( $post_format_meta['images'] as $image ):
			$attachment = wp_get_attachment_image_src( $image, 'full' );
			echo wp_get_attachment_image( $image, 'full', false, array(
				'class' => 'attachment-full rsImg', 
				'data-rsw' => $attachment[1], 
				'data-rsh' => $attachment[2]
			));
		endforeach;
	?></div>

	<?php elseif( 'justified' === $post_format_meta['type'] ): ?>

	<div class="justified-grids"><?php
		foreach( $post_format_meta['images'] as $image ):
			echo wp_get_attachment_image( $image, 'full' );
		endforeach;
	?></div>
	<?php endif; ?>

</section>
<?php
else:
	Youxi()->templates->get( 'media/media', null, get_post_type() );
endif;
