<?php
$related_posts_count = Youxi()->option->get( 'blog_related_posts_count' );
$related_posts = Youxi()->entries->get_related( $related_posts_count );

if( ! empty( $related_posts ) ):

?><section class="post-related-posts">

	<h3 class="post-section-title h4"><?php esc_html_e( 'Related Posts', 'helium' ); ?></h3>

	<div class="row"><?php

	global $post;

	$related_posts = array_slice( $related_posts, 0, $related_posts_count );
	$col_width = min( 12, ( 12 / max( $related_posts_count, count( $related_posts ) ) ) );
	$use_lightbox = ( 'lightbox' === Youxi()->option->get( 'blog_related_posts_behavior' ) );

	foreach( $related_posts as $post ): setup_postdata( $post );

	?><article <?php post_class( array( 'related-entry', 'col-md-' . $col_width ) ); ?>>

		<?php if( has_post_thumbnail() ): 

			if( $use_lightbox ):

			?><figure class="related-entry-media">
				<a href="<?php echo esc_url( wp_get_attachment_url( get_post_thumbnail_id() ) ) ?>" title="<?php the_title_attribute(); ?>" class="mfp-image">
					<?php the_post_thumbnail( 'helium_16by9' ); ?>
					<span class="overlay"></span>
				</a>
			</figure><?php

				else:

			?><figure class="related-entry-media">
				<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>">
					<?php the_post_thumbnail( 'helium_16by9' ); ?>
				</a>
			</figure><?php 

			endif;

		endif;

		the_title( '<h5 class="related-entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h5>' );

		?><p class="related-entry-meta">
			<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
				<?php echo esc_html( get_the_date( get_option( 'date_format' ) ) ); ?>
			</time>
		</p>

		<div class="spacer-30 hidden-md hidden-lg"></div>
		
	</article>
	<?php endforeach;

	wp_reset_postdata();

	?></div>

</section>
<?php endif;
