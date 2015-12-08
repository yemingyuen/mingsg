<?php get_header();

?><div class="content-area-wrap">

	<div class="content-area <?php echo esc_attr( Youxi()->option->get( 'blog_index_layout' ) ) ?>">

		<div class="content-header">

			<div class="content-header-affix clearfix"><?php

				?><h1 class="content-title">
					<?php echo Youxi()->option->get( 'blog_index_title' ); ?>
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