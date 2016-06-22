<?php

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 */

function sans_serif_theme_setup() {

	remove_theme_support( 'theme-layouts'); 

	remove_action( 'omega_before_header', 'omega_get_primary_menu' );
	add_action( 'omega_header', 'omega_get_primary_menu' );
	remove_action( 'omega_after_loop', 'omega_content_nav'); 
	add_action( 'omega_after_loop', 'sans_serif_content_nav');
	add_action( 'omega_after_loop', 'sans_serif_author_box');


	/* Register custom menus. */
	add_action( 'init', 'sans_serif_register_menu' );

	add_action( 'omega_footer', 'sans_serif_footer_links' );

	add_filter( 'omega_site_description', 'sans_serif_site_description' );

	add_action('init', 'sans_serif_init', 1);

	omega_set_content_width( 770 );
}

add_action( 'after_setup_theme', 'sans_serif_theme_setup', 11  );

function sans_serif_site_description($desc) {
	$desc = "";
	return $desc;
}

function sans_serif_author_box() {
	if ( is_singular('post') && get_the_author_meta( 'description' ) && is_multi_author() ) : // If a user has filled out their description and this is a multi-author blog, show a bio on their entries.			
		get_template_part( 'partials/about', 'author' );
	endif;
}

function sans_serif_init() {
	if(!is_admin()){
		wp_enqueue_script("tinynav", get_stylesheet_directory_uri() . '/js/tinynav.js', array('jquery'));
	} 
}

function sans_serif_register_menu() {
	register_nav_menu( 'social',   _x( 'Social', 'nav menu location', 'sans-serif' ) );
}

function sans_serif_content_nav() {
	$nav_class = ( is_single() ) ? 'post-navigation' : 'paging-navigation';
	$prev_link = get_previous_posts_link( '&larr; ' . __( 'Previous Page', 'sans-serif' ) );
	$next_link = get_next_posts_link( __( 'Next Page', 'sans-serif' ) . ' &rarr;' );
	$prev = $prev_link ? '<div class="nav-previous alignleft">' . $prev_link . '</div>' : '';
	$next = $next_link ? '<div class="nav-next alignright">' . $next_link . '</div>' : '';
	$nav = "<nav role='navigation' id='nav-below' class='navigation  $nav_class'>";
	$nav .= $prev;
	$nav .= $next;
	$nav .= '</nav><!-- #nav-below -->';

	if ( $prev || $next )
		echo $nav;

}



function sans_serif_footer_links() {

	if ( has_nav_menu( 'social' ) ) {
		wp_nav_menu(
			array(
				'theme_location'  => 'social',
				'container'       => 'div',
				'container_id'    => 'menu-social',
				'container_class' => 'menu',
				'menu_id'         => 'menu-social-items',
				'menu_class'      => 'menu-items',
				'depth'           => 1,
				'link_before'     => '<span class="screen-reader-text">',
				'link_after'      => '</span>',
				'fallback_cb'     => '',
			)
		);
	}
}

add_filter( 'comment_form_default_fields', 'sans_serif_comment_form_default_fields' );

function sans_serif_comment_form_default_fields() {
	$commenter = wp_get_current_commenter();
    $req = get_option( 'require_name_email' );
    $aria_req = ( $req ? " aria-required='true'" : '' );
    $fields['author'] =
        __( '<span class="comment-form-author">
            <input required minlength="3" maxlength="30" placeholder="Name *" id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) .
    '" size="30"' . $aria_req . ' />
        </span>', 'sans-serif' );

    $fields['email'] =
        __( '<span class="comment-form-email">
            <input required placeholder="Email *" id="email" name="email" type="email" value="' . esc_attr(  $commenter['comment_author_email'] ) .
    		'" size="30"' . $aria_req . ' />
        </span>', 'sans-serif' );

    $fields['url'] =
        __( '<span class="comment-form-url">
            <input placeholder="Website" id="url" name="url" type="url" value="' . esc_attr( $commenter['comment_author_url'] ) .
		    '" size="30" />
        </span>', 'sans-serif' );

	return $fields;
}

add_filter( 'comment_form_defaults', 'sans_serif_custom_comment_form' );

function sans_serif_custom_comment_form($fields) {
    $fields['comment_notes_after'] = ''; //Removes Form Allowed Tags Box
    $fields['comment_notes_before'] = ''; 
    $fields['label_submit'] = __( 'Submit Comment', 'sans-serif' );
    $fields['title_reply'] = __( 'Leave a Comment', 'sans-serif' );
    $fields['comment_field'] = '<p class="comment-form-comment"><textarea placeholder="Comment" id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';
	return $fields;
}

function sans_serif_load_theme_textdomain() {
  load_child_theme_textdomain( 'sans-serif', get_stylesheet_directory() . '/languages' );
}

add_action( 'after_setup_theme', 'sans_serif_load_theme_textdomain' );