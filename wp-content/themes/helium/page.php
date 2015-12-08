<?php

get_header();

if( have_posts() ): the_post();

$layout = wp_parse_args( $post->layout, array(
	'page_layout'  => 'boxed', 
	'wrap_content' => true
));

?><div class="content-area-wrap">

	<article <?php post_class( array( 'content-area', $layout['page_layout'] ) ); ?>>

		<header class="content-header">

			<div class="content-header-affix clearfix"><?php

				the_title( '<h1 class="entry-title content-title">', '</h1>' );
				?>

			</div>

		</header>

		<div class="content-wrap">

			<div class="content-box clearfix">

				<?php if( has_post_thumbnail() ):

				?><div class="featured-content">
					<figure class="featured-image">
						<?php the_post_thumbnail( 'full' ); ?>
					</figure>
				</div>
				<?php endif;

				if( $layout['wrap_content'] ):

				?><div class="content-wrap-inner">

					<div class="container">

						<div class="row">

							<div class="col-lg-12">
							<?php endif; ?>

								<div class="entry-content">
									<?php the_content(); ?>
								</div>

								<?php wp_link_pages(array(
									'before' => '<nav class="pages-nav"><ul class="inline-list">', 
									'after' => '</ul></nav>', 
									'separator' => '', 
									'pagelink' => '<span class="pages-nav-item">%</span>'
								));

							if( $layout['wrap_content'] ):

							?></div>

						</div>

					</div>

				</div>
				<?php endif; ?>

			</div>

			<?php if( have_comments() || comments_open() ): ?>
			<div class="content-box clearfix">

				<div class="entry-comments-wrap">

					<div class="content-wrap-inner three-quarters-vertical-padding">

						<div class="container">

							<div class="row">

								<div class="col-lg-12">

									<?php comments_template(); ?>

								</div>

							</div>

						</div>

					</div>

				</div>

			</div>
			<?php endif; ?>

		</div>

	</article>
</div>
<?php
endif;

get_footer(); ?>