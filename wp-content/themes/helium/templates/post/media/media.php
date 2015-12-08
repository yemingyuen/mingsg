<?php if( has_post_thumbnail() ): ?>
<section class="post-media">

	<figure class="post-featured-image">
		<?php the_post_thumbnail( 'full', array( 'itemprop' => 'image' ) ); ?>
	</figure>
	
</section>
<?php endif;
