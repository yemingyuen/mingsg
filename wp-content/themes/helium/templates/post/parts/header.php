<header class="post-header">

	<div class="content-wrap-inner half-bottom-padding">

		<div class="container">

			<div class="row">

				<div class="col-lg-12">
					
					<time class="post-date updated" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>" itemprop="datePublished"><?php 
						echo esc_html( get_the_date( get_option( 'date_format' ) ) );
					?></time>

					<?php
						if( is_single() ):
							the_title( '<h2 class="entry-title post-title" itemprop="name">', '</h2>' );
						else:
							the_title( '<h2 class="entry-title post-title" itemprop="name"><a href="' . esc_url( get_permalink() ) . '" itemprop="url">', '</a></h2>' );
						endif;

						$hidden_post_meta = Youxi()->option->get( 'hidden_post_meta' );
						$hidden_post_meta = $hidden_post_meta ? $hidden_post_meta : array();

					?><div class="post-meta">

						<ul class="post-meta-list inline-list"><?php

							if( is_sticky() && ! is_single() ):

							?><li><i class="fa fa-paperclip"></i> <?php esc_html_e( 'Sticky', 'helium' ) ?></li>
							<?php endif;

							if( ! in_array( 'author', $hidden_post_meta ) ):

							?><li><i class="slicon slicon-user"></i> <span class="author vcard"><span class="fn"><?php the_author_posts_link(); ?></span></span></li>
							<?php endif;

							if( ! in_array( 'category', $hidden_post_meta ) && get_the_category() ):

							?><li><i class="slicon slicon-drawer"></i> <?php the_category( ', ' ); ?></li>
							<?php endif;

							if( ! in_array( 'tags', $hidden_post_meta ) && get_the_tags() ):

							?><li><i class="slicon slicon-tag"></i> <?php the_tags( '' ); ?></li>
							<?php endif;

							if( ! in_array( 'comments', $hidden_post_meta ) ):

							?><li><i class="slicon slicon-bubbles"></i> <a href="<?php comments_link() ?>" itemprop="discussionUrl"><?php comments_number( 'no comments', 'one comment', '% comments' ); ?></a></li>
							<?php endif;

							if( ! in_array( 'permalink', $hidden_post_meta ) ):

							?><li><i class="slicon slicon-link"></i> <a href="<?php the_permalink(); ?>" itemprop="url"><?php esc_html_e( 'Permalink', 'helium' ) ?></a></li>
							<?php endif;

						?></ul>

					</div>

				</div>

			</div>

		</div>

	</div>

</header>