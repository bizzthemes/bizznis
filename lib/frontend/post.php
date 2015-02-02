<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

/**
 * Restore all default post loop output by re-hooking all default functions.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_reset_loops' ) ) :
function bizznis_reset_loops() {
	# HTML5 Hooks
	add_action( 'bizznis_entry_header', 'bizznis_do_post_format_image', 5 );
	add_action( 'bizznis_entry_header', 'bizznis_entry_header_markup_open', 5 );
	add_action( 'bizznis_entry_header', 'bizznis_entry_header_markup_close', 15 );
	add_action( 'bizznis_entry_header', 'bizznis_post_info' );
	add_action( 'bizznis_entry_header', 'bizznis_do_post_title' );
	add_action( 'bizznis_entry_content', 'bizznis_do_post_image' );
	add_action( 'bizznis_entry_content', 'bizznis_do_post_content' );
	add_action( 'bizznis_entry_content', 'bizznis_do_post_permalink' );
	add_action( 'bizznis_entry_content', 'bizznis_do_post_content_nav' );
	add_action( 'bizznis_entry_footer', 'bizznis_entry_footer_markup_open', 5 );
	add_action( 'bizznis_entry_footer', 'bizznis_entry_footer_markup_close', 15 );
	add_action( 'bizznis_entry_footer', 'bizznis_post_meta' );
	add_action( 'bizznis_after_entry', 'bizznis_do_author_box_single' );
	# Other
	add_action( 'bizznis_loop_else', 'bizznis_do_noposts' );
	add_action( 'bizznis_after_endwhile', 'bizznis_posts_nav' );
	# Reset loop args
	global $_bizznis_loop_args;
	$_bizznis_loop_args = array();
	do_action( 'bizznis_reset_loops' );
}
endif;

add_filter( 'post_class', 'bizznis_entry_post_class' );
/**
 * Add 'entry' post class, remove 'hentry' post class if HTML5.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_entry_post_class' ) ) :
function bizznis_entry_post_class( $classes ) {
	# Add "entry" to the post class array
	$classes[] = 'entry';
	# Remove "hentry" from post class array, if HTML5
	$classes = array_diff( $classes, array( 'hentry' ) );
	return $classes;
}
endif;

add_filter( 'post_class', 'bizznis_custom_post_class', 15 );
/**
 * Add a custom post class, saved as a custom field.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_custom_post_class' ) ) :
function bizznis_custom_post_class( array $classes ) {
	$new_class = bizznis_get_custom_field( '_bizznis_custom_post_class' );
	if ( $new_class ) {
		$classes[] = esc_attr( $new_class );
	}
	return $classes;
}
endif;

add_action( 'bizznis_entry_header', 'bizznis_do_post_format_image', 5 );
/**
 * Add a post format icon. Adds an image, corresponding to the post format, before the post title.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_do_post_format_image' ) ) :
function bizznis_do_post_format_image() {
	# Stop here if post formats aren't supported
	if ( ! current_theme_supports( 'post-formats' ) || ! current_theme_supports( 'bizznis-post-format-images' ) ) {
		return;
	}
	# Get post format
	$post_format = get_post_format();
	# If post format is set, look for post format image
	if ( $post_format && file_exists( sprintf( '%s/images/post-formats/%s.png', CHILD_DIR, $post_format ) ) ) {
		printf( '<a href="%s" title="%s" rel="bookmark"><img src="%s" class="post-format-image" alt="%s" /></a>', get_permalink(), the_title_attribute( 'echo=0' ), sprintf( '%s/images/post-formats/%s.png', CHILD_URL, $post_format ), $post_format );
	}
	# Else, look for the default post format image
	elseif ( file_exists( sprintf( '%s/images/post-formats/default.png', CHILD_DIR ) ) ) {
		printf( '<a href="%s" title="%s" rel="bookmark"><img src="%s/images/post-formats/default.png" class="post-format-image" alt="%s" /></a>', get_permalink(), the_title_attribute( 'echo=0' ), CHILD_URL, 'post' );
	}
}
endif;

add_action( 'bizznis_entry_header', 'bizznis_entry_header_markup_open', 5 );
/**
 * Echo the opening structural markup for the entry header.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_entry_header_markup_open' ) ) :
function bizznis_entry_header_markup_open() {
	printf( '<header %s>', bizznis_attr( 'entry-header' ) );
}
endif;

add_action( 'bizznis_entry_header', 'bizznis_entry_header_markup_close', 15 );
/**
 * Echo the closing structural markup for the entry header.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_entry_header_markup_close' ) ) :
function bizznis_entry_header_markup_close() {
	echo '</header>';
}
endif;

add_action( 'bizznis_entry_header', 'bizznis_do_post_title' );
/**
 * Echo the title of a post.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_do_post_title' ) ) :
function bizznis_do_post_title() {
	$title = apply_filters( 'bizznis_post_title_text', get_the_title() );
	# Stop here if no title
	if ( 0 == strlen( $title ) ) {
		return;
	}
	# Link it, if necessary
	if ( ! is_singular() && apply_filters( 'bizznis_link_post_title', true ) ) {
		$title = sprintf( '<a href="%s" rel="bookmark">%s</a>', get_permalink(), $title );
	}
	# Wrap in H1 on singular pages
	$wrap = is_singular() ? 'h1' : 'h2';
	# Also, if HTML5 with semantic headings, wrap in H1
	$wrap = apply_filters( 'bizznis_entry_header_wrap', $wrap );
	# Build the output
	$output = sprintf( "<{$wrap} %s>", bizznis_attr( 'entry-title' ) );
	$output .= "{$title}";
	$output .= "</{$wrap}>";
	echo apply_filters( 'bizznis_post_title_output', "$output \n" );
}
endif;

add_action( 'bizznis_entry_content', 'bizznis_do_post_image' );
/**
 * Echo the post image on archive pages.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_do_post_image' ) ) :
function bizznis_do_post_image() {
	# If this an archive page and the option is set to show thumbnail, then it gets the image size as per the theme setting, wraps it in the post permalink and echoes it.
	if ( ! is_singular() && bizznis_get_option( 'content_archive_thumbnail' ) ) {
		$img = bizznis_get_image( array(
			'format'  => 'html',
			'size'    => bizznis_get_option( 'image_size' ),
			'context' => 'archive',
			'attr'    => bizznis_parse_attr( 'entry-image' ),
		) );
		if ( ! empty( $img ) ) {
			printf( '<a href="%s" title="%s">%s</a>', get_permalink(), the_title_attribute( 'echo=0' ), $img );
		}
	}
}
endif;

add_action( 'bizznis_entry_content', 'bizznis_do_post_content' );
/**
 * Echo the post content.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_do_post_content' ) ) :
function bizznis_do_post_content() {
	# On single posts or pages it echoes the full content
	if ( is_singular() ) {
		the_content();
		# optionally echoes the trackback string if enabled
		if ( is_single() && 'open' === get_option( 'default_ping_status' ) && post_type_supports( get_post_type(), 'trackbacks' ) ) {
			echo '<!--';
			trackback_rdf();
			echo '-->' . "\n";
		}
		# On single pages, also adds the edit link after the content.
		if ( is_page() && apply_filters( 'bizznis_edit_post_link', true ) ) {
			edit_post_link( __( '(Edit)', 'bizznis' ), '', '' );
		}
	}
	elseif ( 'excerpts' == bizznis_get_option( 'content_archive' ) ) {
		the_excerpt();
	}
	else {
		if ( bizznis_get_option( 'content_archive_limit' ) ) {
			the_content_limit( (int) bizznis_get_option( 'content_archive_limit' ), __( '[Read more...]', 'bizznis' ) );
		}
		else {
			the_content( __( '[Read more...]', 'bizznis' ) );
		}
	}
}
endif;

add_action( 'bizznis_entry_content', 'bizznis_do_post_permalink' );
/**
 * Show permalink if no title.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_do_post_permalink' ) ) :
function bizznis_do_post_permalink() {
	# Don't show on singular views, or if the entry has a title
	if ( is_singular() || get_the_title() ) {
		return;
	}
	$permalink = get_permalink();
	echo apply_filters( 'bizznis_post_permalink', sprintf( '<p class="entry-permalink"><a href="%s" rel="bookmark">%s</a></p>', esc_url( $permalink ), esc_html( $permalink ) ) );
}
endif;

add_action( 'bizznis_entry_content', 'bizznis_do_post_content_nav' );
/**
 * Display page links for paginated posts (i.e. includes the <!--nextpage--> Quicktag one or more times).
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_do_post_content_nav' ) ) :
function bizznis_do_post_content_nav() {
	wp_link_pages( array(
		'before' => sprintf( '<nav %s>', bizznis_attr( 'entry-pagination' ) ) . __( 'Pages:', 'bizznis' ),
		'after'  => '</nav>',
	) );
}
endif;

add_action( 'bizznis_loop_else', 'bizznis_do_noposts' );
/**
 * Echo filterable content when there are no posts to show.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_do_noposts' ) ) :
function bizznis_do_noposts() {
	printf( '<div class="entry"><p>%s</p></div>', apply_filters( 'bizznis_noposts_text', __( 'Sorry, no content matched your criteria.', 'bizznis' ) ) );
}
endif;

add_filter( 'bizznis_post_info', 'do_shortcode', 20 );
add_action( 'bizznis_entry_header', 'bizznis_post_info' );
/**
 * Echo the post info (byline) under the post title.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_post_info' ) ) :
function bizznis_post_info() {
	# Doesn't do post info on pages.
	if ( 'page' === get_post_type() ) {
		return;
	}
	$entry_meta = apply_filters( 'bizznis_post_info', '[post_date] ' . __( 'by', 'bizznis' ) . ' [post_author_posts_link] [post_comments] [post_edit]' );
	printf( '<p %s>' . $entry_meta . '</p>', bizznis_attr( 'entry-meta' ) );
}
endif;

add_action( 'bizznis_entry_footer', 'bizznis_entry_footer_markup_open', 5 );
/**
 * Echo the opening structural markup for the entry footer.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_entry_footer_markup_open' ) ) :
function bizznis_entry_footer_markup_open() {
	if ( 'post' === get_post_type() ) {
		printf( '<footer %s>', bizznis_attr( 'entry-footer' ) );
	}
}
endif;

add_action( 'bizznis_entry_footer', 'bizznis_entry_footer_markup_close', 15 );
/**
 * Echo the closing structural markup for the entry footer.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_entry_footer_markup_close' ) ) :
function bizznis_entry_footer_markup_close() {
	if ( 'post' === get_post_type() ) {
		echo '</footer>';
	}
}
endif;

add_filter( 'bizznis_post_meta', 'do_shortcode', 20 );
add_action( 'bizznis_entry_footer', 'bizznis_post_meta' );
/**
 * Echo the post meta after the post content.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_post_meta' ) ) :
function bizznis_post_meta() {
	# Doesn't do post meta on pages.
	if ( 'page' === get_post_type() ) {
		return;
	}
	$entry_meta = apply_filters( 'bizznis_post_meta', '[post_categories] [post_tags]' );
	printf( '<p %s>' . $entry_meta . '</p>', bizznis_attr( 'entry-meta' ) );
}
endif;

add_action( 'bizznis_after_entry', 'bizznis_do_author_box_single' );
/**
 * Conditionally add the author box after single posts or pages.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_do_author_box_single' ) ) :
function bizznis_do_author_box_single() {
	if ( ! is_single() ) {
		return;
	}
	if ( get_the_author_meta( 'bizznis_author_box_single', get_the_author_meta( 'ID' ) ) ) {
		bizznis_author_box( 'single' );
	}
}
endif;

/**
 * Echo the the author box and its contents.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_author_box' ) ) :
function bizznis_author_box( $context = '', $echo = true ) {
	global $authordata;
	$authordata    = is_object( $authordata ) ? $authordata : get_userdata( get_query_var( 'author' ) );
	$gravatar_size = apply_filters( 'bizznis_author_box_gravatar_size', 60, $context );
	$gravatar      = get_avatar( get_the_author_meta( 'email' ), $gravatar_size );
	$description   = wpautop( get_the_author_meta( 'description' ) );
	# The author box markup, contextual
	$title = __( 'About', 'bizznis' ) . ' <span itemprop="name">' . get_the_author() . '</span>';
	/**
	 * Author box title filter.
	 * 
	 * Allows you to filter the title of the author box. $context passed as second parameter to allow for contextual filtering.
	 *
	 * @since unknown
	 * 
	 * @param string $title Assembled Title.
	 * @param string $context Context. 
	 */
	$title = apply_filters( 'bizznis_author_box_title', $title, $context );
	$pattern  = sprintf( '<section %s>', bizznis_attr( 'author-box' ) );
	$pattern .= '%s';
	$pattern .= '<div class="author-body">';
	if ( 'single' === $context ) {
		$pattern .= '<h4 class="author-box-title">%s</h4>';
	} else {
		$pattern .= '<h1 class="author-box-title">%s</h1>';
	}
	$pattern .= '<div class="author-box-content" itemprop="description">%s</div>';
	$pattern .= '</div>';
	$pattern .= '</section>';
	$output = apply_filters( 'bizznis_author_box', sprintf( $pattern, $gravatar, $title, $description ), $context, $pattern, $gravatar, $title, $description );
	if ( $echo ) {
		echo $output;
	}
	else {
		return $output;
	}
}
endif;

add_action( 'bizznis_after_entry', 'bizznis_after_entry_widget_area' );
/**
 * Display after-entry widget area on the bizznis_after_entry action hook.
 *
 * @since 1.1.0
 * 
 * @uses bizznis_widget_area() Output widget area.
 */
if ( ! function_exists( 'bizznis_after_entry_widget_area' ) ) :
function bizznis_after_entry_widget_area() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}
	bizznis_widget_area( 'after-entry', array(
		'before' => '<div class="after-entry widget-area">',
		'after'  => '</div>',
	) );
}
endif;

add_action( 'bizznis_after_endwhile', 'bizznis_posts_nav' );
/**
 * Conditionally echo post navigation in a format dependent on chosen setting.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_posts_nav' ) ) :
function bizznis_posts_nav() {
	$nav = bizznis_get_option( 'posts_nav' );
	if( 'numeric' == $nav ) {
		bizznis_numeric_posts_nav();
	}
	else {
		bizznis_prev_next_posts_nav();
	}
}
endif;

/**
 * Echo post navigation in Previous Posts / Next Posts format.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_prev_next_posts_nav' ) ) :
function bizznis_prev_next_posts_nav() {
	$prev_link = get_previous_posts_link( apply_filters( 'bizznis_prev_link_text', '&#x000AB; ' . __( 'Previous Page', 'bizznis' ) ) );
	$next_link = get_next_posts_link( apply_filters( 'bizznis_next_link_text', __( 'Next Page', 'bizznis' ) . ' &#x000BB;' ) );
	$prev = $prev_link ? '<div class="pagination-previous alignleft">' . $prev_link . '</div>' : '';
	$next = $next_link ? '<div class="pagination-next alignright">' . $next_link . '</div>' : '';
	$nav = sprintf( '<nav %s>', bizznis_attr( 'archive-pagination' ) );
	$nav .= $prev;
	$nav .= $next;
	$nav .= '</nav>';
	if ( $prev || $next ) {
		echo $nav;
	}
}
endif;

/**
 * Echo post navigation in page numbers format.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_numeric_posts_nav' ) ) :
function bizznis_numeric_posts_nav() {
	# Stop on singular posts
	if( is_singular() ) {
		return;
	}
	global $wp_query;
	# Stop execution if there's only 1 page
	if( $wp_query->max_num_pages <= 1 ) {
		return;
	}
	$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
	$max   = intval( $wp_query->max_num_pages );
	# Add current page to the array
	if ( $paged >= 1 ) {
		$links[] = $paged;
	}
	# Add the pages around the current page to the array
	if ( $paged >= 3 ) {
		$links[] = $paged - 1;
		$links[] = $paged - 2;
	}
	if ( ( $paged + 2 ) <= $max ) {
		$links[] = $paged + 2;
		$links[] = $paged + 1;
	}
	printf( '<nav %s>', bizznis_attr( 'archive-pagination' ) );
	echo '<ul>';
	# Previous Post Link
	if ( get_previous_posts_link() ) {
		printf( '<li class="pagination-previous">%s</li>' . "\n", get_previous_posts_link( apply_filters( 'bizznis_prev_link_text', '&#x000AB; ' . __( 'Previous Page', 'bizznis' ) ) ) );
	}
	# Link to first page, plus ellipses if necessary
	if ( ! in_array( 1, $links ) ) {
		$class = 1 == $paged ? ' class="active"' : '';
		printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( 1 ) ), '1' );
		if ( ! in_array( 2, $links ) ) {
			echo '<li class="pagination-omission">&#x02026;</li>';
		}
	}
	# Link to current page, plus 2 pages in either direction if necessary
	sort( $links );
	foreach ( (array) $links as $link ) {
		$class = $paged == $link ? ' class="active"' : '';
		printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $link ) ), $link );
	}
	# Link to last page, plus ellipses if necessary
	if ( ! in_array( $max, $links ) ) {
		if ( ! in_array( $max - 1, $links ) ) {
			echo '<li class="pagination-omission">&#x02026;</li>' . "\n";
		}
		$class = $paged == $max ? ' class="active"' : '';
		printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $max ) ), $max );
	}
	# Next Post Link
	if ( get_next_posts_link() ) {
		printf( '<li class="pagination-next">%s</li>' . "\n", get_next_posts_link( apply_filters( 'bizznis_next_link_text', __( 'Next Page', 'bizznis' ) . ' &#x000BB;' ) ) );
	}
	echo '</ul></nav>' . "\n";
}
endif;

// add_action( 'bizznis_after_entry', 'bizznis_prev_next_post_nav' );
/**
 * Display links to previous and next post, from a single post. 
 *
 * Optionally call it inside a child theme
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_prev_next_post_nav' ) ) :
function bizznis_prev_next_post_nav() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}
	printf( '<nav %s>', bizznis_attr( 'adjacent-entry-pagination' ) );
	echo '<div class="pagination-previous alignleft">';
	previous_post_link();
	echo '</div>';
	echo '<div class="pagination-next alignright">';
	next_post_link();
	echo '</div>';
	echo '</nav>';
}
endif;