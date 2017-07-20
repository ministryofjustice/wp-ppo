<?php

/**
 * Enqueue scripts and stylesheets
 *
 * Enqueue stylesheets in the following order:
 * 1. /theme/assets/css/main.min.css
 *
 * Enqueue scripts in the following order:
 * 1. jquery-1.10.2.min.js via Google CDN
 * 2. /theme/assets/js/vendor/modernizr-2.7.0.min.js
 * 3. /theme/assets/js/main.min.js (in footer)
 */
function roots_scripts() {
	global $wp_styles;
	
	wp_enqueue_style( 'roots_main', get_template_directory_uri() . '/assets/css/main.min.css', false, '6fdb1bb53650e8bc58715fec12c7e865' );
//	wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/assets/css/font-awesome.min.css', false);
//	$wp_styles->add_data( 'font-awesome', 'conditional', 'gt ie8' );

//	wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/assets/css/font-awesome.min3.2.1.css', false);
	wp_enqueue_style( 'fontello', get_template_directory_uri() . '/assets/fonts/fontello/css/fontello.css', false);

	// jQueryUI theme
	wp_enqueue_style( "jquery-ui-css", get_template_directory_uri() . "/assets/css/jquery-ui.min.css" );

	wp_enqueue_style( 'fontello-ie7', get_template_directory_uri() . '/assets/fonts/fontello/css/fontello-ie7.css', array('roots_main' ) );
	$wp_styles->add_data( 'fontello-ie7', 'conditional', 'lt IE 8' );

	wp_enqueue_style( 'ie7', get_template_directory_uri() . '/assets/css/ie7.css', array('roots_main' ) );
	$wp_styles->add_data( 'ie7', 'conditional', 'lt IE 8' );
	
	wp_enqueue_style( 'ie7and8', get_template_directory_uri() . '/assets/css/ie7and8.css', array('roots_main'), '6fdb1bb53650e8bc58715fec12c7e865' );
	$wp_styles->add_data( 'ie7and8', 'conditional', 'lt IE 9' );

	wp_enqueue_style( 'old-ie', get_stylesheet_directory_uri() . "/assets/css/old-ie.css", array( 'roots_main' ) );
    $wp_styles->add_data( 'old-ie', 'conditional', 'lt IE 10' );

	// jQuery is loaded using the same method from HTML5 Boilerplate:
	// Grab Google CDN's latest jQuery with a protocol relative URL; fallback to local if offline
	// It's kept in the header instead of footer to avoid conflicts with plugins.
	if ( !is_admin() && current_theme_supports( 'jquery-cdn' ) ) {
		wp_deregister_script( 'jquery' );
		wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js', array(), null, false );
		add_filter( 'script_loader_src', 'roots_jquery_local_fallback', 10, 2 );
	}

	if ( is_single() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	wp_register_script( 'modernizr', get_template_directory_uri() . '/assets/js/vendor/modernizr-2.7.0.min.js', array(), null, false );
	wp_register_script( 'roots_scripts', get_template_directory_uri() . '/assets/js/scripts.min.js', array(), '0c0d8395ef91a5c0c4f9efa53c367f91', true );
	wp_enqueue_script( 'modernizr' );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'roots_scripts' );
	// jQueryUI Accordion
	wp_enqueue_script( 'jquery-ui-accordion' );
	// jQueryUI Autocomplete
	wp_enqueue_script( 'jquery-ui-autocomplete' );
}

add_action( 'wp_enqueue_scripts', 'roots_scripts', 100 );

// http://wordpress.stackexchange.com/a/12450
function roots_jquery_local_fallback( $src, $handle = null ) {
	static $add_jquery_fallback = false;

	if ( $add_jquery_fallback ) {
		echo '<script>window.jQuery || document.write(\'<script src="' . get_template_directory_uri() . '/assets/js/vendor/jquery-1.10.2.min.js"><\/script>\')</script>' . "\n";
		$add_jquery_fallback = false;
	}

	if ( $handle === 'jquery' ) {
		$add_jquery_fallback = true;
	}

	return $src;
}

add_action( 'wp_head', 'roots_jquery_local_fallback' );

function roots_google_analytics() {
	?>
	<script>
		(function(b, o, i, l, e, r) {
			b.GoogleAnalyticsObject = l;
			b[l] || (b[l] =
					function() {
						(b[l].q = b[l].q || []).push(arguments)
					});
			b[l].l = +new Date;
			e = o.createElement(i);
			r = o.getElementsByTagName(i)[0];
			e.src = '//www.google-analytics.com/analytics.js';
			r.parentNode.insertBefore(e, r)
		}(window, document, 'script', 'ga'));
		ga('create', '<?php echo GOOGLE_ANALYTICS_ID; ?>');
		ga('send', 'pageview');
	</script>

	<?php
}

if ( GOOGLE_ANALYTICS_ID && !current_user_can( 'manage_options' ) ) {
	add_action( 'wp_footer', 'roots_google_analytics', 20 );
}