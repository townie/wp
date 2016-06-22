<?php
/**
 * @package Satellite
 */

$formats = get_theme_support( 'post-formats' );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( ! has_post_thumbnail() ) : ?>
		<header class="entry-header">
			<?php satellite_categories(); ?>
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
			<span class="entry-meta"><?php scrawl_posted_on(); ?></span>
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

		<footer class="entry-footer">
			<?php
				$tags_list = get_the_tag_list( '', '' );
				if ( $tags_list ) :
			?>
			<span class="tags-links clear">
				<?php echo $tags_list; ?>
			</span>
			<?php endif; // End if $tags_list ?>
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
		</footer><!-- .entry-footer -->
	</div><!-- .content-wrapper -->
</article><!-- #post-## -->
