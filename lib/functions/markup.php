<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

/**
 * Merge array of attributes with defaults, and apply contextual filter on array.
 *
 * @since 1.0.0
 */
function bizznis_parse_attr( $context, $attributes = array() ) {
	# Default attributes
    $defaults = array(
        'class' => sanitize_html_class( $context ),
    );
	# Custom attributes
    $attributes = wp_parse_args( $attributes, $defaults );
    # Return contextual filter
    return apply_filters( "bizznis_attr_{$context}", $attributes, $context );
}

/**
 * Build list of attributes into a string and apply contextual filter on string.
 *
 * @since 1.0.0
 */
function bizznis_attr( $context, $attributes = array() ) {
	# Add attributes
    $attributes = bizznis_parse_attr( $context, $attributes );
    # Cycle through attributes, build tag attribute string
	$output = ''; #zero default
    foreach ( $attributes as $key => $value ) {
		if ( ! $value ) {
            continue;
		}
		# add custom classes per context
		if ( $key == 'class' ) {
			$classes 	= explode( ' ', $value ); # turn into array
			$classes 	= apply_filters( 'bizznis_parse_attr_class', $classes, $context ); # apply filter
			$value 		= join( ' ', $classes ); # turn into string
		}
		if ( true === $value ) {
			$output .= esc_html( $key ) . ' ';
		} else {
			$output .= sprintf( '%s="%s" ', esc_html( $key ), esc_attr( $value ) );
		}
    }
    $output = apply_filters( "bizznis_attr_{$context}_output", $output, $attributes, $context );
	# Return all atrubutes
    return trim( $output );
}

/**
 * Helper function for use as a filter for when you want to prevent a class from being automatically
 * generated and output on an element that is passed through the markup API.
 *
 * @since 1.2.7
 * @param array $attributes Existing attributes.
 * @return array Amended attributes.
 */
function bizznis_attributes_empty_class( $attributes ) {
	$attributes['class'] = '';

	return $attributes;
}

/**
 * Helper function for use as a filter for when you want to add screen-reader-text class to an element.
 *
 * @since 1.2.7
 * @param array $attributes Existing attributes.
 * @return array Amended attributes.
 */
function bizznis_attributes_screen_reader_class( $attributes ) {
	$attributes['class'] .= ' screen-reader-text';

	return $attributes;
}

//* LIST OF ALL ATTRIBUTE FILTERS

/**
 * Add attributes for head element.
 *
 * @since 1.2.2
 *
 * @param array $attributes Existing attributes.
 * @return array Amended attributes.
 */
add_filter( 'bizznis_attr_head', 'bizznis_attributes_head' );
function bizznis_attributes_head( $attributes ) {
	$attributes['class'] = '';

	if ( ! is_front_page() ) {
		return $attributes;
	}

	$attributes['itemscope'] = true;
	$attributes['itemtype']  = 'http://schema.org/WebSite';

	return $attributes;
}

/**
 * Add attributes for body element.
 *
 * @since 1.0.0
 */
add_filter( 'bizznis_attr_body', 'bizznis_attributes_body' );
function bizznis_attributes_body( $attributes ) {
	$attributes['itemscope']	= true;
	$attributes['itemtype']		= 'http://schema.org/WebPage';
	# Search results pages
	if ( is_search() ) {
		$attributes['itemtype'] = 'http://schema.org/SearchResultsPage';
	}
	if ( is_singular( 'post' ) || is_archive() || is_home() ) {
		$attributes['itemtype']  = 'http://schema.org/Blog';
	}
	
	return $attributes;
}

/**
 * Add attributes for site header element.
 *
 * @since 1.0.0
 */
add_filter( 'bizznis_attr_site-header', 'bizznis_attributes_header' );
function bizznis_attributes_header( $attributes ) {
	$attributes['itemscope']	= true;
	$attributes['itemtype']		= 'http://schema.org/WPHeader';
	
	return $attributes;
}

/**
 * Add attributes for site title element.
 *
 * @since 1.0.0
 */
add_filter( 'bizznis_attr_site-title', 'bizznis_attributes_site_title' );
function bizznis_attributes_site_title( $attributes ) {
	$attributes['itemprop'] 	= 'headline';
	
	return $attributes;
}

/**
 * Add attributes for site description element.
 *
 * @since 1.0.0
 */
add_filter( 'bizznis_attr_site-description', 'bizznis_attributes_site_description' );
function bizznis_attributes_site_description( $attributes ) {
	$attributes['itemprop'] 	= 'description';
	
	return $attributes;
}

/**
 * Add attributes for header widget area element.
 *
 * @since 1.0.0
 */
add_filter( 'bizznis_attr_header-widget-area', 'bizznis_attributes_header_widget_area' );
function bizznis_attributes_header_widget_area( $attributes ) {
	$attributes['class'] 		= 'widget-area header-widget-area';
	
	return $attributes;
}

/**
 * Add attributes for breadcrumb wrapper.
 *
 * @since 1.2.0
 *
 * @param array $attributes Existing attributes.
 * @return array Ammended attributes
 */
add_filter( 'bizznis_attr_breadcrumb', 'bizznis_attributes_breadcrumb' );
function bizznis_attributes_breadcrumb( $attributes ) {
	$attributes['itemprop']  = 'breadcrumb';
	$attributes['itemscope'] = true;
	$attributes['itemtype']  = 'http://schema.org/BreadcrumbList';
	
	//* Breadcrumb itemprop not valid on blog
	if ( is_singular( 'post' ) || is_archive() || is_home() ) {
		unset( $attributes['itemprop'] );
	}

	return $attributes;
}

/**
 * Add attributes for breadcrumb wrapper.
 *
 * @since 1.2.6
 *
 * @param array $attributes Existing attributes.
 *
 * @return array Ammended attributes
 */
add_filter( 'bizznis_attr_breadcrumb-link-wrap', 'bizznis_attributes_breadcrumb_link_wrap' );
function bizznis_attributes_breadcrumb_link_wrap( $attributes ) {
	$attributes['itemprop']  = 'itemListElement';
	$attributes['itemscope'] = true;
	$attributes['itemtype']  = 'http://schema.org/ListItem';

	return $attributes;
}

/**
 * Add attributes for search form.
 *
 * @since 1.2.0
 *
 * @param array $attributes Existing attributes.
 * @return array Amended attributes.
 */
add_filter( 'bizznis_attr_search-form', 'bizznis_attributes_search_form' );
function bizznis_attributes_search_form( $attributes ) {
	$attributes['itemprop']  = 'potentialAction';
	$attributes['itemscope'] = true;
	$attributes['itemtype']  = 'http://schema.org/SearchAction';
	$attributes['method']    = 'get';
	$attributes['action']    = home_url( '/' );
	$attributes['role']      = 'search';

	return $attributes;
}

/**
 * Add attributes for top navigation element.
 *
 * @since 1.0.0
 */
add_filter( 'bizznis_attr_nav-header', 'bizznis_attributes_nav' );
add_filter( 'bizznis_attr_nav-primary', 'bizznis_attributes_nav' );
add_filter( 'bizznis_attr_nav-secondary', 'bizznis_attributes_nav' );
function bizznis_attributes_nav( $attributes ) {
	$attributes['itemscope'] 	= true;
	$attributes['itemtype']  	= 'http://schema.org/SiteNavigationElement';
	
	return $attributes;
}

/**
 * Add attributes for the span wrap around navigation item links.
 *
 * @since 1.2.2
 *
 * @param array $attributes Existing attributes.
 * @return array Amended attributes.
 */
add_filter( 'bizznis_attr_nav-link-wrap', 'bizznis_attributes_nav_link_wrap' );
function bizznis_attributes_nav_link_wrap( $attributes ) {
	$attributes['class'] = '';
	$attributes['itemprop'] = 'name';

	return $attributes;
}

/**
 * Add attributes for the navigation item links.
 *
 * @since 1.2.2
 *
 * @param array $attributes Existing attributes.
 * @return array Amended attributes.
 */
add_filter( 'bizznis_attr_nav-link', 'bizznis_attributes_nav_link' );
function bizznis_attributes_nav_link( $attributes ) {
	/**
	 * Since we're utilizing a filter that plugins might also want to filter, don't overwrite class here.
	 */
	$class = str_replace( 'nav-link', '', $attributes['class'] );

	$attributes['class']    = $class;
	$attributes['itemprop'] = 'url';

	return $attributes;
}

/**
 * Add attributes for main content element.
 *
 * @since 1.0.0
 */
add_filter( 'bizznis_attr_content', 'bizznis_attributes_content' );
function bizznis_attributes_content( $attributes ) {
	return $attributes;
}

/**
 * Add attributes for taxonomy description.
 *
 * @since 1.2.7
 * @param array $attributes Existing attributes.
 * @return array Amended attributes.
 */
add_filter( 'bizznis_attr_taxonomy-archive-description', 'bizznis_attributes_taxonomy_archive_description' );
function bizznis_attributes_taxonomy_archive_description( $attributes ) {
	$attributes['class'] = 'archive-description taxonomy-archive-description taxonomy-description';

	return $attributes;
}

/**
 * Add attributes for author description.
 *
 * @since 1.2.7
 * @param array $attributes Existing attributes.
 * @return array Amended attributes.
 */
add_filter( 'bizznis_attr_author-archive-description', 'bizznis_attributes_author_archive_description' );
function bizznis_attributes_author_archive_description( $attributes ) {
	$attributes['class'] = 'archive-description author-archive-description author-description';

	return $attributes;
}

/**
 * Add attributes for date archive description.
 *
 * @since 1.2.7
 * @param array $attributes Existing attributes.
 * @return array Amended attributes.
 */
add_filter( 'bizznis_attr_date-archive-description', 'bizznis_attributes_date_archive_description' );
function bizznis_attributes_date_archive_description( $attributes ) {
	$attributes['class'] = 'archive-description date-archive-description archive-date';

	return $attributes;
}

/**
 * Add attributes for posts page description.
 *
 * @since 1.2.7
 * @param array $attributes Existing attributes.
 * @return array Amended attributes.
 */
add_filter( 'bizznis_attr_posts-page-description', 'bizznis_attributes_posts_page_description' );
function bizznis_attributes_posts_page_description( $attributes ) {
	$attributes['class'] = 'archive-description posts-page-description';

	return $attributes;
}

/**
 * Add attributes for entry element.
 *
 * @since 1.0.0
 */
add_filter( 'bizznis_attr_entry', 'bizznis_attributes_entry' );
function bizznis_attributes_entry( $attributes ) {
	$attributes['class'] = join( ' ', get_post_class() );
	
	if ( ! is_main_query() ) {
		return $attributes;
	}
	
	# Blog posts microdata
	if ( 'post' === get_post_type() ) {
		$attributes['itemscope'] = true;
		$attributes['itemtype']  = 'http://schema.org/BlogPosting';
		# If not search results page
		if ( ! is_search() ) {
			$attributes['itemprop']  = 'blogPost';
		}
	}

	return $attributes;
}

/**
 * Add attributes for entry image element.
 *
 * @since 1.0.0
 */
add_filter( 'bizznis_attr_entry-image', 'bizznis_attributes_entry_image' );
function bizznis_attributes_entry_image( $attributes ) {
	$attributes['class']    	= bizznis_get_option( 'image_alignment' ) . ' post-image entry-image';
	$attributes['itemprop'] 	= 'image';
	
	return $attributes;
}

/**
 * Add attributes for entry image element shown in a widget.
 *
 * @since 1.0.0
 */
add_filter( 'bizznis_attr_entry-image-widget', 'bizznis_attributes_entry_image_widget' );
function bizznis_attributes_entry_image_widget( $attributes ) {
	$attributes['class']    	= 'entry-image attachment-' . get_post_type();
	$attributes['itemprop'] 	= 'image';
	
	return $attributes;
}

/**
 * Add attributes for entry image element shown in a grid loop.
 *
 * @since 1.0.0
 */
add_filter( 'bizznis_attr_entry-image-grid-loop', 'bizznis_attributes_entry_image_grid_loop' );
function bizznis_attributes_entry_image_grid_loop( $attributes ) {
	$attributes['itemprop'] 	= 'image';
	
	return $attributes;
}

/**
 * Add attributes for author element for an entry.
 *
 * @since 1.0.0
 */
add_filter( 'bizznis_attr_entry-author', 'bizznis_attributes_entry_author' );
function bizznis_attributes_entry_author( $attributes ) {
	$attributes['itemprop']  	= 'author';
	$attributes['itemscope'] 	= true;
	$attributes['itemtype']  	= 'http://schema.org/Person';
	
	return $attributes;
}

/**
 * Add attributes for entry author link element.
 *
 * @since 1.0.0
 */
add_filter( 'bizznis_attr_entry-author-link', 'bizznis_attributes_entry_author_link' );
function bizznis_attributes_entry_author_link( $attributes ) {
	$attributes['itemprop'] 	= 'url';
	$attributes['rel']      	= 'author';
	
	return $attributes;
}

/**
 * Add attributes for entry author name element.
 *
 * @since 1.0.0
 */
add_filter( 'bizznis_attr_entry-author-name', 'bizznis_attributes_entry_author_name' );
function bizznis_attributes_entry_author_name( $attributes ) {
	$attributes['itemprop'] 	= 'name';
	
	return $attributes;
}

/**
 * AAdd attributes for time element for an entry.
 *
 * @since 1.0.0
 */
add_filter( 'bizznis_attr_entry-time', 'bizznis_attributes_entry_time' );
function bizznis_attributes_entry_time( $attributes ) {
	$attributes['itemprop'] 	= 'datePublished';
	$attributes['datetime'] 	= get_the_time( 'c' );
	
	return $attributes;
}

/**
 * Add attributes for modified time element for an entry.
 *
 * @since 1.1.0
 *
 * @param array $attributes Existing attributes.
 * @return array Amended attributes.
 */
add_filter( 'bizznis_attr_entry-modified-time', 'bizznis_attributes_entry_modified_time' );
function bizznis_attributes_entry_modified_time( $attributes ) {
	$attributes['itemprop'] = 'dateModified';
	$attributes['datetime'] = get_the_modified_time( 'c' );
	
	return $attributes;

}

/**
 * Add attributes for entry title element.
 *
 * @since 1.0.0
 */
add_filter( 'bizznis_attr_entry-title', 'bizznis_attributes_entry_title' );
function bizznis_attributes_entry_title( $attributes ) {
	$attributes['itemprop'] 	= 'headline';
	
	return $attributes;
}

/**
 * Add attributes for entry content element.
 *
 * @since 1.0.0
 */
add_filter( 'bizznis_attr_entry-content', 'bizznis_attributes_entry_content' );
function bizznis_attributes_entry_content( $attributes ) {
	$attributes['itemprop'] 	= 'text';
	
	return $attributes;
}

/**
 * Add attributes for pagination.
 *
 * @since 1.0.0
 */
add_filter( 'bizznis_attr_archive-pagination', 'bizznis_attributes_pagination' );
add_filter( 'bizznis_attr_entry-pagination', 'bizznis_attributes_pagination' );
add_filter( 'bizznis_attr_adjacent-entry-pagination', 'bizznis_attributes_pagination' );
add_filter( 'bizznis_attr_comments-pagination', 'bizznis_attributes_pagination' );
function bizznis_attributes_pagination( $attributes ) {
	$attributes['class'] .= ' pagination';
	
	return $attributes;
}

/**
 * Add attributes for entry comments element.
 *
 * @since 1.0.0
 */
add_filter( 'bizznis_attr_entry-comments', 'bizznis_attributes_entry_comments' );
function bizznis_attributes_entry_comments( $attributes ) {
	$attributes['id'] 			= 'comments';
	
	return $attributes;
}

/**
 * Add attributes for single comment element.
 *
 * @since 1.0.0
 */
add_filter( 'bizznis_attr_comment', 'bizznis_attributes_comment' );
function bizznis_attributes_comment( $attributes ) {
	$attributes['class']     	= '';
	$attributes['itemprop']  	= 'comment';
	$attributes['itemscope'] 	= true;
	$attributes['itemtype']  	= 'http://schema.org/Comment';
	
	return $attributes;
}

/**
 * Add attributes for comment author element.
 *
 * @since 1.0.0
 */
add_filter( 'bizznis_attr_comment-author', 'bizznis_attributes_comment_author' );
function bizznis_attributes_comment_author( $attributes ) {
	$attributes['itemprop']  = 'author';
	$attributes['itemscope'] = true;
	$attributes['itemtype']  = 'http://schema.org/Person';
	
	return $attributes;
}

/**
 * Add attributes for comment author link element.
 *
 * @since 1.1.0
 *
 * @param array $attributes Existing attributes.
 * @return array Amended attributes.
 */
add_filter( 'bizznis_attr_comment-author-link', 'bizznis_attributes_comment_author_link' );
function bizznis_attributes_comment_author_link( $attributes ) {
	$attributes['rel']      = 'external nofollow';
	$attributes['itemprop'] = 'url';
	
	return $attributes;
}

/**
 * Add attributes for comment time element.
 *
 * @since 1.1.0
 *
 * @param array $attributes Existing attributes.
 * @return array Amended attributes.
 */
add_filter( 'bizznis_attr_comment-time', 'bizznis_attributes_comment_time' );
function bizznis_attributes_comment_time( $attributes ) {
	$attributes['datetime'] = esc_attr( get_comment_time( 'c' ) );
	$attributes['itemprop'] = 'datePublished';
	
	return $attributes;
}

/**
 * Add attributes for comment time link element.
 *
 * @since 1.1.0
 *
 * @param array $attributes Existing attributes.
 * @return array Amended attributes.
 */
add_filter( 'bizznis_attr_comment-time-link', 'bizznis_attributes_comment_time_link' );
function bizznis_attributes_comment_time_link( $attributes ) {
	$attributes['itemprop'] = 'url';
	
	return $attributes;
}

/**
 * Add attributes for comment content container.
 *
 * @since 1.1.0
 *
 * @param array $attributes Existing attributes.
 * @return array Amended attributes.
 */
add_filter( 'bizznis_attr_comment-content', 'bizznis_attributes_comment_content' );
function bizznis_attributes_comment_content( $attributes ) {
	$attributes['itemprop'] = 'text';
	
	return $attributes;
}

/**
 * Add attributes for author box element.
 *
 * @since 1.0.0
 */
add_filter( 'bizznis_attr_author-box', 'bizznis_attributes_author_box' );
function bizznis_attributes_author_box( $attributes ) {
	$attributes['itemprop']  	= 'author';
	$attributes['itemscope'] 	= true;
	$attributes['itemtype']  	= 'http://schema.org/Person';
	
	return $attributes;
}

/**
 * Add attributes for primary sidebar element.
 *
 * @since 1.0.0
 */
add_filter( 'bizznis_attr_sidebar-primary', 'bizznis_attributes_sidebar_primary' );
function bizznis_attributes_sidebar_primary( $attributes ) {
	$attributes['class']     	= 'sidebar sidebar-primary widget-area';
	$attributes['role']      	= 'complementary';
	$attributes['aria-label']	= __( 'Primary Sidebar', 'bizznis' );
	$attributes['itemscope'] 	= true;
	$attributes['itemtype']  	= 'http://schema.org/WPSideBar';
	
	return $attributes;
}

/**
 * Add attributes for secondary sidebar element.
 *
 * @since 1.0.0
 */
add_filter( 'bizznis_attr_sidebar-secondary', 'bizznis_attributes_sidebar_secondary' );
function bizznis_attributes_sidebar_secondary( $attributes ) {
	$attributes['class']     	= 'sidebar sidebar-secondary widget-area';
	$attributes['role']      	= 'complementary';
	$attributes['aria-label']	= __( 'Secondary Sidebar', 'bizznis' );
	$attributes['itemscope'] 	= true;
	$attributes['itemtype']  	= 'http://schema.org/WPSideBar';
	
	return $attributes;
}

/**
 * Add attributes for site footer element.
 *
 * @since 1.0.0
 */
add_filter( 'bizznis_attr_site-footer', 'bizznis_attributes_site_footer' );
function bizznis_attributes_site_footer( $attributes ) {
	$attributes['itemscope'] 	= true;
	$attributes['itemtype']  	= 'http://schema.org/WPFooter';
	
	return $attributes;
}

/**
 * Add ID markup to the elements to jump to
 *
 * @since 1.2.0
 */
function bizznis_skiplinks_markup() {
	add_filter( 'bizznis_attr_nav-primary', 'bizznis_skiplinks_attr_nav_primary' );
	add_filter( 'bizznis_attr_content', 'bizznis_skiplinks_attr_content' );
	add_filter( 'bizznis_attr_sidebar-primary', 'bizznis_skiplinks_attr_sidebar_primary' );
	add_filter( 'bizznis_attr_sidebar-secondary', 'bizznis_skiplinks_attr_sidebar_secondary' );
	add_filter( 'bizznis_attr_footer-widgets', 'bizznis_skiplinks_attr_footer_widgets' );
}

/**
 * Add ID markup to primary navigation
 *
 * @since 1.2.0
 *
 * @param array $attributes Existing attributes.
 * @return $attributes plus id and aria-label
 */
function bizznis_skiplinks_attr_nav_primary( $attributes ) {
	$attributes['id'] = 'bizznis-nav-primary';
	$attributes['aria-label'] = __( 'Main navigation', 'bizznis' );
	
	return $attributes;
}

/**
 * Add ID markup to content area
 *
 * @since 1.2.0
 *
 * @param array $attributes Existing attributes.
 * @return $attributes plus id
 */
function bizznis_skiplinks_attr_content( $attributes ) {
	$attributes['id'] = 'bizznis-content';
	
	return $attributes;
}

/**
 * Add ID markup to primary sidebar
 *
 * @since 1.2.0
 *
 * @param array $attributes Existing attributes.
 * @return $attributes plus id
 */
function bizznis_skiplinks_attr_sidebar_primary( $attributes ) {
	$attributes['id'] = 'bizznis-sidebar-primary';
	
	return $attributes;
}

/**
 * Add ID markup to secondary sidebar
 *
 * @since 1.2.0
 *
 * @param array $attributes Existing attributes.
 * @return $attributes plus id
 */
function bizznis_skiplinks_attr_sidebar_secondary( $attributes ) {
	$attributes['id'] = 'bizznis-sidebar-secondary';
	
	return $attributes;
}

/**
 * Add ID markup to footer widget area
 *
 * @since 1.2.0
 *
 * @param array $attributes Existing attributes.
 * @return $attributes plus id
 */
function bizznis_skiplinks_attr_footer_widgets( $attributes ) {
	$attributes['id'] = 'bizznis-footer-widgets';
	
	return $attributes;
}
