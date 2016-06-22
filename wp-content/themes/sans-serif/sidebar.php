<?php
if ( is_active_sidebar( 'primary' ) && (is_singular('page') && !is_page_template('page-full-width.php') )) : ?>	

	<aside class="sidebar sidebar-primary widget-area" <?php omega_attr( 'sidebar' ); ?>>

		<?php dynamic_sidebar( 'primary' ); ?>

  	</aside><!-- .sidebar -->

<?php endif;  ?>

	