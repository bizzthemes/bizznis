<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

/**
 * Class to control breadcrumbs display.
 *
 * @since 1.0.0
 */
class Bizznis_Breadcrumb {

	# Settings array, a merge of provided values and defaults.
	protected $args = array();

	/**
	 * Constructor. Set up cacheable values and settings.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		# Default arguments
		$this->args = array(
			'home'                    => __( 'Home', 'bizznis' ),
			'sep'                     => ' / ',
			'list_sep'                => ', ',
			'prefix'                  => sprintf( '<nav %s>', bizznis_attr( 'breadcrumb' ) ),
			'suffix'                  => '</nav>',
			'heirarchial_attachments' => true,
			'heirarchial_categories'  => true,
			'labels' => array(
				'prefix'    => __( 'You are here: ', 'bizznis' ),
				'author'    => __( 'Archives for ', 'bizznis' ),
				'category'  => __( 'Archives for ', 'bizznis' ),
				'tag'       => __( 'Archives for ', 'bizznis' ),
				'date'      => __( 'Archives for ', 'bizznis' ),
				'search'    => __( 'Search for ', 'bizznis' ),
				'tax'       => __( 'Archives for ', 'bizznis' ),
				'post_type' => __( 'Archives for ', 'bizznis' ),
				'404'       => __( 'Not found ', 'bizznis' )
			)
		);
	}

	/**
	 * Return the final completed breadcrumb in markup wrapper.
	 *
	 * @since 1.0.0
	 */
	public function get_output( $args = array() ) {
		/**
		 * Filter the Bizznis breadcrumb arguments.
		 *
		 * @since 1.0.0
		 *
		 * @param array $args {
		 *      Arguments for generating breadcrumbs.
		 *
		 *      @type string $home                    Homepage link text.
		 *      @type string $sep                     Separator.
		 *      @type string $list_set                List format separator.
		 *      @type string $prefix                  Prefix before breadcrumb list.
		 *      @type string $suffix                  Suffix after breadcrump list.
		 *      @type bool   $heirarchial_attachments Whether attachments are hierarchical.
		 *      @type bool   $heirarchial_categories  Whether categories are hierarchical.
		 *      @type array $labels                   Labels including the following keys: 'prefix', 'author', 'category',
		 *                                            'tag', 'date', 'search', 'tax', 'post_type', '404'.
		 * }
		 */
		$this->args = apply_filters( 'bizznis_breadcrumb_args', wp_parse_args( $args, $this->args ) );
		return $this->args['prefix'] . $this->args['labels']['prefix'] . $this->build_crumbs() . $this->args['suffix'];
	}

	/**
	 * Echo the final completed breadcrumb in markup wrapper.
	 *
	 * @since 1.0.0
	 */
	public function output( $args = array() ) {
		echo $this->get_output( $args );
	}

	/**
	 * Return the correct crumbs for this query, combined together.
	 *
	 * @since 1.0.0
	 */
	protected function build_crumbs() {
		$crumbs[] = $this->get_home_crumb();
		if ( is_home() ) {
			$crumbs[] = $this->get_blog_crumb();
		}
		elseif ( is_search() ) {
			$crumbs[] = $this->get_search_crumb();
		}
		elseif ( is_404() ) {
			$crumbs[] = $this->get_404_crumb();
		}
		elseif ( is_page() ) {
			$crumbs[] = $this->get_page_crumb();
		}
		elseif ( is_archive() ) {
			$crumbs[] = $this->get_archive_crumb();
		}
		elseif ( is_singular() ) {
			$crumbs[] = $this->get_single_crumb();
		}
		/**
		 * Filter the Bizznis breadcrumbs.
		 *
		 * @since 1.1.0
		 *
		 * @param string $crumbs HTML markup for the breadcrumbs.
		 * @param array  $args   Arguments used to generate the breadcrumbs. Documented in Bizznis_Breadcrumbs::get_output().
		 */
		$crumbs = apply_filters( 'bizznis_build_crumbs', $crumbs, $this->args );

		return join( $this->args['sep'], array_filter( array_unique( $crumbs ) ) );
	}

	/**
	 * Return archive breadcrumb.
	 *
	 * @since 1.0.0
	 */
	protected function get_archive_crumb() {
		if ( is_category() ) {
			$crumb = $this->get_category_crumb();
		}
		elseif ( is_tag() ) {
			$crumb = $this->get_tag_crumb();
		}
		elseif ( is_tax() ) {
			$crumb = $this->get_tax_crumb();
		}
		elseif ( is_year() ) {
			$crumb = $this->get_year_crumb();
		}
		elseif ( is_month() ) {
			$crumb = $this->get_month_crumb();
		}
		elseif ( is_day() ) {
			$crumb = $this->get_day_crumb();
		}
		elseif ( is_author() ) {
			$crumb = $this->get_author_crumb();
		}
		elseif ( is_post_type_archive() ) {
			$crumb = $this->get_post_type_crumb();
		}
		/**
		 * Filter the Bizznis archive breadcrumb.
		 *
		 * @since 1.0.0
		 *
		 * @param string $crumb HTML markup for the archive breadcrumb.
		 * @param array  $args  Arguments used to generate the breadcrumbs. Documented in Bizznis_Breadcrumbs::get_output().
		 */
		return apply_filters( 'bizznis_archive_crumb', $crumb, $this->args );
	}

	/**
	 * Get single breadcrumb, including any parent crumbs.
	 *
	 * @since 1.0.0
	 */
	protected function get_single_crumb() {
		if ( is_attachment() ) {
			$crumb = $this->get_attachment_crumb();
		}
		elseif ( is_singular( 'post' ) ) {
			$crumb = $this->get_post_crumb();
		}
		else {
			$crumb = $this->get_cpt_crumb();
		}
		/**
		 * Filter the Bizznis single breadcrumb.
		 *
		 * @since 1.1.0
		 *
		 * @param string $crumb HTML markup for the single breadcrumb.
		 * @param array  $args  Arguments used to generate the breadcrumbs. Documented in Bizznis_Breadcrumbs::get_output().
		 */
		return apply_filters( 'bizznis_single_crumb', $crumb, $this->args );
	}

	/**
	 * Return home breadcrumb.
	 *
	 * @since 1.0.0
	 */
	protected function get_home_crumb() {
		$url   = $this->page_shown_on_front() ? get_permalink( get_option( 'page_on_front' ) ) : trailingslashit( home_url() );
		$crumb = ( is_home() && is_front_page() ) ? $this->args['home'] : $this->get_breadcrumb_link( $url, '', $this->args['home'] );
		/**
		 * Filter the Bizznis home breadcrumb.
		 *
		 * @since 1.0.0
		 *
		 * @param string $crumb HTML markup for the home breadcrumb.
		 * @param array  $args  Arguments used to generate the breadcrumbs. Documented in Bizznis_Breadcrumbs::get_output().
		 */
		return apply_filters( 'bizznis_home_crumb', $crumb, $this->args );
	}

	/**
	 * Return blog posts page breadcrumb.
	 *
	 * @since 1.0.0
	 */
	protected function get_blog_crumb() {
		$crumb = $this->get_home_crumb();
		if ( $this->page_shown_on_front() ) {
			$crumb = get_the_title( get_option( 'page_for_posts' ) );
		}
		/**
		 * Filter the Bizznis blog posts breadcrumb.
		 *
		 * @since 1.0.0
		 *
		 * @param string $crumb HTML markup for the blog posts breadcrumb.
		 * @param array  $args  Arguments used to generate the breadcrumbs. Documented in Bizznis_Breadcrumbs::get_output().
		 */
		return apply_filters( 'bizznis_blog_crumb', $crumb, $this->args );
	}

	/**
	 * Return search results page breadcrumb.
	 *
	 * @since 1.0.0
	 */
	protected function get_search_crumb() {
		$crumb = $this->args['labels']['search'] . '"' . esc_html( apply_filters( 'the_search_query', get_search_query() ) ) . '"';
		/**
		 * Filter the Search page breadcrumb.
		 *
		 * @since 1.0.0
		 *
		 * @param string $crumb HTML markup for the search page breadcrumb.
		 * @param array  $args  Arguments used to generate the breadcrumbs. Documented in Bizznis_Breadcrumbs::get_output().
		 */
		return apply_filters( 'bizznis_search_crumb', $crumb, $this->args );
	}

	/**
	 * Return 404 (page not found) breadcrumb.
	 *
	 * @since 1.0.0
	 */
	protected function get_404_crumb() {
		$crumb = $this->args['labels']['404'];
		/**
		 * Filter the 404 page breadcrumb.
		 *
		 * @since 1.0.0
		 *
		 * @param string $crumb HTML markup for the 404 page breadcrumb.
		 * @param array  $args  Arguments used to generate the breadcrumbs. Documented in Bizznis_Breadcrumbs::get_output().
		 */
		return apply_filters( 'bizznis_404_crumb', $crumb, $this->args );
	}

	/**
	 * Return content page breadcrumb.
	 *
	 * @since 1.0.0
	 */
	protected function get_page_crumb() {
		if ( $this->page_shown_on_front() && is_front_page() ) {
			 # Don't do anything - we're on the front page and we've already dealt with that elsewhere
			$crumb = $this->get_home_crumb(); 
		} else {
			$post = get_queried_object();
			# If this is a top level Page, it's simple to output the breadcrumb
			if ( 0 == $post->post_parent ) {
				$crumb = get_the_title();
			} else {
				if ( isset( $post->ancestors ) ) {
					if ( is_array( $post->ancestors ) ) {
						$ancestors = array_values( $post->ancestors );
					}
					else {
						$ancestors = array( $post->ancestors );
					}
				} else {
					$ancestors = array( $post->post_parent );
				}
				$crumbs = array();
				foreach ( $ancestors as $ancestor ) {
					array_unshift(
						$crumbs,
						$this->get_breadcrumb_link(
							get_permalink( $ancestor ),
							'',
							get_the_title( $ancestor )
						)
					);
				}
				# Add the current page title
				$crumbs[] = get_the_title( $post->ID );
				$crumb = join( $this->args['sep'], $crumbs );
			}
		}
		/**
		 * Filter the content page breadcrumb.
		 *
		 * @since 1.0.0
		 *
		 * @param string $crumb HTML markup for the content page breadcrumb.
		 * @param array  $args  Arguments used to generate the breadcrumbs. Documented in Bizznis_Breadcrumbs::get_output().
		 */
		return apply_filters( 'bizznis_page_crumb', $crumb, $this->args );
	}

	/**
	 * Get breadcrumb for single attachment, including any parent crumbs.
	 *
	 * @since 1.0.0
	 */
	protected function get_attachment_crumb() {
		$post = get_post();
		$crumb = '';
		if ( $this->args['heirarchial_attachments'] ) {
			# If showing attachment parent
			$attachment_parent = get_post( $post->post_parent );
			$crumb = $this->get_breadcrumb_link(
				get_permalink( $post->post_parent ),
				'',
				$attachment_parent->post_title,
				$this->args['sep']
			);
		}
		$crumb .= single_post_title( '', false );
		return $crumb;
	}

	/**
	 * Get breadcrumb for single post, including any parent (category) crumbs.
	 *
	 * @since 1.0.0
	 */
	protected function get_post_crumb() {
		$categories = get_the_category();
		if ( 1 == count( $categories ) ) {
			# If in single category, show it, and any parent categories
			$crumb = $this->get_term_parents( $categories[0]->cat_ID, 'category', true ) . $this->args['sep'];
		}
		if ( count( $categories ) > 1 ) {
			if ( ! $this->args['heirarchial_categories'] ) {
				# Don't show parent categories (unless the post happen to be explicitly in them)
				foreach ( $categories as $category ) {
					$crumbs[] = $this->get_breadcrumb_link(
						get_category_link( $category->term_id ),
						'',
						$category->name
					);
				}
				$crumb = join( $this->args['list_sep'], $crumbs ) . $this->args['sep'];
			} else {
				# Show parent categories - see if one is marked as primary and try to use that
				$primary_category_id = get_post_meta( get_the_ID(), '_category_permalink', true ); # support for sCategory Permalink plugin
				if ( $primary_category_id ) {
					$crumb = $this->get_term_parents( $primary_category_id, 'category', true ) . $this->args['sep'];
				} else {
					$crumb = $this->get_term_parents( $categories[0]->cat_ID, 'category', true ) . $this->args['sep'];
				}
			}
		}
		$crumb .= single_post_title( '', false );
		return $crumb;
	}

	/**
	 * Get breadcrumb for single custom post type entry, including any parent (CPT name) crumbs.
	 *
	 * @since 1.0.0
	 */
	protected function get_cpt_crumb() {
		$post_type = get_query_var( 'post_type' );
		$post_type_object = get_post_type_object( $post_type );
		if ( $cpt_archive_link = get_post_type_archive_link( $post_type ) ) {
			$crumb = $this->get_breadcrumb_link(
				$cpt_archive_link,
				'',
				$post_type_object->labels->name
			);
		} else {
			$crumb = $post_type_object->labels->name;
		}
		$crumb .= $this->args['sep'] . single_post_title( '', false );
		return $crumb;
	}

	/**
	 * Return the category archive crumb.
	 *
	 * @since 1.0.0
	 */
	protected function get_category_crumb() {
		$crumb = $this->args['labels']['category'] . $this->get_term_parents( get_query_var( 'cat' ), 'category' );
		/**
		 * Filter the category archive breadcrumb.
		 *
		 * @since 1.0.0
		 *
		 * @param string $crumb HTML markup for the category archive crumb.
		 * @param array  $args  Arguments used to generate the breadcrumbs. Documented in Bizznis_Breadcrumbs::get_output().
		 */
		return apply_filters( 'bizznis_category_crumb', $crumb, $this->args );
	}

	/**
	 * Return the tag archive crumb.
	 *
	 * @since 1.0.0
	 */
	protected function get_tag_crumb() {
		$crumb = $this->args['labels']['tag'] . single_term_title( '', false );
		/**
		 * Filter the tag archive breadcrumb.
		 *
		 * @since 1.0.0
		 *
		 * @param string $crumb HTML markup for the tag archive crumb.
		 * @param array  $args  Arguments used to generate the breadcrumbs. Documented in Bizznis_Breadcrumbs::get_output().
		 */
		return apply_filters( 'bizznis_tag_crumb', $crumb, $this->args );
	}

	/**
	 * Return the taxonomy archive crumb.
	 *
	 * @since 1.0.0
	 */
	protected function get_tax_crumb() {
		$term  = get_queried_object();
		$crumb = $this->args['labels']['tax'] . $this->get_term_parents( $term->term_id, $term->taxonomy );
		/**
		 * Filter the taxonomy archive breadcrumb.
		 *
		 * @since 1.0.0
		 *
		 * @param string $crumb HTML markup for the taxonomy archive breadcrumb.
		 * @param array  $args  Arguments used to generate the breadcrumbs. Documented in Bizznis_Breadcrumbs::get_output().
		 */
		return apply_filters( 'bizznis_tax_crumb', $crumb, $this->args );

	}

	/**
	 * Return the year archive crumb.
	 *
	 * @since 1.0.0
	 */
	protected function get_year_crumb() {
		$crumb = $this->args['labels']['date'] . get_query_var( 'year' );
		/**
		 * Filter the year archive breadcrumb.
		 *
		 * @since 1.0.0
		 *
		 * @param string $crumb HTML markup for the year archive breadcrumb.
		 * @param array  $args  Arguments used to generate the breadcrumbs. Documented in Bizznis_Breadcrumbs::get_output().
		 */
		return apply_filters( 'bizznis_year_crumb', $crumb, $this->args );
	}

	/**
	 * Return the month archive crumb.
	 *
	 * @since 1.0.0
	 */
	protected function get_month_crumb() {
		$year = get_query_var( 'm' ) ? mb_substr( get_query_var( 'm' ), 0, 4 ) : get_query_var( 'year' );
		$crumb = $this->get_breadcrumb_link(
			get_year_link( $year ),
			'',
			$year,
			$this->args['sep']
		);
		$crumb .= $this->args['labels']['date'] . single_month_title( ' ', false );
		/**
		 * Filter the month archive breadcrumb.
		 *
		 * @since 1.0.0
		 *
		 * @param string $crumb HTML markup for the month archive breadcrumb.
		 * @param array  $args  Arguments used to generate the breadcrumbs. Documented in Bizznis_Breadcrumbs::get_output().
		 */
		return apply_filters( 'bizznis_month_crumb', $crumb, $this->args );
	}

	/**
	 * Return the day archive crumb.
	 *
	 * @since 1.0.0
	 */
	protected function get_day_crumb() {
		global $wp_locale;
		$year  = get_query_var( 'm' ) ? mb_substr( get_query_var( 'm' ), 0, 4 ) : get_query_var( 'year' );
		$month = get_query_var( 'm' ) ? mb_substr( get_query_var( 'm' ), 4, 2 ) : get_query_var( 'monthnum' );
		$day   = get_query_var( 'm' ) ? mb_substr( get_query_var( 'm' ), 6, 2 ) : get_query_var( 'day' );
		$crumb  = $this->get_breadcrumb_link(
			get_year_link( $year ),
			'',
			$year,
			$this->args['sep']
		);
		$crumb .= $this->get_breadcrumb_link(
			get_month_link( $year, $month ),
			'',
			$wp_locale->get_month( $month ),
			$this->args['sep']
		);
		$crumb .= $this->args['labels']['date'] . get_query_var( 'day' ) . date( 'S', mktime( 0, 0, 0, 1, get_query_var( 'day' ) ) );
		/**
		 * Filter the day archive breadcrumb.
		 *
		 * @since 1.0.0
		 *
		 * @param string $crumb HTML markup for the day archive breadcrumb.
		 * @param array  $args  Arguments used to generate the breadcrumbs. Documented in Bizznis_Breadcrumbs::get_output().
		 */
		return apply_filters( 'bizznis_day_crumb', $crumb, $this->args );
	}

	/**
	 * Return the author archive crumb.
	 *
	 * @since 1.0.0
	 */
	protected function get_author_crumb() {
		global $wp_query;
		$crumb = $this->args['labels']['author'] . esc_html( $wp_query->queried_object->display_name );
		/**
		 * Filter the author archive breadcrumb.
		 *
		 * @since 1.0.0
		 *
		 * @param string $crumb HTML markup for the author archive crumb.
		 * @param array  $args  Arguments used to generate the breadcrumbs. Documented in Bizznis_Breadcrumbs::get_output().
		 */
		return apply_filters( 'bizznis_author_crumb', $crumb, $this->args );
	}

	/**
	 * Return the post type archive crumb.
	 *
	 * @since 1.0.0
	 */
	protected function get_post_type_crumb() {
		$crumb = $this->args['labels']['post_type'] . esc_html( post_type_archive_title( '', false ) );
		/**
		 * Filter the post type archive breadcrumb.
		 *
		 * @since 1.0.0
		 *
		 * @param string $crumb HTML markup for the post type archive breadcrumb.
		 * @param array  $args  Arguments used to generate the breadcrumbs. Documented in Bizznis_Breadcrumbs::get_output().
		 */
		return apply_filters( 'bizznis_post_type_crumb', $crumb, $this->args );
	}

	/**
	 * Return recursive linked crumbs of category, tag or custom taxonomy parents.
	 *
	 * @since 1.0.0
	 */
	protected function get_term_parents( $parent_id, $taxonomy, $link = false, array $visited = array() ) {
		$parent = get_term( (int)$parent_id, $taxonomy );
		if ( is_wp_error( $parent ) ) {
			return array();
		}
		if ( $parent->parent && ( $parent->parent != $parent->term_id ) && ! in_array( $parent->parent, $visited ) ) {
			$visited[] = $parent->parent;
			$chain[]   = $this->get_term_parents( $parent->parent, $taxonomy, true, $visited );
		}
		if ( $link && !is_wp_error( get_term_link( get_term( $parent->term_id, $taxonomy ), $taxonomy ) ) ) {
			$chain[] = $this->get_breadcrumb_link(
				get_term_link( get_term( $parent->term_id, $taxonomy ), $taxonomy ),
				'',
				$parent->name
			);
		} else {
			$chain[] = $parent->name;
		}
		return join( $this->args['sep'], $chain );
	}

	/**
	 * Return anchor link for a single crumb.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url URL for href attribute
	 * @param string $title title attribute (unused)
	 * @param string $content linked content
	 * @param string $sep Separator
	 * @return string HTML markup for anchor link and optional separator.
	 */
	protected function get_breadcrumb_link( $url, $title, $content, $sep = false ) {
		$title = $title ? ' title="' . esc_attr( $title ) . '"' : '';
		$link = sprintf( '<a href="%s"%s>%s</a>', esc_attr( $url ), $title, esc_html( $content ) );
		/**
		 * Filter the anchor link for a single breadcrumb.
		 *
		 * @since 1.0.0
		 *
		 * @param string $link    HTML markup for anchor link, before optional separator is added.
		 * @param string $url     URL for href attribute.
		 * @param string $title   Title attribute.
		 * @param string $content Link content.
		 * @param array  $args    Arguments used to generate the breadcrumbs. Documented in Bizznis_Breadcrumbs::get_output().
		 */
		$link = apply_filters( 'bizznis_breadcrumb_link', $link, $url, $title, $content, $this->args );
		if ( $sep ) {
			$link .= $sep;
		}
		return $link;

	}

	/**
	 * Determine if static page is shown on front page.
	 *
	 * @since 1.0.0
	 */
	protected function page_shown_on_front() {
		return 'page' == get_option( 'show_on_front' );
	}

}

/**
* Helper function for the Bizznis Breadcrumb Class.
 *
 * @since 1.0.0
 */
function bizznis_breadcrumb( $args = array() ) {
	global $_bizznis_breadcrumb;
	if ( ! $_bizznis_breadcrumb ) {
		$_bizznis_breadcrumb = new Bizznis_Breadcrumb;
	}
	$_bizznis_breadcrumb->output( $args );
}

/**
 * Display Breadcrumbs above the Loop.
 * Concedes priority to popular breadcrumb plugins.
 *
 * @since 1.0.0
 */
add_action( 'bizznis_loop', 'bizznis_do_breadcrumbs', 4 );
function bizznis_do_breadcrumbs() {
	if (
		( ( 'posts' == get_option( 'show_on_front' ) && is_home() ) && ! bizznis_get_option( 'breadcrumb_home' ) ) ||
		( ( 'page' == get_option( 'show_on_front' ) && is_front_page() ) && ! bizznis_get_option( 'breadcrumb_front_page' ) ) ||
		( ( 'page' == get_option( 'show_on_front' ) && is_home() ) && ! bizznis_get_option( 'breadcrumb_posts_page' ) ) ||
		( is_single() && ! bizznis_get_option( 'breadcrumb_single' ) ) ||
		( is_page() && ! bizznis_get_option( 'breadcrumb_page' ) ) ||
		( ( is_archive() || is_search() ) && ! bizznis_get_option( 'breadcrumb_archive' ) ) ||
		( is_404() && ! bizznis_get_option( 'breadcrumb_404' ) ) ||
		( is_attachment() && ! bizznis_get_option( 'breadcrumb_attachment' ) )
	) {
		return;
	}
	# plugins
	if ( function_exists( 'bcn_display' ) ) {
		echo '<div class="breadcrumb" itemprop="breadcrumb">'. bcn_display() .'</div>';
	}
	elseif ( function_exists( 'yoast_breadcrumb' ) ) {
		yoast_breadcrumb( '<div class="breadcrumb">', '</div>' );
	}
	elseif ( function_exists( 'breadcrumbs' ) ) {
		breadcrumbs();
	}
	elseif ( function_exists( 'crumbs' ) ) {
		crumbs();
	}
	else {
		bizznis_breadcrumb(); # native breadcrumbs
	}
}