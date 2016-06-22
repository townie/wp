<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package Satellite
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( ! has_post_thumbnail() ) : ?>
		<header class="entry-header">
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
		</header><!-- .entry-header -->
	<?php endif; ?>

	<div class="content-wrapper">
		<div class="entry-content">
			<?php the_content(); ?>
			<?php
				wp_link_pages( array(
					'before'      => '<div class="page-links">' . __( 'Pages:', 'satellite' ),
					'after'       => '</div>',
					'link_before' => '<span class="active-link">',
					'link_after'  => '</span>',
				) );
			?>
		</div><!-- .entry-content -->
		<footer class="entry-meta clear">
			<?php edit_post_link( '<span class="screen-reader-text">' . __( 'Edit', 'satellite' ) . '</span>', '<span class="edit-link">', '</span>' ); ?>
		</footer><!-- .entry-footer -->
	</div>
</article><!-- #post-## -->