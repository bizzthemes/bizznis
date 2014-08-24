<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

add_action( 'bizznis_after_entry', 'bizznis_get_comments_template' );
/**
 * Output the comments at the end of entries.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_get_comments_template' ) ) :
function bizznis_get_comments_template() {
	# Stop here if comments are off for this post type
	if ( ! post_type_supports( get_post_type(), 'comments' ) ) {
		return;
	}
	if ( is_singular() && ! in_array( get_post_type(), array( 'post', 'page' ) ) ) {
		comments_template( '', true );
	}
	elseif ( is_singular( 'post' ) && ( bizznis_get_option( 'trackbacks_posts' ) || bizznis_get_option( 'comments_posts' ) ) ) {
		comments_template( '', true );
	}
	elseif ( is_singular( 'page' ) && ( bizznis_get_option( 'trackbacks_pages' ) || bizznis_get_option( 'comments_pages' ) ) ) {
		comments_template( '', true );
	}
}
endif;

add_action( 'bizznis_comments', 'bizznis_do_comments', 5 );
/**
 * Echo Bizznis default comment structure.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_do_comments' ) ) :
function bizznis_do_comments() {
	global $wp_query;
	# Stop here if comments are off for this post type
	if ( ( is_page() && ! bizznis_get_option( 'comments_pages' ) ) || ( is_single() && ! bizznis_get_option( 'comments_posts' ) ) ) {
		return;
	}
	if ( have_comments() && ! empty( $wp_query->comments_by_type['comment'] ) ) {
		printf( '<div %s>', bizznis_attr( 'entry-comments' ) );
		echo apply_filters( 'bizznis_title_comments', __( '<h3>Comments</h3>', 'bizznis' ) );
		printf( '<ol %s>', bizznis_attr( 'comment-list' ) );
			do_action( 'bizznis_list_comments' );
		echo '</ol>';
		# Comment Navigation
		$prev_link = get_previous_comments_link( apply_filters( 'bizznis_prev_comments_link_text', '' ) );
		$next_link = get_next_comments_link( apply_filters( 'bizznis_next_comments_link_text', '' ) );
		if ( $prev_link || $next_link ) {
			printf( '<nav %s>', bizznis_attr( 'comments-pagination' ) );
			printf( '<div class="pagination-previous alignleft">%s</div>', $prev_link );
			printf( '<div class="pagination-next alignright">%s</div>', $next_link );
			echo '</nav>';
		}
		echo '</div>';
	}
	# No comments so far
	elseif ( 'open' == get_post()->comment_status && $no_comments_text = apply_filters( 'bizznis_no_comments_text', '' ) ) {
		echo sprintf( '<div %s>', bizznis_attr( 'entry-comments' ) ) . $no_comments_text . '</div>';
	}
	elseif ( $comments_closed_text = apply_filters( 'bizznis_comments_closed_text', '' ) ) {
		echo sprintf( '<div %s>', bizznis_attr( 'entry-comments' ) ) . $comments_closed_text . '</div>';
	}
}
endif;

add_action( 'bizznis_comments', 'bizznis_do_pings', 10 );
/**
 * Echo Bizznis default trackback structure.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_do_pings' ) ) :
function bizznis_do_pings() {
	global $wp_query;
	# If have pings
	if ( have_comments() && !empty( $wp_query->comments_by_type['pings'] ) ) {
		printf( '<div %s>', bizznis_attr( 'entry-pings' ) );
		echo apply_filters( 'bizznis_title_pings', __( '<h3>Trackbacks</h3>', 'bizznis' ) );
		echo '<ol class="ping-list">';
			do_action( 'bizznis_list_pings' );
		echo '</ol>';
		echo '</div>';
	}
	else {
		echo apply_filters( 'bizznis_no_pings_text', '' );
	}
}
endif;

add_action( 'bizznis_list_pings', 'bizznis_default_list_pings' );
/**
 * Output the list of trackbacks.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_default_list_pings' ) ) :
function bizznis_default_list_pings() {
	$args = apply_filters( 'bizznis_ping_list_args', array(
		'type' => 'pings',
	) );
	wp_list_comments( $args );
}
endif;

add_action( 'bizznis_list_comments', 'bizznis_default_list_comments' );
/**
 * Output the list of comments.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_default_list_comments' ) ) :
function bizznis_default_list_comments() {
	$defaults = array(
		'type'        => 'comment',
		'avatar_size' => 52,
		'format'      => 'html5', # Not necessary, but a good example
		'callback'    => 'bizznis_comment_callback',
	);
	$args = apply_filters( 'bizznis_comment_list_args', $defaults );
	wp_list_comments( $args );
}
endif;

/**
 * Does 'bizznis_before_comment' and 'bizznis_after_comment' actions.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_comment_callback' ) ) :
function bizznis_comment_callback( $comment, array $args, $depth ) {
	$GLOBALS['comment'] = $comment; ?>
	<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
	<article <?php echo bizznis_attr( 'comment' ); ?>>
		<?php do_action( 'bizznis_before_comment' ); ?>
		<div class="comment-avatar">
			<?php echo get_avatar( $comment, $args['avatar_size'] ); ?>
		</div>
		<div class="comment-body">
			<header <?php echo bizznis_attr( 'comment-header' ); ?>>
				<p <?php echo bizznis_attr( 'comment-author' ); ?>>
					<?php
					$author = get_comment_author();
					$url    = get_comment_author_url();
					if ( ! empty( $url ) && 'http://' !== $url ) {
						$author = sprintf( '<a href="%s" %s>%s</a>', esc_url( $url ), bizznis_attr( 'comment-author-link' ), $author );
					}
					/**
					 * Filter the "comment author says" text.
					 *
					 * Allows developer to filter the "comment author says" text so it can say something different, or nothing at all.
					 *
					 * @since 1.1.0
					 * 
					 * @param string $text Comment author says text.
					 */
					$comment_author_says_text = apply_filters( 'comment_author_says_text', __( 'says', 'bizznis' ) );
					printf( '<span itemprop="name">%s</span> <span class="says">%s</span>', $author, $comment_author_says_text );
					?>
				</p>
				<p <?php echo bizznis_attr( 'comment-meta' ); ?>>
				<?php				
					printf( '<time %s>', bizznis_attr( 'comment-time' ) );
					printf( '<a href="%s" %s>', esc_url( get_comment_link( $comment->comment_ID ) ), bizznis_attr( 'comment-time-link' ) );
					echo    esc_html( get_comment_date() ) . ' ' . __( 'at', 'bizznis' ) . ' ' . esc_html( get_comment_time() );
					echo    '</a></time>';
					edit_comment_link( __( '(Edit)', 'bizznis' ), ' ' );
					?>
				</p>
			</header>
			<div <?php echo bizznis_attr( 'comment-content' ); ?>>
				<?php if ( ! $comment->comment_approved ) : ?>
					<?php
					/**
					 * Filter the "comment awaiting moderation" text.
					 *
					 * Allows developer to filter the "comment awaiting moderation" text so it can say something different, or nothing at all.
					 *
					 * @since unknown
					 * 
					 * @param string $text Comment awaiting moderation text.
					 */
					$comment_awaiting_moderation_text = apply_filters( 'bizznis_comment_awaiting_moderation', __( 'Your comment is awaiting moderation.', 'bizznis' ) );
					?>
					<p class="alert"><?php echo $comment_awaiting_moderation_text; ?></p>
				<?php endif; ?>
				<?php comment_text(); ?>
			</div>
			<?php
			comment_reply_link( array_merge( $args, array(
				'depth'  => $depth,
				'before' => sprintf( '<div %s>', bizznis_attr( 'comment-reply' ) ),
				'after'  => '</div>',
			) ) );
			?>
		</div>
		<?php do_action( 'bizznis_after_comment' ); ?>
	</article>
	<?php
	# No ending </li> tag because of comment threading
}
endif;

add_action( 'bizznis_comments', 'bizznis_do_comment_form', 15 );
/**
 * Optionally show the comment form.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_do_comment_form' ) ) :
function bizznis_do_comment_form() {
	# Stop here if comments are closed for this post type
	if ( ( is_page() && ! bizznis_get_option( 'comments_pages' ) ) || ( is_single() && ! bizznis_get_option( 'comments_posts' ) ) ) {
		return;
	}
	comment_form( array( 'format' => 'html5' ) );
}
endif;

/**
 * Filter the default comment form arguments, used by 'comment_form()'.
 *
 * @since 1.0.0
 */
// add_filter( 'comment_form_defaults', 'bizznis_comment_form_args' );
if ( ! function_exists( 'bizznis_comment_form_args' ) ) :
function bizznis_comment_form_args( array $defaults ) {
	global $user_identity;
	$commenter = wp_get_current_commenter();
	$req       = get_option( 'require_name_email' );
	$aria_req  = ( $req ? ' aria-required="true"' : '' );
	$author = '<div class="form-group comment-form-author">' .
			  '<label for="author" class="control-label">' . __( 'Name', 'bizznis' ) .
	          ( $req ? ' <span class="text-danger required">*</span>' : '' ) .
			  '</label>' .
	          '<input id="author" class="form-control input-lg" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" tabindex="1"' . $aria_req . ' />' .
	          '</div>';
	$email = '<div class="form-group comment-form-email">' .
			 '<label for="email">' . __( 'Email', 'bizznis' ) .
	         ( $req ? ' <span class="text-danger required">*</span>' : '' ) .
			 '</label>' .
	         '<input id="email" class="form-control input-lg" name="email" type="text" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" tabindex="2"' . $aria_req . ' />' .
	         '</div>';
	$url = '<div class="form-group comment-form-url">' .
		   '<label for="url" class="control-label">' . __( 'Website', 'bizznis' ) . '</label>' .
	       '<input id="url" class="form-control input-lg" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" tabindex="3" />' .
	       '</div>';
	$comment_field = '<div class="comment-form-comment">' .
	                 '<textarea id="comment" class="form-control" name="comment" cols="45" rows="8" tabindex="4" aria-required="true"></textarea>' .
	                 '</div>';
	$args = array(
		'comment_field'        => $comment_field,
		'title_reply'          => __( 'Leave a Reply', 'bizznis' ),
		'comment_notes_before' => '',
		'comment_notes_after'  => '',
		'fields'               => array(
			'author' => $author,
			'email'  => $email,
			'url'    => $url,
		),
	);
	# Merge $args with $defaults
	$args = wp_parse_args( $args, $defaults );
	# Return filterable array of $args, along with other optional variables
	return apply_filters( 'bizznis_comment_form_args', $args, $user_identity, get_the_ID(), $commenter, $req, $aria_req );
}
endif;

add_filter( 'get_comments_link', 'bizznis_comments_link_filter', 10, 2 );
/**
 * Filter the comments link. If post has comments, link to #comments div. If no, link to #respond div.
 * 
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_comments_link_filter' ) ) :
function bizznis_comments_link_filter( $link, $post_id ) {
	if ( 0 == get_comments_number() ) {
		return get_permalink( $post_id ) . '#respond';
	}
	return $link;
}
endif;