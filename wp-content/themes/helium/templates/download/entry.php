<?php

/* Layout metadata */
$layout = wp_parse_args( $post->layout, array(
	'show_title' => true
));

?><article <?php post_class( 'content-area' ); ?> itemscope itemtype="http://schema.org/Product">

	<header class="content-header">

		<div class="content-header-affix clearfix"><?php

			the_title( '<h1 class="entry-title content-title" itemprop="name">', '</h1>' );

			$prev_post_link = get_previous_post_link(
				'<li class="content-nav-link">%link</li>', 
				'<span class="content-nav-link-wrap">' . 
					'<i class="fa fa-chevron-left"></i>' . 
					'<span class="content-nav-link-label">' . esc_html__( 'Previous', 'helium' ) . '</span>' . 
				'</span>'
			);

			$next_post_link = get_next_post_link(
				'<li class="content-nav-link">%link</li>', 
				'<span class="content-nav-link-wrap">' . 
					'<span class="content-nav-link-label">' . esc_html__( 'Next', 'helium' ) . '</span>' . 
					'<i class="fa fa-chevron-right"></i>' . 
				'</span>'
			);

			?><nav class="content-nav" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">

				<ul class="plain-list"><?php
					
					if( $prev_post_link ):
						echo $prev_post_link;
					else:
						echo '<li class="content-nav-link disabled">';
							echo '<span>';
								echo '<span class="content-nav-link-wrap">';
									echo '<i class="fa fa-chevron-left"></i>';
									echo '<span class="content-nav-link-label">' . esc_html__( 'Previous', 'helium' ) . '</span>';
								echo '</span>';
							echo '</span>';
						echo '</li>';
					endif;

					$archive_page = Youxi()->option->get( 'edd_archive_page' );
					if( 'default' == $archive_page ) {
						$archive_page = get_post_type_archive_link( 'downloads' );
					} else {
						$archive_page = get_post( $archive_page );
						if( $archive_page && 'page' == $archive_page->post_type && 'edd-store.php' == $archive_page->_wp_page_template ) {
							$archive_page = get_permalink( $archive_page );
						} else {
							$archive_page = get_post_type_archive_link( 'downloads' );
						}
					}

					if( $archive_page ) {

						echo '<li class="content-nav-link">';
							echo '<a href="' . esc_url( $archive_page ) . '">';
								echo '<span class="content-nav-link-wrap">';
									echo '<i class="fa fa-th"></i>';
								echo '</span>';
							echo '</a>';
						echo '</li>';
					}

					if( $next_post_link ):
						echo $next_post_link;
					else:
						echo '<li class="content-nav-link disabled">';
							echo '<span>';
								echo '<span class="content-nav-link-wrap">';
									echo '<span class="content-nav-link-label">' . esc_html__( 'Next', 'helium' ) . '</span>';
									echo '<i class="fa fa-chevron-right"></i>';
								echo '</span>';
							echo '</span>';
						echo '</li>';
					endif;
					
				?></ul>
				
			</nav>

		</div>

	</header>

	<div class="content-wrap">

		<div class="content-box clearfix">

			<?php if( has_post_thumbnail() ):

			?><div class="featured-content">
				<figure class="edd-download-featured-image">
					<?php the_post_thumbnail( 'full', array( 'itemprop' => 'image' ) ); ?>
				</figure>
			</div>
			<?php endif; ?>

			<div class="content-wrap-inner">

				<div class="container">

					<div class="row">

						<div class="col-lg-9 entry-content">

							<?php if( (bool) $layout['show_title'] ):
								the_title( '<h2 class="no-margin-top">', '</h2>' );
							endif; ?>

							<div class="entry-content" itemprop="description">
								<?php the_content(); ?>
							</div>

						</div>

						<div class="col-lg-3 entry-sidebar">

							<ul class="entry-details plain-list">
								<li>
									<span class="entry-detail-value edd-download-link-box">
										<?php if( ! edd_has_variable_prices( get_the_ID() ) ):
											echo '<span class="edd-download-price" itemprop="price">';
												edd_price( get_the_ID() );
											echo '</span>';
										endif;
										echo edd_get_purchase_link( array( 'price' => false ) ); ?>
									</span>
								</li>

								<?php if( Youxi()->option->get( 'edd_show_categories' ) ):
								?><li>
									<h5 class="entry-detail-label"><?php esc_html_e( 'Categories', 'helium' ) ?></h5>
									<span class="entry-detail-value"><?php echo get_the_term_list( get_the_ID(), 'download_category', '', ', ' ) ?></span>
								</li>
								<?php endif;

								if( Youxi()->option->get( 'edd_show_tags' ) ):
								?><li>
									<h5 class="entry-detail-label"><?php esc_html_e( 'Tags', 'helium' ) ?></h5>
									<span class="entry-detail-value"><?php echo get_the_term_list( get_the_ID(), 'download_tag', '', ', ' ) ?></span>
								</li>
								<?php endif;

								if( Youxi()->option->get( 'edd_show_sharing_buttons' ) ):

									$sharing_buttons = Youxi()->option->get( 'addthis_sharing_buttons' );
									$sharing_buttons = array_filter( array_map( 'trim', explode( ',', $sharing_buttons ) ) );

									if( ! empty( $sharing_buttons ) ):

								?><li>
									<h5 class="entry-detail-label"><?php esc_html_e( 'Share This', 'helium' ) ?></h5>
									<span class="entry-detail-value">
										<div class="addthis_toolbox addthis_default_style addthis_20x20_style" addthis:url="<?php the_permalink() ?>" addthis:title="<?php the_title_attribute() ?>">
											<?php array_walk( $sharing_buttons, 'helium_sharing_button' ); ?>
										</div>
									</span>
								</li>
								<?php endif;
								endif; ?>
								
							</ul>

						</div>

					</div>

				</div>

			</div>

		</div>

		<?php if( Youxi()->option->get( 'edd_show_related_items' ) ):
			Youxi()->templates->get( 'related', null, get_post_type() );
		endif; ?>

	</div>

</article>