<?php
/*
Template Name: Portfolio
*/
if( ! function_exists( 'youxi_portfolio_cpt_name' ) ) {
	get_template_part( 'page' );
	return;
}

global $wp_query;

// Template is used for displaying portfolio archive
if(  is_post_type_archive( youxi_portfolio_cpt_name() ) ) {

	/* Get the portfolio grid settings from defaults */
	$grid_settings = helium_portfolio_grid_defaults();

	/* Make sure the included categories is an empty array */
	$grid_settings['include'] = array();

	/* Get default archive title */
	$the_title = Youxi()->option->get( 'portfolio_archive_page_title' );

	$the_query = $wp_query;

// Template used as page template
} else {

	if( have_posts() ) : the_post();

		/* Get the portfolio grid settings */
		$grid_settings = $post->portfolio_grid_settings;
		if( ! isset( $grid_settings['use_defaults'] ) || ! $grid_settings['use_defaults'] ) {
			$grid_settings = wp_parse_args( $grid_settings, helium_portfolio_grid_defaults() );
		} else {
			$grid_settings = helium_portfolio_grid_defaults();
		}

		if( is_front_page() && ! is_home() ) {
			$paged = get_query_var( 'page' ) ? intval( get_query_var( 'page' ) ) : 1;
		} else {
			$paged = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
		}

		// Make sure we're on page 1 when using AJAX/Infinite pagination
		if( preg_match( '/^(ajax|infinite)$/', $grid_settings['pagination'] ) && $paged > 1 ) {
			$grid_settings['pagination'] = 'numbered';
		}

		if( 'show_all' == $grid_settings['pagination'] ) {
			$grid_settings['posts_per_page'] = -1;
		}

		if( 'menu_order' == $grid_settings['orderby'] ) {
			$grid_settings['order'] = 'ASC';
		}

		/* Prepare query arguments */
		$_query = array(
			'post_type'        => youxi_portfolio_cpt_name(), 
			'posts_per_page'   => $grid_settings['posts_per_page'], 
			'orderby'          => $grid_settings['orderby'], 
			'order'            => $grid_settings['order'], 
			'paged'            => $paged, 
			'suppress_filters' => false
		);

		/* Clean the includes from empty values */
		$grid_settings['include'] = array_filter( (array) $grid_settings['include'] );

		if( $grid_settings['include'] ) {
			$_query['tax_query'] = array(
				array(
					'taxonomy' => youxi_portfolio_tax_name(), 
					'field' => 'id', 
					'terms' => $grid_settings['include'], 
					'operator' => 'IN'
				)
			);
		}

		/* Set the title as the current page title */
		$the_title = get_the_title();

		/* Create the query */
		$the_query = new WP_Query( $_query );

	else:
		get_template_part( 'page' );
		return;
	endif;

}

get_header();

// Override main query
$wp_query = $the_query;

?><div class="content-area-wrap">

	<div class="content-area fullwidth">

		<div class="content-header">

			<div class="content-header-affix clearfix"><?php

				?><h1 class="content-title">
					<?php echo $the_title; ?>
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

				<?php if( $grid_settings['show_filter'] ):

					/* Get portfolio taxonomy terms */
					$terms = get_terms( youxi_portfolio_tax_name(), array( 'include' => $grid_settings['include'] ) );

					/* Output portfolio filters */
					if( $terms && count( $terms ) > 1 ):

					?><div class="grid-list-filter">
						<span class="filter-label"><?php esc_html_e( 'Filter', 'helium' ); ?></span><?php

						?><ul class="filter-items plain-list"><?php

							?><li>
								<a href="<?php echo esc_url( get_post_type_archive_link( youxi_portfolio_cpt_name() ) ); ?>" class="filter active" data-filter="*">
									<?php esc_html_e( 'All', 'helium' ) ?>
								</a>
							</li><?php

							foreach( $terms as $term ):
								if( empty( $term->slug ) ) {
									continue;
								}

								$term_link = get_term_link( $term );
								$term_link = is_wp_error( $term_link ) ? '#' : $term_link;

							?><li>
								<a href="<?php echo esc_url( $term_link ) ?>" class="filter" data-filter=".<?php echo esc_attr( $term->slug ) ?>">
									<?php echo esc_html( $term->name ) ?>
								</a>
							</li><?php 
							endforeach; ?>
						</ul>
					</div>
					<?php
					endif;

				endif;

				?><div class="grid-list-wrap"><?php

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

						<div class="grid-loader"><div class="helium-loader"></div></div>

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
<?php

// Restore the main query
wp_reset_query();

get_footer(); ?>