<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>

<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>

<body <?php body_class() ?> itemscope itemtype="http://schema.org/WebPage">

	<div class="back-to-top">
		<button class="btn btn-ui"><i class="fa fa-angle-up"></i></button>
	</div>

	<?php if( Youxi()->option->get( 'show_search' ) ):
	?>
	<div class="search-wrap"><?php

		?><div class="container">

			<div class="row">

				<div class="col-md-10 col-md-push-1">

					<div class="search-inner-wrap">

						<?php get_search_form(); ?>

					</div>

				</div>

			</div>

		</div>

	</div>
	<?php endif; ?>

	<div class="site-outer-wrap">

		<div class="site-wrap">

			<header class="header" itemscope itemtype="http://schema.org/WPHeader">

				<div class="header-content-wrap">

					<div class="header-content">

						<div class="header-content-top">

							<div class="header-links">
								<ul class="inline-list"><?php

									if( Youxi()->option->get( 'show_search' ) ):

									?><li class="ajax-search-link">
										<a href="#"><i class="fa fa-search"></i></a>
									</li><?php 

									endif;

									if( Youxi()->option->get( 'edd_show_cart' ) && class_exists( 'Easy_Digital_Downloads' ) ):

									?><li class="edd-shopping-cart">
										<a href="<?php echo esc_url( edd_get_checkout_uri() ); ?>">
											<i class="fa fa-shopping-cart"></i>
											<span class="header-links-tooltip"><?php echo esc_html( edd_get_cart_quantity() ); ?></span>
										</a>
									</li>
									<?php 

									endif;

								?></ul>
							</div>

							<div class="brand"><?php

								?><div class="site-logo">

									<a href="<?php echo esc_url( home_url() ); ?>"><?php 
										if( Youxi()->option->get( 'logo_image' ) ):
										?><img src="<?php echo esc_url( Youxi()->option->get( 'logo_image' ) ); ?>" alt="<?php bloginfo( 'name' ); ?>">
										<?php else:
										?><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/default-logo.png' ); ?>" alt="<?php bloginfo( 'name' ); ?>">
										<?php endif; ?>
									</a><?php

								?></div><?php

								?><div class="tagline"><?php bloginfo( 'description' ) ?></div><?php

							?></div>

							<button class="header-toggle btn btn-ui">
								<span><span></span></span>
							</button>

						</div>

						<div class="header-content-bottom">

							<nav class="main-nav" itemscope itemtype="http://schema.org/SiteNavigationElement">
								<?php wp_nav_menu(array(
									'theme_location' => 'main-menu', 
									'container' => false, 
									'fallback_cb' => 'helium_fallback_menu', 
									'walker' => class_exists( 'Helium_Walker_Nav_Menu' ) ? new Helium_Walker_Nav_Menu() : ''
								)) ?>
							</nav>

							<?php get_sidebar();

							if( Youxi()->option->get( 'copyright_text' ) ):
							?>
							<div class="header-copyright"><?php echo Youxi()->option->get( 'copyright_text' ); ?></div>
							<?php endif; ?>

						</div>

					</div>

				</div>

			</header>