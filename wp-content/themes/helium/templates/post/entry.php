<article <?php post_class(); ?> itemscope itemtype="http://schema.org/Article">

	<div class="content-box clearfix">

		<?php Youxi()->templates->get( 'parts/header', get_post_format(), get_post_type() ); ?>
		<?php Youxi()->templates->get( 'media/media', get_post_format(), get_post_type() ); ?>

		<div class="post-entry">

			<div class="content-wrap-inner">

				<div class="container">

					<div class="row">

						<div class="col-lg-12">

							<section class="entry-content post-body" itemprop="articleBody"><?php

								if( ! is_single() && 'the_excerpt' === Youxi()->option->get( 'blog_summary' ) ):
									echo apply_filters( 'the_excerpt', Youxi()->entries->get_excerpt( Youxi()->option->get( 'blog_excerpt_length' ) ) );

								?><div class="more-link-wrap">
									<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark" class="more-link"><?php esc_html_e( 'Continue Reading &rarr;', 'helium' ); ?></a>
								</div><?php

								else:
									the_content( esc_html__( 'Continue Reading &rarr;', 'helium' ) );
								endif;

							?></section>

							<?php if( is_single() ):

								wp_link_pages(array(
									'before' => '<section class="posts-pages-nav"><nav class="pages-nav"><ul class="inline-list">', 
									'after' => '</ul></nav></section>', 
									'separator' => '', 
									'pagelink' => '<span class="pages-nav-item">%</span>'
								));

								if( Youxi()->option->get( 'blog_show_tags' ) && get_the_tags() )  :

								?><section class="post-tags"><?php the_tags( '', '' ); ?></section>

								<?php endif;

								if( Youxi()->option->get( 'blog_sharing' ) ):
									Youxi()->templates->get( 'parts/sharing', get_post_format(), get_post_type() );
								endif;

								if( Youxi()->option->get( 'blog_show_author' ) ):
									Youxi()->templates->get( 'parts/author', get_post_format() , get_post_type());
								endif;

								if( Youxi()->option->get( 'blog_related_posts' ) ):
									Youxi()->templates->get( 'parts/related', get_post_format(), get_post_type() );
								endif;

								if( have_comments() || comments_open() ):
									comments_template();
								endif;

							endif;
							?>

						</div>

					</div>

				</div>

			</div>

		</div>

	</div>

</article>
