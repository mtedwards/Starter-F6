<?php
/*********************
Start all the functions
at once
*********************/

// start all the functions
add_action('after_setup_theme','starter_startup');

function starter_startup() {

    // launching operation cleanup
    add_action('init', 'starter_head_cleanup');
    // remove WP version from RSS
    add_filter('the_generator', 'starter_rss_version');
    // remove pesky injected css for recent comments widget
    add_filter( 'wp_head', 'starter_remove_wp_widget_recent_comments_style', 1 );
    // clean up comment styles in the head
    add_action('wp_head', 'starter_remove_recent_comments_style', 1);
    // clean up gallery output in wp
    add_filter('gallery_style', 'starter_gallery_style');

    // enqueue base scripts and styles
    add_action('wp_enqueue_scripts', 'starter_scripts_and_styles', 999);
    // ie conditional wrapper
    add_filter( 'style_loader_tag', 'starter_ie_conditional', 10, 2 );
    
    // additional post related cleaning
    add_filter( 'img_caption_shortcode', 'starter_cleaner_caption', 10, 3 );
    add_filter('get_image_tag_class', 'starter_image_tag_class', 0, 4);
    add_filter('get_image_tag', 'starter_image_editor', 0, 4);

} /* end startup */


/**********************
WP_HEAD GOODNESS
The default WordPress head is
a mess. Let's clean it up.

Thanks for Bones
http://themble.com/bones/
**********************/

function starter_head_cleanup() {
	// category feeds
	 remove_action( 'wp_head', 'feed_links_extra', 3 );
	// post and comment feeds
	 remove_action( 'wp_head', 'feed_links', 2 );
	// EditURI link
	remove_action( 'wp_head', 'rsd_link' );
	// windows live writer
	remove_action( 'wp_head', 'wlwmanifest_link' );
	// index link
	remove_action( 'wp_head', 'index_rel_link' );
	// previous link
	remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
	// start link
	remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
	// links for adjacent posts
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
	// WP version
	remove_action( 'wp_head', 'wp_generator' );
  // remove WP version from css
  // add_filter( 'style_loader_src', 'starter_remove_wp_ver_css_js', 9999 );
  // remove Wp version from scripts
  // add_filter( 'script_loader_src', 'starter_remove_wp_ver_css_js', 9999 );

} /* end head cleanup */

// remove WP version from RSS
function starter_rss_version() { return ''; }

// remove WP version from scripts
function starter_remove_wp_ver_css_js( $src ) {
    if ( strpos( $src, 'ver=' ) )
        $src = remove_query_arg( 'ver', $src );
    return $src;
}

// remove injected CSS for recent comments widget
function starter_remove_wp_widget_recent_comments_style() {
   if ( has_filter('wp_head', 'wp_widget_recent_comments_style') ) {
      remove_filter('wp_head', 'wp_widget_recent_comments_style' );
   }
}

// remove injected CSS from recent comments widget
function starter_remove_recent_comments_style() {
  global $wp_widget_factory;
  if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
    remove_action('wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style'));
  }
}

// remove injected CSS from gallery
function starter_gallery_style($css) {
  return preg_replace("!<style type='text/css'>(.*?)</style>!s", '', $css);
}

/**********************
Enqueue CSS and Scripts
**********************/

// loading modernizr and jquery, and reply script
function starter_scripts_and_styles() {
  if (!is_admin()) {

    // modernizr (without media query polyfill)
    wp_register_script( 'starter-modernizr', get_template_directory_uri() . '/build/modernizr.min.js', array(), '2.8.3', false );

    // comment reply script for threaded comments
    if( get_option( 'thread_comments' ) )  { wp_enqueue_script( 'comment-reply' ); }
    
    
    // If the server include .dev then load unminifeid css, else load minified and prefixed. 
    // Allows us to use Sourcemaps in chrome to see which .scss file is creating rules
    if (strpos($_SERVER['SERVER_NAME'],'.dev') !== false) {
      wp_register_style( 'starter-stylesheet', get_template_directory_uri() . '/build/style.css', array(), '201411181035', 'all' );
      wp_register_script( 'starter-js', get_template_directory_uri() . '/build/production.js', array( 'jquery' ), '201411181035', true );
    } else {
      wp_register_style( 'starter-stylesheet', get_template_directory_uri() . '/build/mini-style.css', array(), '201411181035', 'all' );
      wp_register_script( 'starter-js', get_template_directory_uri() . '/build/production.min.js', array( 'jquery' ), '201411181035', true );
    }
    

    // enqueue styles and scripts
    wp_enqueue_script( 'starter-modernizr' );
    wp_enqueue_style('starter-ie-only');
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'starter-js' );
    wp_enqueue_script( 'html5shiv' );
    wp_enqueue_style( 'starter-stylesheet' );
  }
}

// adding the conditional wrapper around ie stylesheet
// source: http://code.garyjones.co.uk/ie-conditional-style-sheets-wordpress/
function starter_ie_conditional( $tag, $handle ) {
	if ( 'starter-ie-only' == $handle )
		$tag = '<!--[if lt IE 9]>' . "\n" . $tag . '<![endif]-->' . "\n";
	return $tag;
}

/*********************
Post related cleaning
*********************/
/* Customized the output of caption, you can remove the filter to restore back to the WP default output. Courtesy of DevPress. http://devpress.com/blog/captions-in-wordpress/ */
  function starter_cleaner_caption( $output, $attr, $content ) {

	/* We're not worried abut captions in feeds, so just return the output here. */
	if ( is_feed() )
		return $output;

	/* Set up the default arguments. */
	$defaults = array(
		'id' => '',
		'align' => 'alignnone',
		'width' => '',
		'caption' => ''
	);

	/* Merge the defaults with user input. */
	$attr = shortcode_atts( $defaults, $attr );

	/* If the width is less than 1 or there is no caption, return the content wrapped between the [caption]< tags. */
	if ( 1 > $attr['width'] || empty( $attr['caption'] ) )
		return $content;

	/* Set up the attributes for the caption <div>. */
	$attributes = ' class="figure ' . esc_attr( $attr['align'] ) . '"';

	/* Open the caption <div>. */
	$output = '<figure' . $attributes .'>';

	/* Allow shortcodes for the content the caption was created for. */
	$output .= do_shortcode( $content );

	/* Append the caption text. */
	$output .= '<figcaption>' . $attr['caption'] . '</figcaption>';

	/* Close the caption </div>. */
	$output .= '</figure>';

	/* Return the formatted, clean caption. */
	return $output;
	
} /* end cleaner_caption */

// Clean the output of attributes of images in editor. Courtesy of SitePoint. http://www.sitepoint.com/wordpress-change-img-tag-html/
function starter_image_tag_class($class, $id, $align, $size) {
	$align = 'align' . esc_attr($align);
	return $align;
} /* end image_tag_class */

// Remove width and height in editor, for a better responsive world.
function starter_image_editor($html, $id, $alt, $title) {
	return preg_replace(array(
			'/\s+width="\d+"/i',
			'/\s+height="\d+"/i',
			'/alt=""/i'
		),
		array(
			'',
			'',
			'',
			'alt="' . $title . '"'
		),
		$html);
} /* end image_editor */

/**
 * Filter Yoast SEO Metabox Priority
 */
add_filter( 'wpseo_metabox_prio', 'mb_filter_yoast_seo_metabox' );
function mb_filter_yoast_seo_metabox() {
	return 'low';
}
?>
