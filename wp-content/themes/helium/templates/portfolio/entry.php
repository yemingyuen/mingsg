<?php

/* General metadata */
$general = wp_parse_args( $post->general, array(
	'url'        => '', 
	'client'     => '', 
	'client_url' => ''
));

/* Layout metadata */
$layout = wp_parse_args( $post->layout, array(
	'show_title'       => true, 
	'media_position'   => 'top', 
	'details_position' => 'left', 
	'details'          => array()
));

/* Validate layout positions */
if( ! preg_match( '/^top|(lef|righ)t$/', $layout['media_position'] ) ) {
	$layout['media_position'] = 'top';
}

if( ! preg_match( '/^hidden|(lef|righ)t$/', $layout['details_position'] ) ) {
	$layout['details_position'] = 'left';
}

/* Media metadata */
$media = wp_parse_args( $post->media, array(
	'type'         => 'featured-image', 
	'images'       => array(), 

	'video_type'   => '', 
	'video_embed'  => '', 
	'video_src'    => '', 
	'video_poster' => '', 

	'audio_type'   => '', 
	'audio_embed'  => '', 
	'audio_src'    => ''
));

/* Validate media type */
if( ! preg_match( '/^(featur|stack|justifi)ed(-(image|grids))?|slider|(vide|audi)o$/', $media['type'] ) ) {
	$media['type'] = 'featured-image';
}

?><article <?php post_class( array(
	'content-area', 
	$post->post_type . '-media-' . $layout['media_position'], 
	$post->post_type . '-media-' . $media['type'], 
	$post->post_type . '-details-' . $layout['details_position'], 
	$post->page_layout ) ); ?> itemscope itemtype="http://schema.org/Article">

	<header class="content-header">

		<div class="content-header-affix clearfix"><?php

			the_title( '<h1 class="entry-title content-title" itemprop="name">', '</h1>' );

				$prev_post_link = get_previous_post_link(
					'<li class="content-nav-link">%link</li>', 
					'<span class="content-nav-link-wrap">' . 
						'<i class="fa fa-chevron-left"></i>' . 
						'<span class="content-nav-link-label">' . esc_html__( 'Older', 'helium' ) . '</span>' . 
					'</span>'
				);

				$next_post_link = get_next_post_link(
					'<li class="content-nav-link">%link</li>', 
					'<span class="content-nav-link-wrap">' . 
						'<span class="content-nav-link-label">' . esc_html__( 'Newer', 'helium' ) . '</span>' . 
						'<i class="fa fa-chevron-right"></i>' . 
					'</span>'
				);

			?><nav class="content-nav" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">

				<ul class="plain-list"><?php
					
					if( $prev_post_link ):
						echo $prev_post_link;
					else:
						echo '<li class="content-nav-link disabled">';
							echo '<span>';
								echo '<span class="content-nav-link-wrap">';
									echo '<i class="fa fa-chevron-left"></i>';
									echo '<span class="content-nav-link-label">' . esc_html__( 'Older', 'helium' ) . '</span>';
								echo '</span>';
							echo '</span>';
						echo '</li>';
					endif;

					$archive_page = $post->archive_page ? $post->archive_page : 'default';
					if( 'default' == $archive_page ) {
						$archive_page = get_post_type_archive_link( youxi_portfolio_cpt_name() );
					} else {
						$archive_page = get_post( $archive_page );
						if( $archive_page && 'page' == $archive_page->post_type && 'archive-portfolio.php' == $archive_page->_wp_page_template ) {
							$archive_page = get_permalink( $archive_page );
						} else {
							$archive_page = get_post_type_archive_link( youxi_portfolio_cpt_name() );
						}
					}

					if( $archive_page ) {

						echo '<li class="content-nav-link">';
							echo '<a href="' . esc_url( $archive_page ) . '">';
								echo '<span class="content-nav-link-wrap">';
									echo '<i class="fa fa-th"></i>';
								echo '</span>';
							echo '</a>';
						echo '</li>';
					}

					if( $next_post_link ):
						echo $next_post_link;
					else:
						echo '<li class="content-nav-link disabled">';
							echo '<span>';
								echo '<span class="content-nav-link-wrap">';
									echo '<span class="content-nav-link-label">' . esc_html__( 'Newer', 'helium' ) . '</span>';
									echo '<i class="fa fa-chevron-right"></i>';
								echo '</span>';
							echo '</span>';
						echo '</li>';
					endif;
					
				?></ul>
			</nav>

		</div>

	</header>

	<div class="content-wrap">

		<div class="content-box clearfix">

			<?php if( has_post_thumbnail() ):

				$featured_content_class = 'featured-content';
				if( 'top' != $layout['media_position'] ) {
					$featured_content_class .= ' featured-content-' . $layout['media_position'];
				}

			?><div class="<?php echo esc_attr( $featured_content_class ) ?>">

				<?php if( 'featured-image' == $media['type'] ):

				?><figure class="featured-image">
					<?php the_post_thumbnail( 'full', array( 'itemprop' => 'image' ) ); ?>
				</figure>
				<?php elseif( 'video' == $media['type'] ):
				
					switch( $media['video_type'] ):

						case 'embed': ?>
						<div class="media">
							<?php global $wp_embed;
							if( is_a( $wp_embed, 'WP_Embed' ) ):
								echo $wp_embed->autoembed( $media['video_embed'] );
							else:
								echo $media['video_embed'];
							endif; ?>
						</div>
						<?php break;

						case 'hosted': ?>
						<div class="media">
							<?php
							// Check if the attachment is a video
							if( 0 === strpos( get_post_mime_type( $media['video_src'] ), 'video/' ) ):

								$meta = wp_get_attachment_metadata( $media['video_src'] );
								if( isset( $meta['width'], $meta['height'] ) ) {
									$video_ar = 100.0 * $meta['height'] / $meta['width'];
									printf( '<div class="wp-video-wrapper" style="padding-top: %s%%">', $video_ar );
								}

								echo wp_video_shortcode(array(
									'src' => wp_get_attachment_url( $media['video_src'] ), 
									'poster' => wp_get_attachment_url( $media['video_poster'] )
								));

								if( isset( $video_ar ) ) {
									echo '</div>';
								}
							endif; ?>
						</div>
						<?php break;

					endswitch;

				elseif( 'audio' == $media['type'] ):

					switch( $media['audio_type'] ):

						case 'embed':
							global $wp_embed;
							if( is_a( $wp_embed, 'WP_Embed' ) ):
								echo $wp_embed->autoembed( $media['audio_embed'] );
							else:
								echo $media['audio_embed'];
							endif;
							break;

						case 'hosted':
							// Check if the attachment is an audio
							if( 0 === strpos( get_post_mime_type( $media['audio_src'] ), 'audio/' ) ):
								echo wp_audio_shortcode(array(
									'src' => wp_get_attachment_url( $media['audio_src'] )
								));
							endif;
							break;
							
					endswitch;

				else:

					$attachments = array();
					foreach( $media['images'] as $image ):
						if( $attachment = wp_get_attachment_image_src( $image, 'full' ) ):
							$attachments[ $image ] = $attachment;
						endif;
					endforeach;

					switch( $media['type'] ):

						case 'slider':

							echo '<div class="royalSlider rsHelium" data-rs-settings="' . esc_attr( helium_rs_settings( $media ) ) . '">';
							foreach( $attachments as $id => $attachment ):
								echo wp_get_attachment_image( $id, 'full', false, array(
									'class' => 'attachment-full rsImg', 
									'data-rsw' => $attachment[1], 
									'data-rsh' => $attachment[2]
								));
							endforeach;
							echo '</div>';
							break;

						case 'justified-grids':
							echo '<div class="justified-grids">';
							foreach( $attachments as $id => $attachment ):
								echo wp_get_attachment_image( $id, 'full' );
							endforeach;
							echo '</div>';
							break;

						case 'stacked':
						default:
							foreach( $attachments as $id => $attachment ):
								echo wp_get_attachment_image( $id, 'full' );
							endforeach;
							break;

					endswitch;

				endif; ?>
			</div>
			<?php endif; ?>

			<div class="content-wrap-inner">

				<div class="container">

					<div class="row">

						<?php

						$show_details    = false;
						$content_class[] = 'entry-content';
						$sidebar_class[] = 'entry-sidebar';

						if( 'hidden' != $layout['details_position'] && 
							is_array( $layout['details'] ) && ! empty( $layout['details'] ) ):

							$show_details = true;

							if( 'top' == $layout['media_position'] ):
								$content_class[] = 'col-lg-9';
								$sidebar_class[] = 'col-lg-3';

								if( 'left' == $layout['details_position'] ):
									$content_class[] = 'col-lg-push-3';
									$sidebar_class[] = 'col-lg-pull-9';
								endif;
							else:
								$content_class[] = 'col-lg-12';
								$sidebar_class[] = 'col-lg-12';
							endif;
						else:
							$content_class[] = 'col-lg-12';
						endif;

						$content_class = implode( ' ', $content_class );
						$sidebar_class = implode( ' ', $sidebar_class );

						?><div class="<?php echo esc_attr( $content_class ); ?>">

							<?php if( (bool) $layout['show_title'] ):
								the_title( '<h2 class="no-margin-top">', '</h2>' );
							endif; ?>

							<div class="entry-content" itemprop="articleBody">
								<?php the_content(); ?>
							</div>

						</div>

						<?php if( $show_details ):

						?><div class="<?php echo esc_attr( $sidebar_class ); ?>">

							<ul class="entry-details plain-list">
							<?php foreach( $layout['details'] as $detail ):

								$value = '';
								$detail = wp_parse_args( $detail, array(
									'type'         => '', 
									'label'        => '', 
									'custom_value' => ''
								));

								switch( $detail['type'] ):
									case 'categories':
										$value = get_the_term_list( get_the_ID(), youxi_portfolio_tax_name(), '', ', ' );
										break;
									case 'url':
										if( ! empty( $general['url'] ) ):
											$value = '<a href="' . esc_url( $general['url'] ) . '" itemprop="url">' . esc_html( $general['url'] ) . '</a>';
										endif;
										break;
									case 'client':
										if( ! empty( $general['client'] ) ):
											if( '' !== $general['client_url'] ):
												$value = '<a href="' . esc_url( $general['client_url'] ) . '" title="' . esc_html( $general['client'] ) . '">';
											endif;
											$value .= esc_html( $general['client'] );
											if( '' !== $general['client_url'] ):
												$value .= '</a>';
											endif;
										endif;
										break;
									case 'share':
										$sharing_buttons = Youxi()->option->get( 'addthis_sharing_buttons' );
										$sharing_buttons = array_filter( array_map( 'trim', explode( ',', $sharing_buttons ) ) );
										if( ! empty( $sharing_buttons ) ):

											ob_start();
										?><div class="addthis_toolbox addthis_default_style addthis_20x20_style" addthis:url="<?php the_permalink() ?>" addthis:title="<?php the_title_attribute() ?>">
											<?php array_walk( $sharing_buttons, 'helium_sharing_button' ); ?>
										</div>
										<?php
											$value = ob_get_clean();
										endif;
										break;
									case 'custom':
										if( ! empty( $detail['custom_value'] ) ):
											$value = $detail['custom_value'];
										endif;
										break;
								endswitch;

								if( ! empty( $value ) ):

							?><li>
								<h5 class="entry-detail-label"><?php echo esc_html( $detail['label'] ); ?></h5>
								<span class="entry-detail-value"><?php echo $value ?></span>
							</li>
							<?php
								endif;
							endforeach;

							?></ul>

						</div>
						<?php endif; ?>

					</div>

				</div>

			</div>

		</div>

		<?php if( Youxi()->option->get( 'portfolio_show_related_items' ) ):
			Youxi()->templates->get( 'related', null, get_post_type() );
		endif; ?>

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
