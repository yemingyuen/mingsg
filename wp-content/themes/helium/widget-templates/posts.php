<?php

global $post;

foreach( $posts as $post ): setup_postdata( $post );

?><article class="post-list-entry clearfix">

	<?php if( has_post_thumbnail() ): ?>
	<figure class="post-list-media">
		<?php the_post_thumbnail( 'thumbnail' ); ?>
	</figure>
	<?php endif; ?>

	<div class="post-list-info">
		<?php the_title( '<h5 class="post-list-title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h5>' ); ?>
		<time class="post-list-time" datetime="<?php echo esc_attr( get_the_date( 'c' ) ) ?>">
			<?php echo esc_html( get_the_date( 'F d, Y' ) ); ?>
		</time>
	</div>

</article><?php

endforeach;