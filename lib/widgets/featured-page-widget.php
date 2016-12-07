<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

/**
 * Bizznis Featured Page widget class.
 *
 * @since 1.0.0
 */
if ( ! class_exists( 'Bizznis_Featured_Page' ) ) :
class Bizznis_Featured_Page extends WP_Widget {

	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @since 1.0.0
	 */
	protected $defaults;

	/**
	 * Constructor. Set the default widget options and create widget.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		$this->defaults = array(
			'title'           => '',
			'page_id'         => '',
			'show_image'      => 0,
			'image_alignment' => '',
			'image_size'      => '',
			'show_title'      => 0,
			'show_content'    => 0,
			'content_limit'   => '',
			'more_text'       => '',
		);
		$widget_ops = array(
			'classname'   => 'featured-content featuredpage',
			'description' => __( 'Displays featured page with thumbnails', 'bizznis' ),
		);
		$control_ops = array(
			'id_base' => 'featured-page',
		);
		parent::__construct( 'featured-page', __( 'Bizznis - Featured Page', 'bizznis' ), $widget_ops, $control_ops );
	}

	/**
	 * Echo the widget content.
	 *
	 * @since 1.0.0
	 *
	 * @global WP_Query $wp_query Query object.
	 * @global int      $more
	 *
	 * @param array $args     Display arguments including `before_title`, `after_title`,
	 *                        `before_widget`, and `after_widget`.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	function widget( $args, $instance ) {
		global $wp_query;
		
		// Merge with defaults.
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		
		echo $args['before_widget'];
		
		// Set up the title.
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $args['after_title'];
		}
		
		$wp_query = new WP_Query( array( 'page_id' => $instance['page_id'] ) );
		if ( have_posts() ) : while ( have_posts() ) : the_post();
			printf( '<article %s>', bizznis_attr( 'entry', array( 'class' => implode( ' ', get_post_class() ) ) ) );
			$image = bizznis_get_image( array(
				'format'  => 'html',
				'size'    => $instance['image_size'],
				'context' => 'featured-page-widget',
				'attr'    => bizznis_parse_attr( 'entry-image-widget', array ( 'alt' => get_the_title() ) ),
			) );
			
			if ( $instance['show_image'] && $image ) {
				$role = empty( $instance['show_title'] ) ? '' : 'aria-hidden="true"';
				printf( '<a href="%s" class="%s" %s>%s</a>', get_permalink(), esc_attr( $instance['image_alignment'] ), $role, wp_make_content_images_responsive( $image ) );
			}
			
			if ( ! empty( $instance['show_title'] ) ) {
				$title = get_the_title() ? get_the_title() : __( '(no title)', 'bizznis' );
				/**
				 * Filter the featured page widget title.
				 *
				 * @since  1.2.0
				 *
				 * @param string $title    Featured page title.
				 * @param array  $instance {
				 *     Widget settings for this instance.
				 *
				 *     @type string $title           Widget title.
				 *     @type int    $page_id         ID of the featured page.
				 *     @type bool   $show_image      True if featured image should be shown, false
				 *                                   otherwise.
				 *     @type string $image_alignment Image alignment: alignnone, alignleft,
				 *                                   aligncenter or alignright.
				 *     @type string $image_size      Name of the image size.
				 *     @type bool   $show_title      True if featured page title should be shown,
				 *                                   false otherwise.
				 *     @type bool   $show_content    True if featured page content should be shown,
				 *                                   false otherwise.
				 *     @type int    $content_limit   Amount of content to show, in characters.
				 *     @type int    $more_text       Text to use for More link.
				 * }
				 * @param array  $args     {
				 *     Widget display arguments.
				 *
				 *     @type string $before_widget Markup or content to display before the widget.
				 *     @type string $before_title  Markup or content to display before the widget title.
				 *     @type string $after_title   Markup or content to display after the widget title.
				 *     @type string $after_widget  Markup or content to display after the widget.
				 * }
				 */
				$title = apply_filters( 'bizznis_featured_page_title', $title, $instance, $args );
				$heading = bizznis_a11y( 'headings' ) ? 'h4' : 'h2';
				printf( '<header class="entry-header"><%s class="entry-title"><a href="%s">%s</a></%s></header>', $heading, get_permalink(), $title, $heading );
			}
			if ( ! empty( $instance['show_content'] ) ) {
				printf( '<div %s>', bizznis_attr( 'entry-content' ) );
				if ( empty( $instance['content_limit'] ) ) {
					global $more;
					$orig_more = $more;
					$more = 0;
					the_content( bizznis_a11y_more_link( $instance['more_text'] ) );
					$more = $orig_more;
				} else {
					the_content_limit( (int) $instance['content_limit'], bizznis_a11y_more_link( esc_html( $instance['more_text'] ) ) );
				}
				echo '</div>';
			}
			echo  '</article>';
			endwhile;
		endif;
		
		// Restore original query.
		wp_reset_query();
		
		echo $args['after_widget'];
	}

	/**
	 * Update a particular instance.
	 *
	 * This function should check that `$new_instance` is set correctly.
	 * The newly calculated value of $instance should be returned.
	 * If "false" is returned, the instance won't be saved/updated.
	 *
	 * @since 1.0.0
	 *
	 * @param array $new_instance New settings for this instance as input by the user via `form()`.
	 * @param array $old_instance Old settings for this instance.
	 * @return array Settings to save or bool false to cancel saving.
	 */
	function update( $new_instance, $old_instance ) {
		$new_instance['title']     = strip_tags( $new_instance['title'] );
		$new_instance['more_text'] = strip_tags( $new_instance['more_text'] );
		return $new_instance;
	}

	/**
	 * Echo the settings update form.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance Current settings.
	 * @return void
	 */
	function form( $instance ) {
		// Merge with defaults.
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'bizznis' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'page_id' ); ?>"><?php _e( 'Page', 'bizznis' ); ?>:</label>
			<?php wp_dropdown_pages( array( 'name' => esc_attr( $this->get_field_name( 'page_id' ) ), 'id' => $this->get_field_id( 'page_id' ), 'selected' => $instance['page_id'] ) ); ?>
		</p>

		<hr class="div" />

		<p>
			<input id="<?php echo $this->get_field_id( 'show_image' ); ?>" type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'show_image' ) ); ?>" value="1"<?php checked( $instance['show_image'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_image' ) ); ?>"><?php _e( 'Show Featured Image', 'bizznis' ); ?></label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'image_size' ) ); ?>"><?php _e( 'Image Size', 'bizznis' ); ?>:</label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'image_size' ) ); ?>" class="bizznis-image-size-selector" name="<?php echo esc_attr( $this->get_field_name( 'image_size' ) ); ?>">
				<?php
				$sizes = bizznis_get_image_sizes();
				foreach( (array) $sizes as $name => $size )
					printf( '<option value="%s" %s>%s (%sx%s)</option>', esc_attr( $name ), selected( $name, $instance['image_size'], false ), esc_html( $name ), esc_html( $size['width'] ), esc_html( $size['height'] ) );
				?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'image_alignment' ) ); ?>"><?php _e( 'Image Alignment', 'bizznis' ); ?>:</label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'image_alignment' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'image_alignment' ) ); ?>">
				<option value="alignnone">- <?php _e( 'None', 'bizznis' ); ?> -</option>
				<option value="alignleft" <?php selected( 'alignleft', $instance['image_alignment'] ); ?>><?php _e( 'Left', 'bizznis' ); ?></option>
				<option value="alignright" <?php selected( 'alignright', $instance['image_alignment'] ); ?>><?php _e( 'Right', 'bizznis' ); ?></option>
				<option value="aligncenter" <?php selected( 'aligncenter', $instance['image_alignment'] ); ?>><?php _e( 'Center', 'bizznis' ); ?></option>
			</select>
		</p>

		<hr class="div" />

		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_title' ) ); ?>" type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'show_title' ) ); ?>" value="1"<?php checked( $instance['show_title'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_title' ) ); ?>"><?php _e( 'Show Page Title', 'bizznis' ); ?></label>
		</p>
		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_content' ) ); ?>" type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'show_content' ) ); ?>" value="1"<?php checked( $instance['show_content'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_content' ) ); ?>"><?php _e( 'Show Page Content', 'bizznis' ); ?></label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'content_limit' ) ); ?>"><?php _e( 'Content Character Limit', 'bizznis' ); ?>:</label>
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'content_limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'content_limit' ) ); ?>" value="<?php echo esc_attr( $instance['content_limit'] ); ?>" size="3" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'more_text' ) ); ?>"><?php _e( 'More Text', 'bizznis' ); ?>:</label>
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'more_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'more_text' ) ); ?>" value="<?php echo esc_attr( $instance['more_text'] ); ?>" />
		</p>
		<?php
	}
}
endif;