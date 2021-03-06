<?php
/**
 * HTML attribute functions and filters.  The purposes of this is to provide a way for theme/plugin devs 
 * to hook into the attributes for specific HTML elements and create new or modify existing attributes.  
 * This is sort of like `body_class()`, `post_class()`, and `comment_class()` on steroids.  Plus, it 
 * handles attributes for many more elements.  The biggest benefit of using this is to provide richer 
 * microdata while being forward compatible with the ever-changing Web.  Currently, the default microdata 
 * vocabulary supported is Schema.org.
 */

/* Attributes for major structural elements. */
add_filter( 'omega_attr_body',    'omega_attr_body',    5    );
add_filter( 'omega_attr_header',  'omega_attr_header',  5    );
add_filter( 'omega_attr_footer',  'omega_attr_footer',  5    );
add_filter( 'omega_attr_content', 'omega_attr_content', 5    );
add_filter( 'omega_attr_sidebar', 'omega_attr_sidebar', 5, 2 );
add_filter( 'omega_attr_menu',    'omega_attr_menu',    5, 2 );

/* Header attributes. */
add_filter( 'omega_attr_site-title',       'omega_attr_site_title',       5 );
add_filter( 'omega_attr_site-description', 'omega_attr_site_description', 5 );

/* Loop attributes. */
add_filter( 'omega_attr_loop-meta',        'omega_attr_loop_meta',        5 );
add_filter( 'omega_attr_loop-title',       'omega_attr_loop_title',       5 );
add_filter( 'omega_attr_loop-description', 'omega_attr_loop_description', 5 );

/* Post-specific attributes. */
add_filter( 'omega_attr_post',            'omega_attr_post',            5    );
add_filter( 'omega_attr_entry',           'omega_attr_post',            5    ); // Alternate for "post".
add_filter( 'omega_attr_entry-title',     'omega_attr_entry_title',     5    );
add_filter( 'omega_attr_entry-author',    'omega_attr_entry_author',    5    );
add_filter( 'omega_attr_entry-published', 'omega_attr_entry_published', 5    );
add_filter( 'omega_attr_entry-content',   'omega_attr_entry_content',   5    );
add_filter( 'omega_attr_entry-summary',   'omega_attr_entry_summary',   5    );
add_filter( 'omega_attr_entry-terms',     'omega_attr_entry_terms',     5, 2 );

/* Comment specific attributes. */
add_filter( 'omega_attr_comment',           'omega_attr_comment',           5 );
add_filter( 'omega_attr_comment-author',    'omega_attr_comment_author',    5 );
add_filter( 'omega_attr_comment-published', 'omega_attr_comment_published', 5 );
add_filter( 'omega_attr_comment-permalink', 'omega_attr_comment_permalink', 5 );
add_filter( 'omega_attr_comment-content',   'omega_attr_comment_content',   5 );

/**
 * Outputs an HTML element's attributes.
 *
 * @since  0.9.0
 * @access public
 * @param  string  $slug        The slug/ID of the element (e.g., 'sidebar').
 * @param  string  $context     A specific context (e.g., 'primary').
 * @param  array   $attributes  Custom attributes to pass in.
 * @return void
 */
function omega_attr( $slug, $context = '', $attributes = array() ) {
	echo omega_get_attr( $slug, $context, $attributes );
}

/**
 * Gets an HTML element's attributes.  This function is actually meant to be filtered by theme authors, plugins, 
 * or advanced child theme users.  The purpose is to allow folks to modify, remove, or add any attributes they 
 * want without having to edit every template file in the theme.  So, one could support microformats instead 
 * of microdata, if desired.
 *
 * @since  0.9.0
 * @access public
 * @param  string  $slug        The slug/ID of the element (e.g., 'sidebar').
 * @param  string  $context     A specific context (e.g., 'primary').
 * @param  array   $attributes  Custom attributes to pass in.
 * @return string
 */
function omega_get_attr( $slug, $context = '', $attributes = array() ) {

	$out    = '';
	$attr   = apply_filters( "omega_attr_{$slug}", $attributes, $context );

	if ( empty( $attr ) )
		$attr['class'] = $slug;

	foreach ( $attr as $name => $value )
		$out .= !empty( $value ) ? sprintf( ' %s="%s"', esc_html( $name ), esc_attr( $value ) ) : esc_html( " {$name}" );

	return trim( $out );
}

/* === Structural === */

/**
 * <body> element attributes.
 *
 * @since  0.9.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function omega_attr_body( $attr ) {

	$attr['dir']       = is_rtl() ? 'rtl' : 'ltr';
	$attr['itemscope'] = 'itemscope';
	$attr['itemtype']  = 'http://schema.org/WebPage';

	return $attr;
}

/**
 * Page <header> element attributes.
 *
 * @since  0.9.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function omega_attr_header( $attr ) {

	$attr['id']        = 'header';
	$attr['class']     = 'site-header';
	$attr['role']      = 'banner';
	$attr['itemscope'] = 'itemscope';
	$attr['itemtype']  = 'http://schema.org/WPHeader';

	return $attr;
}

/**
 * Page <footer> element attributes.
 *
 * @since  0.9.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function omega_attr_footer( $attr ) {

	$attr['id']        = 'footer';
	$attr['class']     = 'site-footer';
	$attr['role']      = 'contentinfo';
	$attr['itemscope'] = 'itemscope';
	$attr['itemtype']  = 'http://schema.org/WPFooter';

	return $attr;
}

/**
 * Main content container of the page attributes.
 *
 * @since  0.9.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function omega_attr_content( $attr ) {

	$attr['id']       = 'content';
	$attr['role']     = 'main';
	$attr['itemprop'] = 'mainContentOfPage';

	if ( is_singular( 'post' ) || is_home() || is_archive() ) {
		$attr['itemscope'] = 'itemscope';
		$attr['itemtype']  = 'http://schema.org/Blog';
	}

	elseif ( is_search() ) {
		$attr['itemscope'] = 'itemscope';
		$attr['itemtype']  = 'http://schema.org/SearchResultsPage';
	}

	return $attr;
}

/**
 * Sidebar attributes.
 *
 * @since  0.9.0
 * @access public
 * @param  array   $attr
 * @param  string  $context
 * @return array
 */
function omega_attr_sidebar( $attr, $context ) {

	if ( !empty( $context ) )
		$attr['id'] = "sidebar-{$context}";

	$attr['role']      = 'complementary';
	$attr['itemscope'] = 'itemscope';
	$attr['itemtype']  = 'http://schema.org/WPSideBar';

	return $attr;
}

/**
 * Nav menu attributes.
 *
 * @since  0.9.0
 * @access public
 * @param  array   $attr
 * @param  string  $context
 * @return array
 */
function omega_attr_menu( $attr, $context ) {

	if ( !empty( $context ) )
		$attr['id'] = "menu-{$context}";

	$attr['role']      = 'navigation';
	$attr['itemscope'] = 'itemscope';
	$attr['itemtype']  = 'http://schema.org/SiteNavigationElement';

	return $attr;
}

/* === header === */

/**
 * Site title attributes.
 *
 * @since  0.9.0
 * @access public
 * @param  array   $attr
 * @param  string  $context
 * @return array
 */
function omega_attr_site_title( $attr ) {

	$attr['id']       = 'site-title';
	$attr['itemprop'] = 'headline';

	return $attr;
}

/**
 * Site description attributes.
 *
 * @since  0.9.0
 * @access public
 * @param  array   $attr
 * @param  string  $context
 * @return array
 */
function omega_attr_site_description( $attr ) {

	$attr['id']       = 'site-description';
	$attr['itemprop'] = 'description';

	return $attr;
}

/* === loop === */

/**
 * Loop meta attributes.
 *
 * @since  0.9.0
 * @access public
 * @param  array   $attr
 * @param  string  $context
 * @return array
 */
function omega_attr_loop_meta( $attr ) {

	$attr['class']     = 'loop-meta';
	$attr['itemscope'] = 'itemscope';
	$attr['itemtype']  = 'http://schema.org/WebPageElement';

	return $attr;
}

/**
 * Loop title attributes.
 *
 * @since  0.9.0
 * @access public
 * @param  array   $attr
 * @param  string  $context
 * @return array
 */
function omega_attr_loop_title( $attr ) {

	$attr['class']     = 'loop-title';
	$attr['itemprop']  = 'headline';

	return $attr;
}

/**
 * Loop description attributes.
 *
 * @since  0.9.0
 * @access public
 * @param  array   $attr
 * @param  string  $context
 * @return array
 */
function omega_attr_loop_description( $attr ) {

	$attr['class']     = 'loop-description';
	$attr['itemprop']  = 'text';

	return $attr;
}

/* === posts === */

/**
 * Post <article> element attributes.
 *
 * @since  0.9.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function omega_attr_post( $attr ) {

	$post = get_post();

	/* Make sure we have a real post first. */
	if ( !empty( $post ) ) {

		$attr['id']        = 'post-' . get_the_ID();
		$attr['class']     = join( ' ', get_post_class() );
		$attr['itemscope'] = 'itemscope';

		if ( 'post' === get_post_type() ) {

			$attr['itemtype']  = 'http://schema.org/BlogPosting';
			$attr['itemprop']  = 'blogPost';
		}

		elseif ( 'attachment' === get_post_type() && wp_attachment_is_image() ) {

			$attr['itemtype'] = 'http://schema.org/ImageObject';
		}

		elseif ( 'attachment' === get_post_type() && omega_attachment_is_audio() ) {

			$attr['itemtype'] = 'http://schema.org/AudioObject';
		}

		elseif ( 'attachment' === get_post_type() && omega_attachment_is_video() ) {

			$attr['itemtype'] = 'http://schema.org/VideoObject';
		}

		else {
			$attr['itemtype']  = 'http://schema.org/CreativeWork';
		}

	} else {

		$attr['id']    = 'post-0';
		$attr['class'] = join( ' ', get_post_class() );
	}

	return $attr;
}

/**
 * Post title attributes.
 *
 * @since  0.9.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function omega_attr_entry_title( $attr ) {

	$attr['class']    = 'entry-title';
	$attr['itemprop'] = 'headline';

	return $attr;
}

/**
 * Post author attributes.
 *
 * @since  0.9.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function omega_attr_entry_author( $attr ) {

	$attr['class']     = 'entry-author';
	$attr['itemprop']  = 'author';
	$attr['itemscope'] = 'itemscope';
	$attr['itemtype']  = 'http://schema.org/Person';

	return $attr;
}

/**
 * Post time/published attributes.
 *
 * @since  0.9.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function omega_attr_entry_published( $attr ) {

	$attr['class']    = 'entry-time';
	$attr['datetime'] = get_the_time( 'Y-m-d\TH:i:sP' );
	$attr['itemprop']  = 'datePublished';
	/* Translators: Post date/time "title" attribute. */
	$attr['title']    = get_the_time( _x( 'l, F j, Y, g:i a', 'post time format', 'omega' ) );

	return $attr;
}

/**
 * Post content (not excerpt) attributes.
 *
 * @since  0.9.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function omega_attr_entry_content( $attr ) {

	$attr['class']    = 'entry-content';	
	if ( 'post' === get_post_type() )
		$attr['itemprop'] = 'articleBody';
	else
		$attr['itemprop'] = 'text';

	return $attr;
}

/**
 * Post summary/excerpt attributes.
 *
 * @since  0.9.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function omega_attr_entry_summary( $attr ) {

	$attr['class']    = 'entry-summary';
	$attr['itemprop'] = 'description';

	return $attr;
}

/**
 * Post terms (tags, categories, etc.) attributes.
 *
 * @since  0.9.0
 * @access public
 * @param  array   $attr
 * @param  string  $context
 * @return array
 */
function omega_attr_entry_terms( $attr, $context ) {

	if ( !empty( $context ) ) {

		$attr['class'] = 'entry-terms ' . sanitize_html_class( $context );

		if ( 'category' === $context )
			$attr['itemprop'] = 'articleSection';

		else if ( 'post_tag' === $context )
			$attr['itemprop'] = 'keywords';
	}

	return $attr;
}


/* === Comment elements === */


/**
 * Comment wrapper attributes.
 *
 * @since  0.9.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function omega_attr_comment( $attr ) {

	//$attr['id']    = 'comment-' . get_comment_ID(); hence disabled
	//$attr['class'] = join( ' ', get_comment_class() );

	if ( in_array( get_comment_type(), array( '', 'comment' ) ) ) {
		$attr['class']     = 'comment-item';
		$attr['itemprop']  = 'comment';
		$attr['itemscope'] = 'itemscope';
		$attr['itemtype']  = 'http://schema.org/UserComments';
	}

	return $attr;
}

/**
 * Comment author attributes.
 *
 * @since  0.9.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function omega_attr_comment_author( $attr ) {

	$attr['class']     = 'comment-author';
	$attr['itemprop']  = 'creator';
	$attr['itemscope'] = 'itemscope';
	$attr['itemtype']  = 'http://schema.org/Person';

	return $attr;
}

/**
 * Comment time/published attributes.
 *
 * @since  0.9.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function omega_attr_comment_published( $attr ) {

	$attr['class']    = 'comment-published';
	$attr['datetime'] = get_comment_time( 'Y-m-d\TH:i:sP' );

	/* Translators: Comment date/time "title" attribute. */
	$attr['title']    = get_comment_time( _x( 'l, F j, Y, g:i a', 'comment time format', 'omega' ) );
	$attr['itemprop'] = 'commentTime';

	return $attr;
}

/**
 * Comment permalink attributes.
 *
 * @since  0.9.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function omega_attr_comment_permalink( $attr ) {

	$attr['class']    = 'comment-permalink';
	$attr['href']     = get_comment_link();
	$attr['itemprop'] = 'url';

	return $attr;
}

/**
 * Comment content/text attributes.
 *
 * @since  0.9.0
 * @access public
 * @param  array   $attr
 * @return array
 */
function omega_attr_comment_content( $attr ) {

	$attr['class']    = 'comment-content';
	$attr['itemprop'] = 'commentText';

	return $attr;
}