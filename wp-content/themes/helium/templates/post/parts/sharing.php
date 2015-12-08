<?php
$sharing_buttons = Youxi()->option->get( 'addthis_sharing_buttons' );
$sharing_buttons = array_filter( array_map( 'trim', explode( ',', $sharing_buttons ) ) );

if( ! empty( $sharing_buttons ) ):

?><section class="post-sharing">

	<div class="addthis_toolbox addthis_default_style addthis_32x32_style" addthis:url="<?php the_permalink() ?>" addthis:title="<?php the_title_attribute() ?>">
		<?php array_walk( $sharing_buttons, 'helium_sharing_button' ); ?>
	</div>

</section>
<?php endif;
