<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

/**
 * Wraps the page title in a 'title' element.
 *
 * @since 1.0.0
 */
add_filter( 'wp_title', 'bizznis_doctitle_wrap', 20 );
function bizznis_doctitle_wrap( $title ) {
	# Only applies, if not currently in admin, or for a feed.
	return is_feed() || is_admin() ? $title : sprintf( "<title>%s</title>\n", $title );
}

/**
 * Return filtered post title.
 *
 * @since 1.0.0
 */
add_action( 'wp_head', 'wp_title', 1 );
add_filter( 'wp_title', 'bizznis_default_title', 10, 3 );
function bizznis_default_title( $title, $sep, $seplocation ) {
	global $wp_query;
	if ( is_feed() ) {
		return trim( $title );
	}
	# Separator definition
	$sep = bizznis_get_seo_option( 'doctitle_sep' ) ? bizznis_get_seo_option( 'doctitle_sep' ) : '-';
	$seplocation = bizznis_get_seo_option( 'doctitle_seplocation' ) ? bizznis_get_seo_option( 'doctitle_seplocation' ) : 'right';
	# If viewing the home page
	if ( is_front_page() ) {
		#* Determine the doctitle
		$title = bizznis_get_seo_option( 'home_doctitle' ) ? bizznis_get_seo_option( 'home_doctitle' ) : get_bloginfo( 'name' );
		#* Append site description, if necessary
		$title = bizznis_get_seo_option( 'append_description_home' ) ? $title . " $sep " . get_bloginfo( 'description' ) : $title;
	}
	# if viewing a post / page / attachment
	if ( is_singular() ) {
		# The User Defined Title (Bizznis)
		if ( bizznis_get_custom_field( '_bizznis_title' ) ) {
			$title = bizznis_get_custom_field( '_bizznis_title' );
		}
		# All-in-One SEO Pack Title (latest, vestigial)
		elseif ( bizznis_get_custom_field( '_aioseop_title' ) ) {
			$title = bizznis_get_custom_field( '_aioseop_title' );
		}
		# Headspace Title (vestigial)
		elseif ( bizznis_get_custom_field( '_headspace_page_title' ) ) {
			$title = bizznis_get_custom_field( '_headspace_page_title' );
		}
		# Thesis Title (vestigial)
		elseif ( bizznis_get_custom_field( 'thesis_title' ) ) {
			$title = bizznis_get_custom_field( 'thesis_title' );
		}
		# SEO Title Tag (vestigial)
		elseif ( bizznis_get_custom_field( 'title_tag' ) ) {
			$title = bizznis_get_custom_field( 'title_tag' );
		}
		# All-in-One SEO Pack Title (old, vestigial)
		elseif ( bizznis_get_custom_field( 'title' ) ) {
			$title = bizznis_get_custom_field( 'title' );
		}
	}
	if ( is_category() ) {
		// $term = get_term( get_query_var('cat'), 'category' );
		$term  = $wp_query->get_queried_object();
		$title = ! empty( $term->meta['doctitle'] ) ? $term->meta['doctitle'] : $title;
	}
	if ( is_tag() ) {
		// $term = get_term( get_query_var('tag_id'), 'post_tag' );
		$term  = $wp_query->get_queried_object();
		$title = ! empty( $term->meta['doctitle'] ) ? $term->meta['doctitle'] : $title;
	}
	if ( is_tax() ) {
		$term  = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
		$title = ! empty( $term->meta['doctitle'] ) ? wp_kses_stripslashes( wp_kses_decode_entities( $term->meta['doctitle'] ) ) : $title;
	}
	if ( is_author() ) {
		$user_title = get_the_author_meta( 'doctitle', (int) get_query_var( 'author' ) );
		$title      = $user_title ? $user_title : $title;
	}
	# If we don't want site name appended, or if we're on the home page
	if ( ! bizznis_get_seo_option( 'append_site_title' ) || is_front_page() ) {
		return esc_html( trim( $title ) );
	}
	# Else append the site name
	$title = 'right' == $seplocation ? $title . " $sep " . get_bloginfo( 'name' ) : get_bloginfo( 'name' ) . " $sep " . $title;
	return esc_html( trim( $title ) );
}

/**
 * Remove unnecessary code that WordPress puts in the 'head'.
 *
 * @since 1.0.0
 */
add_action( 'get_header', 'bizznis_doc_head_control' );
function bizznis_doc_head_control() {
	remove_action( 'wp_head', 'wp_generator' );
	if ( ! bizznis_get_seo_option( 'head_adjacent_posts_rel_link' ) ) {
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
	}
	if ( ! bizznis_get_seo_option( 'head_wlwmanifest_link' ) ) {
		remove_action( 'wp_head', 'wlwmanifest_link' );
	}
	if ( ! bizznis_get_seo_option( 'head_shortlink' ) ) {
		remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
	}
	if ( is_single() && ! bizznis_get_option( 'comments_posts' ) ) {
		remove_action( 'wp_head', 'feed_links_extra', 3 );
	}
	if ( is_page() && ! bizznis_get_option( 'comments_pages' ) ) {
		remove_action( 'wp_head', 'feed_links_extra', 3 );
	}
}

/**
 * Output the meta description based on contextual criteria.
 *
 * @since 1.0.0
 */
add_action( 'wp_head', 'bizznis_seo_meta_description', 1 );
function bizznis_seo_meta_description() {
	global $wp_query;
	# Output nothing if description isn't present.
	$description = '';
	# If we're on the home page
	if ( is_front_page() ) {
		$description = bizznis_get_seo_option( 'home_description' ) ? bizznis_get_seo_option( 'home_description' ) : get_bloginfo( 'description' );
	}
	# If we're on a single post / page / attachment
	if ( is_singular() ) {
		# Description is set via custom field
		if ( bizznis_get_custom_field( '_bizznis_description' ) ) {
			$description = bizznis_get_custom_field( '_bizznis_description' );
		}
		# All-in-One SEO Pack (latest, vestigial)
		elseif ( bizznis_get_custom_field( '_aioseop_description' ) ) {
			$description = bizznis_get_custom_field( '_aioseop_description' );
		}
		# Headspace2 (vestigial)
		elseif ( bizznis_get_custom_field( '_headspace_description' ) ) {
			$description = bizznis_get_custom_field( '_headspace_description' );
		}
		# Thesis (vestigial)
		elseif ( bizznis_get_custom_field( 'thesis_description' ) ) {
			$description = bizznis_get_custom_field( 'thesis_description' );
		}
		# All-in-One SEO Pack (old, vestigial)
		elseif ( bizznis_get_custom_field( 'description' ) ) {
			$description = bizznis_get_custom_field( 'description' );
		}
	}
	if ( is_category() ) {
		// $term = get_term( get_query_var('cat'), 'category' );
		$term = $wp_query->get_queried_object();
		$description = ! empty( $term->meta['description'] ) ? $term->meta['description'] : '';
	}
	if ( is_tag() ) {
		// $term = get_term( get_query_var('tag_id'), 'post_tag' );
		$term = $wp_query->get_queried_object();
		$description = ! empty( $term->meta['description'] ) ? $term->meta['description'] : '';
	}
	if ( is_tax() ) {
		$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
		$description = ! empty( $term->meta['description'] ) ? wp_kses_stripslashes( wp_kses_decode_entities( $term->meta['description'] ) ) : '';
	}
	if ( is_author() ) {
		$user_description = get_the_author_meta( 'meta_description', (int) get_query_var( 'author' ) );
		$description = $user_description ? $user_description : '';
	}
	# Add the description if one exists
	if ( $description ) {
		echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
	}
}

/**
 * Optionally output the responsive CSS viewport tag.
 *
 * @since 1.0.0
 */
add_action( 'wp_head', 'bizznis_responsive_viewport', 1 );
function bizznis_responsive_viewport() {
	# Child theme needs to support 'bizznis-responsive-viewport'.
	if ( ! current_theme_supports( 'bizznis-responsive-viewport' ) ) {
		return;
	}
	echo '<meta name="viewport" content="width=device-width, initial-scale=1" />' . "\n";
}

/**
 * Output the 'index', 'follow', 'noodp', 'noydir', 'noarchive' robots meta code in the document 'head'.
 *
 * @since 1.0.0
 */
add_action( 'wp_head', 'bizznis_robots_meta', 1 );
function bizznis_robots_meta() {
	global $wp_query;
	# If the blog is private, then following logic is unnecessary as WP will insert noindex and nofollow
	if ( 0 == get_option( 'blog_public' ) ) {
		return;
	}
	# Defaults
	$meta = array(
		'noindex'   => '',
		'nofollow'  => '',
		'noarchive' => bizznis_get_seo_option( 'noarchive' ) ? 'noarchive' : '',
		'noodp'     => bizznis_get_seo_option( 'noodp' ) ? 'noodp' : '',
		'noydir'    => bizznis_get_seo_option( 'noydir' ) ? 'noydir' : '',
	);
	# Check home page SEO settings, set noindex, nofollow and noarchive
	if ( is_front_page() ) {
		$meta['noindex']   = bizznis_get_seo_option( 'home_noindex' ) ? 'noindex' : $meta['noindex'];
		$meta['nofollow']  = bizznis_get_seo_option( 'home_nofollow' ) ? 'nofollow' : $meta['nofollow'];
		$meta['noarchive'] = bizznis_get_seo_option( 'home_noarchive' ) ? 'noarchive' : $meta['noarchive'];
	}
	if ( is_category() ) {
		$term = $wp_query->get_queried_object();
		$meta['noindex']   = $term->meta['noindex'] ? 'noindex' : $meta['noindex'];
		$meta['nofollow']  = $term->meta['nofollow'] ? 'nofollow' : $meta['nofollow'];
		$meta['noarchive'] = $term->meta['noarchive'] ? 'noarchive' : $meta['noarchive'];
		$meta['noindex']   = bizznis_get_seo_option( 'noindex_cat_archive' ) ? 'noindex' : $meta['noindex'];
		$meta['noarchive'] = bizznis_get_seo_option( 'noarchive_cat_archive' ) ? 'noarchive' : $meta['noarchive'];
		# noindex paged archives, if canonical archives is off
		$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
		$meta['noindex'] = $paged > 1 && ! bizznis_get_seo_option( 'canonical_archives' ) ? 'noindex' : $meta['noindex'];
	}
	if ( is_tag() ) {
		$term = $wp_query->get_queried_object();
		$meta['noindex']   = $term->meta['noindex'] ? 'noindex' : $meta['noindex'];
		$meta['nofollow']  = $term->meta['nofollow'] ? 'nofollow' : $meta['nofollow'];
		$meta['noarchive'] = $term->meta['noarchive'] ? 'noarchive' : $meta['noarchive'];
		$meta['noindex']   = bizznis_get_seo_option( 'noindex_tag_archive' ) ? 'noindex' : $meta['noindex'];
		$meta['noarchive'] = bizznis_get_seo_option( 'noarchive_tag_archive' ) ? 'noarchive' : $meta['noarchive'];
		# noindex paged archives, if canonical archives is off
		$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
		$meta['noindex'] = $paged > 1 && ! bizznis_get_seo_option( 'canonical_archives' ) ? 'noindex' : $meta['noindex'];
	}
	if ( is_tax() ) {
		$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
		$meta['noindex']   = $term->meta['noindex'] ? 'noindex' : $meta['noindex'];
		$meta['nofollow']  = $term->meta['nofollow'] ? 'nofollow' : $meta['nofollow'];
		$meta['noarchive'] = $term->meta['noarchive'] ? 'noarchive' : $meta['noarchive'];
		# noindex paged archives, if canonical archives is off
		$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
		$meta['noindex'] = $paged > 1 && ! bizznis_get_seo_option( 'canonical_archives' ) ? 'noindex' : $meta['noindex'];
	}
	if ( is_author() ) {
		$meta['noindex']   = get_the_author_meta( 'noindex', (int) get_query_var( 'author' ) ) ? 'noindex' : $meta['noindex'];
		$meta['nofollow']  = get_the_author_meta( 'nofollow', (int) get_query_var( 'author' ) ) ? 'nofollow' : $meta['nofollow'];
		$meta['noarchive'] = get_the_author_meta( 'noarchive', (int) get_query_var( 'author' ) ) ? 'noarchive' : $meta['noarchive'];
		$meta['noindex']   = bizznis_get_seo_option( 'noindex_author_archive' ) ? 'noindex' : $meta['noindex'];
		$meta['noarchive'] = bizznis_get_seo_option( 'noarchive_author_archive' ) ? 'noarchive' : $meta['noarchive'];
		# noindex paged archives, if canonical archives is off
		$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
		$meta['noindex'] = $paged > 1 && ! bizznis_get_seo_option( 'canonical_archives' ) ? 'noindex' : $meta['noindex'];
	}
	if ( is_date() ) {
		$meta['noindex']   = bizznis_get_seo_option( 'noindex_date_archive' ) ? 'noindex' : $meta['noindex'];
		$meta['noarchive'] = bizznis_get_seo_option( 'noarchive_date_archive' ) ? 'noarchive' : $meta['noarchive'];
	}
	if ( is_search() ) {
		$meta['noindex']   = bizznis_get_seo_option( 'noindex_search_archive' ) ? 'noindex' : $meta['noindex'];
		$meta['noarchive'] = bizznis_get_seo_option( 'noarchive_search_archive' ) ? 'noarchive' : $meta['noarchive'];
	}
	if ( is_singular() ) {
		$meta['noindex']   = bizznis_get_custom_field( '_bizznis_noindex' ) ? 'noindex' : $meta['noindex'];
		$meta['nofollow']  = bizznis_get_custom_field( '_bizznis_nofollow' ) ? 'nofollow' : $meta['nofollow'];
		$meta['noarchive'] = bizznis_get_custom_field( '_bizznis_noarchive' ) ? 'noarchive' : $meta['noarchive'];
	}
	# Strip empty array items
	$meta = array_filter( $meta );
	# Add meta if any exist
	if ( $meta ) {
		printf( '<meta name="robots" content="%s" />' . "\n", implode( ',', $meta ) );
	}
}

/**
 * Echo favicon link if one is found.
 *
 * @since 1.0.0
 */
add_action( 'wp_head', 'bizznis_load_favicon' );
function bizznis_load_favicon() {
	# Allow child theme to short-circuit this function
	$pre = apply_filters( 'bizznis_pre_load_favicon', false );
	if ( $pre !== false ) {
		$favicon = $pre;
	}
	elseif ( file_exists( CHILD_DIR . '/images/favicon.ico' ) ) {
		$favicon = CHILD_URL . '/images/favicon.ico';
	}
	elseif ( file_exists( CHILD_DIR . '/images/favicon.gif' ) ) {
		$favicon = CHILD_URL . '/images/favicon.gif';
	}
	elseif ( file_exists( CHILD_DIR . '/images/favicon.png' ) ) {
		$favicon = CHILD_URL . '/images/favicon.png';
	}
	elseif ( file_exists( CHILD_DIR . '/images/favicon.jpg' ) ) {
		$favicon = CHILD_URL . '/images/favicon.jpg';
	}
	else {
		$favicon = BIZZNIS_IMAGES_URL . '/favicon.ico';
	}
	# URL to favicon is filtered via 'bizznis_favicon_url' before being echoed.
	$favicon = apply_filters( 'bizznis_favicon_url', $favicon );
	if ( $favicon ) {
		echo '<link rel="Shortcut Icon" href="' . esc_url( $favicon ) . '" type="image/x-icon" />' . "\n";
	}
}

/**
 * Adds the pingback meta tag to the head so that other sites can know how to send a pingback to our site.
 *
 * @since 1.0.0
 */
add_action( 'wp_head', 'bizznis_do_meta_pingback' );
function bizznis_do_meta_pingback() {
	if ( 'open' == get_option( 'default_ping_status' ) ) {
		echo '<link rel="pingback" href="' . get_bloginfo( 'pingback_url' ) . '" />' . "\n";
	}
}

/**
 * Echo custom canonical link tag.
 *
 * @since 1.0.0
 */
add_action( 'wp_head', 'bizznis_canonical', 5 );
function bizznis_canonical() {
	# Remove the WordPress canonical
	remove_action( 'wp_head', 'rel_canonical' );
	global $wp_query;
	# Output nothing if canonical isn't present.
	$canonical = '';
	if ( is_front_page() ) {
		$canonical = trailingslashit( home_url() );
	}
	if ( is_singular() ) {
		if ( ! $id = $wp_query->get_queried_object_id() ) {
			return;
		}
		$cf = bizznis_get_custom_field( '_bizznis_canonical_uri' );
		$canonical = $cf ? $cf : get_permalink( $id );
	}
	if ( is_category() || is_tag() || is_tax() ) {
		if ( ! $id = $wp_query->get_queried_object_id() ) {
			return;
		}
		$taxonomy = $wp_query->queried_object->taxonomy;
		$canonical = bizznis_get_seo_option( 'canonical_archives' ) ? get_term_link( (int) $id, $taxonomy ) : 0;
	}
	if ( is_author() ) {
		if ( ! $id = $wp_query->get_queried_object_id() ) {
			return;
		}
		$canonical = bizznis_get_seo_option( 'canonical_archives' ) ? get_author_posts_url( $id ) : 0;
	}
	if ( $canonical ) {
		printf( '<link rel="canonical" href="%s" />' . "\n", esc_url( apply_filters( 'bizznis_canonical', $canonical ) ) );
	}
}

/**
 * Echo custom rel="author" link tag.
 *
 * @since 1.0.0
 */
add_action( 'wp_head', 'bizznis_rel_author' );
function bizznis_rel_author() {
	# If the appropriate information has been entered for the homepage author
	if ( is_front_page() && $gplus_url = get_user_option( 'googleplus', bizznis_get_seo_option( 'home_author' ) ) ) {
		printf( '<link rel="author" href="%s" />' . "\n", esc_url( $gplus_url ) );
		return;
	}
	global $post;
	# If the appropriate information has been entered for an individual post/page
	if ( is_singular() && isset( $post->post_author ) && $gplus_url = get_user_option( 'googleplus', $post->post_author ) ) {
		printf( '<link rel="author" href="%s" />' . "\n", esc_url( $gplus_url ) );
		return;
	}
	# If the appropriate information has been entered for an individual author archive
	if ( is_author() && get_query_var( 'author' ) && $gplus_url = get_user_option( 'googleplus', get_query_var( 'author' ) ) ) {
		printf( '<link rel="author" href="%s" />' . "\n", esc_url( $gplus_url ) );
		return;
	}
}

/**
 * Echo header scripts in to wp_head().
 *
 * @since 1.0.0
 */
add_filter( 'bizznis_header_scripts', 'do_shortcode' );
add_action( 'wp_head', 'bizznis_header_scripts' );
function bizznis_header_scripts() {
	# Applies 'bizznis_header_scripts' filter on value stored in header_scripts setting.
	echo apply_filters( 'bizznis_header_scripts', bizznis_get_option( 'header_scripts' ) );
	# If singular, echo scripts from custom field
	if ( is_singular() ) {
		bizznis_custom_field( '_bizznis_scripts' );
	}
}

/**
 * Activate the custom header feature.
 *
 * @since 1.0.0
 */
add_action( 'after_setup_theme', 'bizznis_custom_header' );
function bizznis_custom_header() {
	$wp_custom_header = get_theme_support( 'custom-header' );
	# Stop here if not active (Bizznis of WP custom header)
	if ( ! $wp_custom_header ) {
		return;
	}
	# Blog title option is obsolete when custom header is active
	add_filter( 'bizznis_pre_get_option_blog_title', '__return_empty_array' );
	# Stop here if WP custom header is active
	if ( $wp_custom_header ) {
		return;
	}
}

/**
 * Custom header callback.
 *
 * @since 1.0.0
 */
add_action( 'wp_head', 'bizznis_custom_header_style' );
function bizznis_custom_header_style() {
	# Stop here if custom header not supported
	if ( ! current_theme_supports( 'custom-header' ) ) {
		return;
	}
	# Stop here if user specifies their own callback
	if ( get_theme_support( 'custom-header', 'wp-head-callback' ) ) {
		return;
	}
	# Output nothing if header style isn't present.
	$output = '';
	$header_image = get_header_image();
	$text_color   = get_header_textcolor();
	# If no options set, don't waste the output. Stop here.
	if ( empty( $header_image ) && ! display_header_text() && $text_color == get_theme_support( 'custom-header', 'default-text-color' ) ) {
		return;
	}
	$header_selector = get_theme_support( 'custom-header', 'header-selector' );
	$title_selector  = '.custom-header .site-title';
	$desc_selector   = '.custom-header .site-description';
	# Header selector fallback
	if ( ! $header_selector ) {
		$header_selector = '.custom-header .title-area';
	}
	# Header image CSS, if exists
	if ( $header_image ) {
		$output .= sprintf( '%s { background-image: url(%s) !important; background-repeat: no-repeat; background-position: center center; min-width: %spx; min-height: %spx; }', $header_selector, esc_url( $header_image ), get_custom_header()->width, get_custom_header()->height );
		$output .= sprintf( '%s a { min-width: %spx; min-height: %spx; }', $title_selector, get_custom_header()->width, get_custom_header()->height );
	}
	# Header text color CSS, if showing text
	if ( display_header_text() && $text_color != get_theme_support( 'custom-header', 'default-text-color' ) ) {
		$output .= sprintf( '%2$s a, %2$s a:hover, %3$s { color: #%1$s !important; }', esc_html( $text_color ), esc_html( $title_selector ), esc_html( $desc_selector ) );
	}
	if ( $output ) {
		printf( '<style type="text/css">%s</style>' . "\n", $output );
	}
}

/**
 * Echo the site title into the header.
 *
 * @since 1.0.0
 */
add_action( 'bizznis_site_title', 'bizznis_seo_site_title' );
function bizznis_seo_site_title() {
	# Set what goes inside the wrapping tags
	$inside = sprintf( '<a href="%s" title="%s">%s</a>', trailingslashit( home_url() ), esc_attr( get_bloginfo( 'name' ) ), get_bloginfo( 'name' ) );
	# Determine which wrapping tags to use
	$wrap = is_home() && 'title' == bizznis_get_seo_option( 'home_h1_on' ) ? 'h1' : 'p';
	# A little fallback, in case an SEO plugin is active
	$wrap = is_home() && ! bizznis_get_seo_option( 'home_h1_on' ) ? 'h1' : $wrap;
	# And finally, $wrap in h1 if HTML5 & semantic headings enabled
	$wrap = bizznis_get_seo_option( 'semantic_headings' ) ? 'h1' : $wrap;
	# Build the title
	$title  = sprintf( "<{$wrap} %s>", bizznis_attr( 'site-title' ) ). $inside ."</{$wrap}>";
	# Echo (filtered)
	echo apply_filters( 'bizznis_seo_title', $title, $inside, $wrap );
}

/**
 * Echo the site description into the header.
 *
 * @since 1.0.0
 */
add_action( 'bizznis_site_title', 'bizznis_seo_site_description' );
function bizznis_seo_site_description() {
	# Set what goes inside the wrapping tags
	$inside = esc_html( get_bloginfo( 'description' ) );
	# Determine which wrapping tags to use
	$wrap = is_home() && 'description' == bizznis_get_seo_option( 'home_h1_on' ) ? 'h1' : 'p';
	# And finally, $wrap in h2 if HTML5 & semantic headings enabled
	$wrap = bizznis_get_seo_option( 'semantic_headings' ) ? 'h2' : $wrap;
	# Build the description
	$description  = sprintf( "<{$wrap} %s>", bizznis_attr( 'site-description' ) ). $inside ."</{$wrap}>";
	# Output (filtered)
	$output = $inside ? apply_filters( 'bizznis_seo_description', $description, $inside, $wrap ) : '';
	echo $output;
}

/**
 * Hide header content on all pages except homepage
 *
 * Optionally call inside a Child theme
 *
 * @since 1.0.0
 */
// add_filter( 'bizznis_show_header_content', 'bizznis_hide_header_content' );
function bizznis_hide_header_content() {
	# hide header content on all pages except homepage and when primary menu location is not active
	if ( is_home() || is_front_page() || ! has_nav_menu( 'primary' ) ) {
		return true;
	}
}
