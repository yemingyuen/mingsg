<?php
/*
Template Name: Portfolio Slider
*/
if( ! function_exists( 'youxi_portfolio_cpt_name' ) ) {
	get_template_part( 'page' );
	return;
}

if( have_posts() ) : the_post();

$slider_settings = wp_parse_args( $post->portfolio_slider_settings, array(
	'posts_per_page' => 5, 
	'orderby'        => 'date', 
	'order'          => 'DESC'
));

if( 'menu_order' == $slider_settings['orderby'] ) {
	$slider_settings['order'] = 'ASC';
}

$featured_posts = get_posts(array(
	'posts_per_page'      => $slider_settings['posts_per_page'], 
	'post_type'           => youxi_portfolio_cpt_name(), 
	'order'               => $slider_settings['order'], 
	'orderby'             => $slider_settings['orderby'], 
	'ignore_sticky_posts' => true, 
	'suppress_filters'    => false, 
	'no_found_rows'       => true, 
	'meta_query'          => array(
		array(
			'key'     => 'featured', 
			'value'   => 1, 
			'compare' => '='
		)
	)
));

get_header();

?><div class="content-area-wrap">

	<div class="content-area fullscreen">

		<div class="content-header">

			<div class="content-header-affix clearfix"><?php

				the_title( '<h1 class="content-title">', '</h1>' ); ?>

			</div>

		</div>

		<div class="content-wrap">

			<div class="featured-portfolio-slider royalSlider rsHelium" data-rs-settings="<?php echo esc_attr( json_encode( array( 'controlNavigation' => 'none', 'autoScaleSlider' => false, 'imageScaleMode' => 'fill' ) ) ) ?>">

				<?php
				
				global $post;
				$tmp_post = $post;

				foreach( $featured_posts as $post ) : setup_postdata( $post );

					$post_terms = wp_get_post_terms( get_the_ID(), get_object_taxonomies( get_post_type() ) );
					$post_terms = wp_list_pluck( $post_terms, 'name' );

				?><figure <?php post_class( 'entry-slide' ) ?>>

					<figcaption class="entry-slide-caption">

						<a href="<?php the_permalink() ?>" title="<?php the_title_attribute() ?>" class="entry-link">

							<?php the_title( '<h1 class="entry-title">', '</h1>' );

							if( $post_terms ): 

							?><p class="entry-meta"><?php echo join( ', ', $post_terms ); ?></p>
							<?php endif; ?>

						</a>

					</figcaption>

					<?php if( has_post_thumbnail() ) : ?>
					<a class="rsImg" href="<?php echo esc_url( wp_get_attachment_url( get_post_thumbnail_id() ) ) ?>"></a>
					<?php endif; ?>

				</figure>
				<?php endforeach;

				$post = $tmp_post;
				if( is_a( $post, 'WP_Post' ) ) {
					setup_postdata( $post );
				}
				?>

			</div>

		</div>

	</div>

</div>
<?php

get_footer();
endif;
