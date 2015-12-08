<?php

global $wp_query;
$grid_settings = helium_edd_grid_defaults();

get_header();

?><div class="content-area-wrap">

	<div class="content-area fullwidth">

		<div class="content-header">

			<div class="content-header-affix clearfix"><?php

				?><h1 class="content-title">
					<?php single_term_title( Youxi()->option->get( 'edd_archive_page_title' ) . ': ' ); ?>
				</h1>

			</div>

		</div>

		<div class="content-wrap">

			<?php if( have_posts() ):

				$gridlist_classes = array( 'edd-download-grid', 'grid-list', 'grid-list-masonry' );

				$col_class = strtr( $grid_settings['columns'], array( 3 => 'three', 4 => 'four', 5 => 'five' ) );
				$gridlist_classes[] = $col_class . '-columns';

				// Prepare EDD HTML attributes
				$gridlist_attributes = array();

				// EDD class
				$gridlist_attributes['class'] = join( ' ', $gridlist_classes );

				// EDD data-* attributes
				foreach( array( 'pagination', 'ajax_button_text', 'ajax_button_complete_text' ) as $opt ) {
					$gridlist_attributes[ 'data-gridlist-' . str_replace( '_', '-', $opt ) ] = $grid_settings[ $opt ];
				}

				// Create the HTML markup
				foreach( $gridlist_attributes as $attr => $value ) {
					$gridlist_attributes[ $attr ] = $attr . '="' . esc_attr( $value ) . '"';
				}
				$gridlist_attributes = join( ' ', $gridlist_attributes );

			?><div <?php echo $gridlist_attributes ?>>

				<div class="edd-download-wrap grid-list-wrap">

					<div class="grid-sizer"></div>
			
					<?php while( have_posts() ) : the_post();

					?><article <?php post_class( 'grid' ); ?> itemscope itemtype="http://schema.org/Product">

						<div class="grid-inner">

							<?php if( has_post_thumbnail() ):

								$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(), 'helium_portfolio_thumb' );

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
										<?php the_post_thumbnail( 'helium_portfolio_thumb', array( 'itemprop' => 'image' ) ); ?>
									</span>
									<span class="overlay"></span>
								</a>
								<?php else: 

								?><span class="grid-list-image-placeholder" style="padding-top: <?php echo esc_attr( 100 * $thumbnail[2] / $thumbnail[1] ); ?>%;">
									<?php the_post_thumbnail( 'helium_portfolio_thumb', array( 'itemprop' => 'image' ) ); ?>
								</span>
								<?php endif; ?>
							</figure>
							<?php endif; ?>

							<div class="edd-download-info-wrap">
								<div class="edd-download-actions">
									<?php if( ! edd_has_variable_prices( get_the_ID() ) ):
										echo edd_get_purchase_link(array(
											'price' => false, 
											'style' => '', 
											'class' => '', 
											'color' => ''
										));
									else: ?>
									<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="edd-download-view-details" itemprop="url">
										<i class="fa fa-external-link"></i>
										<?php esc_html_e( 'Details', 'helium' ); ?>
									</a>
									<?php endif; ?>
								</div>

								<div class="edd-download-info">
									<?php the_title( '<h3 class="entry-title edd-download-title" itemprop="name"><a href="' . esc_url( get_permalink() ) . '" itemprop="url">', '</a></h3>' ); ?>
									<p class="edd-download-price" itemprop="price"><?php
										if( ! edd_has_variable_prices( get_the_ID() ) ):
											edd_price( get_the_ID() );
										else:
											echo edd_price_range( get_the_ID() );
										endif;
									?></p>
								</div>
							</div>

						</div>

					</article>

					<?php endwhile; ?>

				</div>

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
<?php

// Restore the main query
wp_reset_postdata();

get_footer(); ?>