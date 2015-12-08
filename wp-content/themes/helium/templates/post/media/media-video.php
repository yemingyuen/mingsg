<?php if( $post_format_meta = helium_extract_post_format_meta() ): ?>
<section class="post-media post-media-video-<?php echo esc_attr( $post_format_meta['type'] ) ?>">

	<?php switch( $post_format_meta['type'] ):

		case 'embed': ?>
		<div class="media">
			<?php global $wp_embed;
			if( is_a( $wp_embed, 'WP_Embed' ) ):
				echo $wp_embed->autoembed( $post_format_meta['embed'] );
			else:
				echo $post_format_meta['embed'];
			endif; ?>
		</div>
		<?php break;

		case 'hosted': ?>
		<div class="media">
			<?php
			// Check if the attachment is a video
			if( wp_attachment_is( 'video', $post_format_meta['src'] ) ):

				$meta = wp_get_attachment_metadata( $post_format_meta['src'] );
				if( isset( $meta['width'], $meta['height'] ) ) {
					$video_ar = 100.0 * $meta['height'] / $meta['width'];
					printf( '<div class="wp-video-wrapper" style="padding-top: %s%%">', $video_ar );
				}

				echo wp_video_shortcode(array(
					'src' => wp_get_attachment_url( $post_format_meta['src'] ), 
					'poster' => wp_get_attachment_url( $post_format_meta['poster'] )
				));

				if( isset( $video_ar ) ) {
					echo '</div>';
				}
			endif; ?>
		</div>

		<?php break;

	endswitch; ?>

</section>

<?php else:
	Youxi()->templates->get( 'media/media', null, get_post_type() );
endif;
