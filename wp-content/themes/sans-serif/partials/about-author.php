<section class="author-box" itemtype="http://schema.org/Person" itemscope="itemscope" itemprop="author">		
	<?php echo get_avatar( get_the_author_meta( 'user_email' ), 80 ); ?>
	<div class="author-description">
		<h2 class="author-box-title"><?php printf( __( 'About %s', 'sans-serif' ), get_the_author() ); ?></h2>
		<div class="author-box-content" itemprop="description">
			<?php the_author_meta( 'description' ); ?>
		</div>
	</div><!-- .author-description -->
</section><!-- .author-box -->
