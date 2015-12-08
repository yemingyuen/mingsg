<article <?php post_class( 'search-entry' ); ?> itemscope itemtype="http://schema.org/Article">

	<div class="search-entry-wrap clearfix">

		<?php if( has_post_thumbnail() ): ?>
		<figure class="search-entry-thumbnail">
			<?php the_post_thumbnail( 'thumbnail', array( 'itemprop' => 'image' ) ); ?>
		</figure>
		<?php endif; ?>

		<div class="search-entry-content">

			<header class="search-entry-header">

				<?php $post_type_object = get_post_type_object( get_post_type() );

				if( is_object( $post_type_object ) ):

				?><span class="search-entry-post-type label label-primary">
					<?php if( $post_type_object->has_archive ):

					?><a href="<?php echo esc_url( get_post_type_archive_link( get_post_type() ) ); ?>">
						<?php echo esc_html( $post_type_object->labels->singular_name ); ?>
					</a>
					<?php else:
						echo esc_html( $post_type_object->labels->singular_name );
					endif; ?>
				</span>
				<?php endif;

				the_title( '<h5 class="entry-title search-entry-title" itemprop="name"><a href="' . get_permalink() . '" title="' . the_title_attribute( array( 'echo' => false ) ) . '" itemprop="url">', '</a></h5>' ); ?>

				<div class="entry-content search-entry-excerpt">
					<?php the_excerpt(); ?>
				</div>

			</header>

		</div>

	</div>

</article>
