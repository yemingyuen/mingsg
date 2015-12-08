<section class="entry-author" itemprop="author" itemscope itemtype="http://schema.org/Person">

	<div class="clearfix">

		<figure class="entry-author-avatar">
			<?php echo get_avatar( get_the_author_meta( 'user_email' ), 320 ); ?>
		</figure>

		<div class="entry-author-info">
			<h4 class="entry-author-name" itemprop="name">
				<span class="author vcard"><span class="fn"><?php the_author() ?></span></span>
			</h4>

			<div class="entry-author-description" itemprop="description">
				<?php echo wpautop( get_the_author_meta( 'description' ) ); ?>
			</div>			

			<?php $profiles = '';

			if( get_the_author_meta( 'url' ) ):
				$profiles .= '<li><a href="' . esc_url( get_the_author_meta( 'url' ) ) . '" title="' . esc_attr( sprintf( esc_html__( "Visit %s&#8217;s website", 'helium' ), get_the_author()) ) . '" rel="author external" itemprop="url"><i class="fa fa-home"></i></a></li>';
			endif;

			foreach( helium_user_social_profiles() as $key => $profile ) {
				if( $url = get_the_author_meta( $key ) ) {
					$profiles .= '<li class="social-' . $key . '"><a href="' . esc_url( $url ) . '" title="' . $profile . '"><i class="socicon socicon-' . $key . '"></i></a></li>';
				}
			}
			if( $profiles ) {
				echo '<div class="social-list small"><ul>' . $profiles . '</ul></div>';
			}
			?>
		</div>

	</div>

</section>