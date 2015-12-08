<?php get_header();

?><div class="content-area-wrap">

	<div class="content-area <?php echo esc_attr( Youxi()->option->get( 'blog_archive_layout' ) ) ?>">

		<div class="content-header">

			<div class="content-header-affix clearfix"><?php

					$the_title = '';

					if( is_category() ):
						$strtr = array( '{category}' => single_cat_title( '', false ));
						$ot_prefix = 'blog_category';
					elseif( is_tag() ):
						$strtr = array( '{tag}' => single_tag_title( '', false ));
						$ot_prefix = 'blog_tag';
					elseif( is_author() ):
						$strtr = array( '{author}' => get_the_author() );
						$ot_prefix = 'blog_author';
					elseif( is_day() ):
						$strtr = array( '{date}' => get_the_date( 'F d, Y' ) );
						$ot_prefix = 'blog_date';
					elseif( is_month() ):
						$strtr = array( '{date}' => get_the_date( 'F, Y' ) );
						$ot_prefix = 'blog_date';
					elseif( is_year() ):
						$strtr = array( '{date}' => get_the_date( 'Y' ) );
						$ot_prefix = 'blog_date';
					endif;

					if( isset( $strtr, $ot_prefix ) ):
						$the_title = strtr( Youxi()->option->get( $ot_prefix . '_title' ), $strtr );
					else:
						$the_title = esc_html__( 'Archive', 'helium' );
					endif;

				?><h1 class="content-title">
					<?php echo $the_title; ?>
				</h1>

			</div>

		</div>

		<div class="content-wrap">

			<?php
			while( have_posts() ) : the_post();
				Youxi()->templates->get( 'entry', get_post_format(), get_post_type() );
			endwhile;

			helium_entry_pagination(); ?>

		</div>

	</div>

</div>
<?php get_footer(); ?>