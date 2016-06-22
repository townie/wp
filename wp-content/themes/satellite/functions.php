<?php
/**
 *
 * @package Satellite
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 1000; /* pixels */
}

/**
 * Enqueue scripts and styles.
 */
function satellite_parent_styles() {
	wp_enqueue_style( 'satellite-parent-style', get_template_directory_uri() . '/style.css' );

}
add_action( 'wp_enqueue_scripts', 'satellite_parent_styles', 1 );

function satellite_scripts() {
	wp_enqueue_style( 'satellite-style', get_stylesheet_uri() );
	wp_enqueue_style( 'satellite-fonts', satellite_fonts_url(), array(), null );

	wp_dequeue_script( 'scrawl-script' );
	wp_enqueue_script( 'satellite-script', get_stylesheet_directory_uri() . '/js/satellite.js', array( 'jquery' ), '20150312', true );

}
add_action( 'wp_enqueue_scripts', 'satellite_scripts', 99 );

/*
 * Adjust default background color
 */
function satellite_background_color( $args ) {
	$args['default-color'] = 'f5f7f7';
	
	return $args;
}
add_filter( 'scrawl_custom_background_args', 'satellite_background_color' );


/**
 * Register Google Fonts
 */
function satellite_fonts_url() {
    $fonts_url = '';

    /* Translators: If there are characters in your language that are not
	 * supported by Lato, translate this to 'off'. Do not translate
	 * into your own language.
	 */
	$lato = _x( 'on', 'Lato font: on or off', 'satellite' );

	/* Translators: If there are characters in your language that are not
	 * supported by Montserrat, translate this to 'off'. Do not translate
	 * into your own language.

	$montserrat = _x( 'on', 'Montserrat font: on or off', 'satellite' );*/

	if ( 'off' !== $lato ) {
		$font_families = array();

		$font_families[] = 'Lato:400,400italic,700,700italic,900,900italic';

		$query_args = array(
			'family' => urlencode( implode( '|', $font_families ) ),
			'subset' => urlencode( 'latin,latin-ext' ),
		);

		$fonts_url = add_query_arg( $query_args, '//fonts.googleapis.com/css' );
	}

	return $fonts_url;

}

/**
 * Enqueue Google Fonts for Editor Styles
 */
function satellite_editor_styles() {
    add_editor_style( array( 'editor-style.css', satellite_fonts_url() ) );
}
add_action( 'after_setup_theme', 'satellite_editor_styles' );

/**
 * Enqueue Google Fonts for custom headers
 */
function satellite_admin_scripts( $hook_suffix ) {

	wp_enqueue_style( 'satellite-fonts', satellite_fonts_url(), array(), null );

}
add_action( 'admin_print_styles-appearance_page_custom-header', 'satellite_admin_scripts' );

function satellite_posted_on() {
	$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string .= '<time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	printf( __( '<span class="posted-on">Posted on %1$s</span><span class="byline"> by %2$s</span>', 'satellite' ),
		sprintf( '<a href="%1$s" rel="bookmark">%2$s</a>',
			esc_url( get_permalink() ),
			$time_string
		),
		sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s">%2$s</a></span>',
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_html( get_the_author() )
		)
	);
}

function satellite_categories() {
	if ( ! scrawl_categorized_blog() )
		return;

	//Translators: Used between list items, there is a space after the comma
	echo '<div class="entry-categories">' . get_the_category_list( __( ', ', 'satellite' ) ) . '</div>';

}

/*
 * Allow .bypostauthor Author flag to be made available for translation
 */
function satellite_bypostauthor_flag() {
	if ( ! get_comments_number() && is_archive() || is_home() || is_search() )
		return; ?>

	<style type="text/css" id="bypostauthor-flag">
		.bypostauthor > .comment-body .comment-author .fn:after {
			content: "<?php _ex( 'Author', 'post author', 'satellite' ); ?>";
		}
	</style>

<?php }
add_action( 'wp_enqueue_scripts', 'satellite_bypostauthor_flag', 999 );


/* Overrides parent function of the same name
 * to adjust styles and support featured header images 
 * on pages as well as single posts */

function scrawl_featured_header() {
	if ( ! has_post_thumbnail() || is_archive() || is_home() || is_search() )
		return;

	$img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'scrawl-featured-header' );
	?>
	<style type="text/css" id="scrawl-featured-header">
		.featured-header-image {
			background-image: url( <?php echo esc_url( $img[0] ); ?> );
			background-size: cover;
			background-position: center;
			position: relative;
			width: 100%;
			height: 40%;
			z-index: 0;
		}
		.entry-title {
			color: white;
			margin-top: 0;
			margin-bottom: 0;
			padding: .75em .75em .75em 0;
			position: relative;
				left: 0;
				top: 50%;
				bottom: 0;
			text-shadow: 0px 2px 5px rgba(0,0,0,0.5);
			-webkit-transform: translateY(-50%);
					transform: translateY(-50%);
			z-index: 1;
		}
		@media screen and ( min-width: 58em ) {
			.entry-title {
				position: absolute;
					top: auto;
					bottom: .75em;
				-webkit-transform: translateY(0);
						transform: translateY(0);
				width: 20em;
			}
			.featured-header-image {
				height: 90%;
			}
		}
		.single.has-thumbnail .entry-title a,
		.page.has-thumbnail .entry-title a {
			color: white;
		}
	</style>
	<?php
}
add_action( 'wp_head', 'scrawl_featured_header', 99 );

/**
 * Implement the Custom Header feature.
 */
require get_stylesheet_directory() . '/inc/custom-header.php';
