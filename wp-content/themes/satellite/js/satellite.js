( function( $ ) {

	var menuToggle = $( '.menu-toggle' );
	var slideMenu = $( '.slide-menu' );
	var body = $( 'body' );
	
	var toggleAria = function( selector, property ) {
		if ( 'false' === selector.attr( property ) ) {
			selector.attr( property, 'true' );
		}
		else {
			selector.attr( property, 'false' );
		}
	};
	
	slideMenu.attr( 'aria-hidden', 'true' );
	menuToggle.attr( 'aria-expanded', 'false' );
	
	/*
	 * Toggle slide menu
	 */
	function slideControl() {
		menuToggle.on( 'click', function( e ) {
			e.preventDefault();
			slideMenu.toggleClass( 'expanded' ).resize();
			body.toggleClass( 'sidebar-open' );
			$( this ).toggleClass( 'toggle-on' );
			toggleAria( slideMenu, 'aria-hidden' );
			toggleAria( menuToggle, 'aria-expanded' );

			//Close slide menu with double click
			body.dblclick( function( e ) {
				e.preventDefault();
				slideMenu.removeClass( 'expanded' ).resize();
				$( this ).removeClass( 'sidebar-open' );
				menuToggle.removeClass( 'toggle-on' );
				toggleAria( slideMenu, 'aria-hidden' );
				toggleAria( menuToggle, 'aria-expanded' );
			} );
		} );
	}

	/*
	 * Close slide menu with escape key
	 */
	$( document ).keyup( function( e ) {
		if ( e.keyCode === 27 && slideMenu.hasClass( 'expanded' ) ) {
			body.removeClass( 'sidebar-open' );
			menuToggle.removeClass( 'toggle-on' );
			slideMenu.removeClass( 'expanded' ).resize();
			toggleAria( slideMenu, 'aria-hidden' );
			toggleAria( menuToggle, 'aria-expanded' );
		}
	} );

	/* Remove :after pseudo-element from linked images */
	function linkedImages() {
		var imgs = $( '.entry-content img' );

		for ( var i = 0, imgslength = imgs.length; i < imgslength; i++ ) {
			if ( '' !== $( imgs[i] ).closest( 'a' ) ) {
				$( imgs[i] ).closest( 'a' ).addClass( 'no-line' );
			}
		}
	}

	/* Scroll to content on single posts */
	function scrollToContent() {
		$( '#scroll-to-content' ).click( function(e) {

			e.preventDefault();

			var link = $( this ).attr( 'href' );

			body.animate({
		        scrollTop: $( link ).offset().top - 45
		    }, 700 );
		});
	}

	/* Highlight labels in comment reply form on focus */
	function formLabels() {

		$( '.comment-form input, .comment-form textarea' ).focus(
			function(e) {
				e.preventDefault();
				$( this ).prev( 'label' ).addClass( 'label-focus' );
			}
		).focusout(
			function(e) {
				e.preventDefault();
				$( this ).prev( 'label' ).removeClass( 'label-focus' );
			}
		);

	}

	// After DOM is ready
	$( document ).ready( function() {
		slideControl();
	} );

	// After window loads
	$( window ).load( function() {
		linkedImages();
		formLabels();
		scrollToContent();
	} );

	// After window is resized or infinite scroll loads new posts
	$( window ).on( 'resize post-load', function() {
		linkedImages();
	} );

} )( jQuery );
