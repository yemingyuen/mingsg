<?php get_header(); ?>

<div class="content-area-wrap">

	<?php if( have_posts() ): the_post();
		Youxi()->templates->get( 'entry', null, get_post_type() );
	endif; ?>

</div>

<?php get_footer(); ?>