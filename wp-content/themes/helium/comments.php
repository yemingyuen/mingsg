<?php
/*
 * If the current post is protected by a password and the visitor has not yet
 * entered the password we will return early without loading the comments.
 */
if( post_password_required() ) {
	return;
}

if( have_comments() ):

?><section id="comments" class="entry-comments">

	<h3 class="post-section-title h4"><?php
		if( get_comments_number() > 0 ):
			printf( _n( 'One comment on &ldquo;%2$s&rdquo;', '%1$s comments on &ldquo;%2$s&rdquo;', get_comments_number(), 'helium' ),
				number_format_i18n( get_comments_number() ), get_the_title() );
		else:
			printf( esc_html__( 'No comment on &ldquo;%2$s&rdquo;', 'helium' ), get_the_title() );
		endif;
	?></h3>

	<ul class="comment-list">
		<?php wp_list_comments( array( 'callback' => 'helium_comment' )); ?>
	</ul>

	<?php if( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : 
	?><nav class="entry-comments-nav clearfix">
		<div class="nav-previous"><?php previous_comments_link( esc_html__( '&larr; Older Comments', 'helium' ) ); ?></div>
		<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments &rarr;', 'helium' ) ); ?></div>
	</nav>
	<?php endif; ?>

	<?php if( ! comments_open() ):
	?><div class="alert alert-warning">
		<?php esc_html_e( 'Comments are closed for this post.', 'helium' ); ?>
	</div>
	<?php endif; ?>

</section>
<?php endif;

?><section class="entry-comments-form">
	<?php comment_form(); ?>
</section>