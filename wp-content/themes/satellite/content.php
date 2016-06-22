<?php
/**
 * @package Satellite
 */
$formats = get_theme_support( 'post-formats' );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php satellite_categories(); ?>
		<?php if ( 'link' == get_post_format() ) : ?>
			<?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( scrawl_get_link_url() ) ), '</a></h1>' ); ?>
		<?php else : ?>
			<?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>
		<?php endif; ?>
		<span class="entry-meta"><?php scrawl_posted_on(); ?></span>
	</header><!-- .entry-header -->
	<div class="content-wrapper">
		<?php if ( is_search() ) : // Only display Excerpts for Search ?>
			<div class="entry-summary">
				<?php the_excerpt(); ?>
			</div><!-- .entry-summary -->
		<?php else : ?>
			<div class="entry-content">
				<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'satellite' ) ); ?>
				<?php
					wp_link_pages( array(
						'before'      => '<div class="page-links">' . __( 'Pages:', 'satellite' ),
						'after'       => '</div>',
						'link_before' => '<span class="active-link">',
						'link_after'  => '</span>',
					) );
				?>
			</div><!-- .entry-content -->
		<?php endif; ?>
		<?php if ( 'post' == get_post_type() ) : ?>
			<div class="entry-meta clear">
				<?php if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
					echo '<span class="comments-link">';
					comments_popup_link( __( '0', 'satellite' ), __( '1', 'satellite' ), __( '%', 'satellite' ) );
					echo '</span>';
				} ?>
				<?php if ( has_post_format( $formats[0] ) ) : ?>
					<?php scrawl_post_format(); ?>
				<?php endif; ?>
				<?php edit_post_link( '<span class="screen-reader-text">' . __( 'Edit', 'satellite' ) . '</span>', '<span class="edit-link">', '</span>' ); ?>
			</div><!-- .entry-meta -->
		<?php endif; ?>
	</div>
</article><!-- #post-## -->
