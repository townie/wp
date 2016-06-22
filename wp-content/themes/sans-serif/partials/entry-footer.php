<footer class="entry-footer">
	<div class="entry-meta">		
		<?php if (is_singular('post')) omega_post_terms( array( 'taxonomy' => 'post_tag', 'text' => __( 'Tagged: %s', 'sans-serif' ), 'before' => '' ) ); ?>
	</div><!-- .entry-meta -->
</footer>