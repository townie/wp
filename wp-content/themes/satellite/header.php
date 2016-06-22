<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Satellite
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'satellite' ); ?></a>

<div class="sidebar-nav">
	<button class="menu-toggle x" title="<?php esc_attr_e( 'Menu', 'satellite' ); ?>">
		<span class="lines"></span>
		<span class="screen-reader-text"><?php _e( 'Primary Menu', 'satellite' ); ?></span>
	</button>
	<?php if ( has_nav_menu ( 'social' ) ) : ?>
		<?php wp_nav_menu( array( 'theme_location' => 'social', 'depth' => 1, 'link_before' => '<span class="screen-reader-text">', 'link_after' => '</span>', 'container_class' => 'social-links', ) ); ?>
	<?php endif; ?>
</div>
<div class="slide-menu">
	<?php if ( function_exists( 'jetpack_the_site_logo' ) && has_site_logo() ) {
			jetpack_the_site_logo();
		} elseif ( '' !== get_theme_mod( 'scrawl_gravatar_email', '' ) ) {
			scrawl_get_gravatar();
		}
	?>
	<h1 class="site-title">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
	</h1>
	<nav id="site-navigation" class="main-navigation" role="navigation">
		<?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
	</nav><!-- #site-navigation -->

	<?php if ( is_active_sidebar( 'sidebar-1' ) ) {
		get_sidebar();
	} ?>
</div><!-- .slide-menu -->
<div id="page" class="hfeed site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'satellite' ); ?></a>
	<?php // Single post header images */
		  if ( ( is_single() || is_page() ) && has_post_thumbnail() && ! post_password_required() ) : ?>
		<div class="featured-header-image">
			<?php the_title( '<h1 class="entry-title"><a id="scroll-to-content" href="#post-' . get_the_ID() . '">', '</a></h1>' ); ?>
		</div>
	<?php endif; ?>
	<div class="site-inner-wrapper">

		<header id="masthead" class="site-header" role="banner">

			<div class="site-branding">
				<?php if ( function_exists( 'jetpack_the_site_logo' ) && has_site_logo() ) {
						jetpack_the_site_logo();
					} elseif ( '' !== get_theme_mod( 'scrawl_gravatar_email', '' ) ) {
						scrawl_get_gravatar();
					}
				?>
				<h1 class="site-title">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
				</h1>
				<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
			</div>
		</header><!-- #masthead -->

		<?php if ( is_home() || is_front_page() && get_header_image() ) : ?>
			<div class="featured-header-image">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<img class="custom-header" src="<?php header_image(); ?>" width="<?php echo get_custom_header()->width; ?>" height="<?php echo get_custom_header()->height; ?>" alt="">
				</a>
			</div>
		<?php endif; ?>

		<div id="content" class="site-content">

