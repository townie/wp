<div class="entry-meta">
	<p><time <?php omega_attr( 'entry-published' ); ?>><?php echo get_the_date(); ?></time>
	<span <?php omega_attr( 'entry-author' ); ?>><?php echo __('by ', 'sans-serif'); the_author_posts_link(); ?></span>	
	<?php echo omega_post_comments( ); ?>
	<?php edit_post_link( __('Edit', 'sans-serif'), ' | ' ); ?><p>
	<?php omega_post_terms( array( 'taxonomy' => 'category', 'text' => __( 'Posted in: %s', 'sans-serif' ) ) ); ?>
</div><!-- .entry-meta -->