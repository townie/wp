<?php
/**
 * Template Name: Full-width, No Sidebar
 *
 */

get_header(); ?>

	<main  class="content" <?php omega_attr( 'content' ); ?>>

		<?php 
		do_action( 'omega_before_content' ); 

		do_action( 'omega_content' ); 

		do_action( 'omega_after_content' ); 
		?>

	</main><!-- .content -->

<?php get_footer(); ?>
