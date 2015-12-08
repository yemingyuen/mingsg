<?php

$grid_settings = helium_portfolio_grid_defaults();

get_header();

?><div class="content-area-wrap">

	<div class="content-area fullwidth">

		<div class="content-header">

			<div class="content-header-affix clearfix"><?php

				?><h1 class="content-title">
					<?php single_term_title( Youxi()->option->get( 'portfolio_archive_page_title' ) . ': ' ); ?>
				</h1>

			</div>

		</div>

		<div class="content-wrap">

			<?php if( have_posts() ):

				$gridlist_classes = array( 'portfolio-grid', 'grid-list', 'grid-list-' . $grid_settings['layout'] );

				if( 'justified' != $grid_settings['layout'] ) {
					$col_class = strtr( $grid_settings['columns'], array( 3 => 'three', 4 => 'four', 5 => 'five' ) );
					$gridlist_classes[] = $col_class . '-columns';
				}

				// Prepare portfolio HTML attributes
				$gridlist_attributes = array();

				// Portfolio class
				$gridlist_attributes['class'] = join( ' ', $gridlist_classes );

				// Portfolio data-* attributes
				foreach( array( 'layout', 'pagination', 'ajax_button_text', 'ajax_button_complete_text' ) as $opt ) {
					$gridlist_attributes[ 'data-gridlist-' . str_replace( '_', '-', $opt ) ] = $grid_settings[ $opt ];
				}

				// Create the HTML markup
				foreach( $gridlist_attributes as $attr => $value ) {
					$gridlist_attributes[ $attr ] = $attr . '="' . esc_attr( $value ) . '"';
				}
				$gridlist_attributes = join( ' ', $gridlist_attributes );

				// Determine thumbnail size
				$thumbnail_size = ( 'classic' == $grid_settings['layout'] ) ? 
					'helium_portfolio_thumb_4by3' : 'helium_portfolio_thumb';

			?><div <?php echo $gridlist_attributes ?>>

				<div class="grid-list-wrap"><?php

				if( 'masonry' == $grid_settings['layout'] ):

				?><div class="grid-sizer"></div>
				<?php endif;

					while( have_posts() ) : the_post();

						$post_terms = wp_get_post_terms( get_the_ID(), get_object_taxonomies( get_post_type() ) );
						$post_class = wp_list_pluck( $post_terms, 'slug' );
						$post_terms = wp_list_pluck( $post_terms, 'name' );

					?><article <?php post_class( array_merge( array( 'grid' ), $post_class ) ); ?> itemscope itemtype="http://schema.org/Article">

						<div class="grid-inner">

							<?php if( has_post_thumbnail() ):

								$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(), $thumbnail_size );

								$thumb_url = wp_get_attachment_url( get_post_thumbnail_id() );
								$thumb_class = 'grid-list-image-link grid-list-mfp';

								if( 'page' === $grid_settings['behavior'] ) {
									$thumb_url = get_permalink();
									$thumb_class = 'grid-list-image-link grid-list-page';
								}

							?><figure class="grid-list-image">
								<?php if( preg_match( '/^lightbox|page$/', $grid_settings['behavior'] ) ):

								?><a href="<?php echo esc_url( $thumb_url ) ?>" class="<?php echo esc_attr( $thumb_class ) ?>">
									<span class="grid-list-image-placeholder" style="padding-top: <?php echo esc_attr( 100 * $thumbnail[2] / $thumbnail[1] ); ?>%;">
										<?php the_post_thumbnail( $thumbnail_size, array( 'itemprop' => 'image' ) ); ?>
									</span>
									<span class="overlay"></span>
								</a>
								<?php else: 

								?><span class="grid-list-image-placeholder" style="padding-top: <?php echo esc_attr( 100 * $thumbnail[2] / $thumbnail[1] ); ?>%;">
									<?php the_post_thumbnail( $thumbnail_size, array( 'itemprop' => 'image' ) ); ?>
								</span>
								<?php endif; ?>
							</figure>
							<?php endif; ?>

							<div class="portfolio-info">
								<a class="portfolio-info-link" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" itemprop="url">
									<?php the_title( '<h3 class="entry-title portfolio-title" itemprop="name">', '</h3>' );

									if( $post_terms ):

									?><p class="portfolio-meta">
										<?php echo join( ', ', $post_terms ); ?>
									</p>
									<?php endif; ?>
								</a>
							</div>

						</div>

					</article><?php

					endwhile;

				?></div>

				<?php

				if( 'show_all' != $grid_settings['pagination'] ):

					echo '<div class="grid-list-nav">';
						helium_entry_pagination( $grid_settings['pagination'] );
					echo '</div>';
					
				endif; ?>

			</div>
			
			<?php endif; ?>

		</div>

	</div>

</div>
<?php get_footer(); ?>