<?php get_header(); ?>


<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<div class="article" <?php post_class(); ?>>
    <?php if ( is_sticky() ) : ?><div class="post-title"><?php _e( 'Featured', 'simplestyle' ); ?></div><?php endif; ?>
	<h1><a href="<?php esc_url( the_permalink() ); ?>"><?php the_title(); ?></a></h1>
	<div class="date"><?php the_date(); ?></div>
	
	<?php the_content( __( 'read more ...', 'simplestyle' ) ); ?>
	
	<div class="clear"></div>
	
	 <?php wp_link_pages('before=<div class="tags"><span class="tags">Pages</span><span class="link-pages">&after=</span></div>'); ?>
	
	<div id="comments"><?php comments_template('', true); ?> </div>
			
	</div>
	<div id="border-bottom"></div>
	
	
	<?php endwhile; ?> 
	<?php endif; ?>
	
		
	<?php get_footer(); ?>