<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

/**
 * Bizznis Page List widget class.
 *
 * @since 1.0.0
 */
class Bizznis_Page_List extends WP_Widget {

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
			'title'				=> '',
			'sort_column'		=> 'menu_order, post_title',
			'sort_order'		=> 'asc',
			'exclude'			=> '',
			'child_of'			=> '',
			'link_before'		=> '',
			'link_after'		=> ''
		);
		$widget_ops = array(
			'classname'   => 'featured-content pagelist',
			'description' => __( 'Displays page list', 'bizznis' ),
		);
		$control_ops = array(
			'id_base' => 'page-list',
		);
		parent::__construct( 'page-list', __( 'Bizznis - Page List', 'bizznis' ), $widget_ops, $control_ops );
	}

	/**
	 * Echo the widget content.
	 *
	 * @since 1.0.0
	 */
	function widget( $args, $instance ) {
		global $wp_query;
		extract( $args );
		# Merge with defaults
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		# Bulletproof ordering
		if ( $instance['sort_column'] == 'menu_order' ) {
			$instance['sort_column'] = 'menu_order, post_title';
		}
		# Get page list
		$out = wp_list_pages( apply_filters('widget_pages_list_args', array(
			'title_li'			=> '',
			'echo'				=> 0,			
			'sort_column'		=> $instance['sort_column'],
			'sort_order'		=> $instance['sort_order'],
			'exclude'			=> $instance['exclude'],
			'child_of'			=> $instance['child_of'],
			'link_before'		=> $instance['link_before'],
			'link_after'		=> $instance['link_after']
		) ) );
		# stop if nothing to show
		if ( empty( $out ) ) {
			return;
		}
		# start
		echo $before_widget;
		if ( ! empty( $instance['title'] ) ) {
			echo $before_title . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $after_title;
		}
		?>
		<ul>
			<?php echo $out; ?>
		</ul>
		<?php
		echo $after_widget;
		# end
	}

	/**
	 * Update a particular instance.
	 *
	 * This function should check that $new_instance is set correctly.
	 * The newly calculated value of $instance should be returned.
	 * If "false" is returned, the instance won't be saved/updated.
	 *
	 * @since 1.0.0
	 */
	function update( $new_instance, $old_instance ) {
		$new_instance['title']     = strip_tags( $new_instance['title'] );
		return $new_instance;
	}

	/**
	 * Echo the settings update form.
	 *
	 * @since 1.0.0
	 */
	function form( $instance ) {
		# Merge with defaults
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'bizznis' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'sort_column' ); ?>"><?php _e( 'Sort by', 'bizznis' ); ?>:</label>
			<select id="<?php echo $this->get_field_id( 'sort_column' ); ?>" name="<?php echo $this->get_field_name( 'sort_column' ); ?>">
				<option value="post_title" <?php selected( 'post_title', $instance['sort_column'] ); ?>><?php _e( 'Page title', 'bizznis' ); ?></option>
				<option value="menu_order" <?php selected( 'menu_order', $instance['sort_column'] ); ?>><?php _e( 'Menu order', 'bizznis' ); ?></option>
				<option value="post_date" <?php selected( 'post_date', $instance['sort_column'] ); ?>><?php _e( 'Publish date', 'bizznis' ); ?></option>
				<option value="post_modified" <?php selected( 'post_modified', $instance['sort_column'] ); ?>><?php _e( 'Modified date', 'bizznis' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'sort_order' ); ?>"><?php _e( 'Order by', 'bizznis' ); ?>:</label>
			<select id="<?php echo $this->get_field_id( 'sort_order' ); ?>" name="<?php echo $this->get_field_name( 'sort_order' ); ?>">
				<option value="asc" <?php selected( 'asc', $instance['sort_order'] ); ?>><?php _e( 'Ascending', 'bizznis' ); ?></option>
				<option value="desc" <?php selected( 'desc', $instance['sort_order'] ); ?>><?php _e( 'Descending', 'bizznis' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'child_of' ); ?>"><?php _e( 'Child Of', 'bizznis' ); ?>:</label>
			<?php wp_dropdown_pages( array( 'name' => $this->get_field_name( 'child_of' ), 'selected' => $instance['child_of'], 'show_option_none' => __( '- Select -', 'bizznis' ) ) ); ?>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'exclude' ); ?>"><?php _e( 'Exclude', 'bizznis' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'exclude' ); ?>" name="<?php echo $this->get_field_name( 'exclude' ); ?>" value="<?php echo esc_attr( $instance['exclude'] ); ?>" />
			<br />
			<small><?php _e( 'Page IDs, separated by commas.', 'bizznis' ); ?></small>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'link_before' ); ?>"><?php _e( 'Before link', 'bizznis' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'link_before' ); ?>" name="<?php echo $this->get_field_name( 'link_before' ); ?>" value="<?php echo esc_attr( $instance['link_before'] ); ?>" />
			<br />
			<small><?php _e( 'Sets the text or html that precedes the link text inside &lt;a&gt; tag.', 'bizznis' ); ?></small>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'link_after' ); ?>"><?php _e( 'After link', 'bizznis' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'link_after' ); ?>" name="<?php echo $this->get_field_name( 'link_after' ); ?>" value="<?php echo esc_attr( $instance['link_after'] ); ?>" />
			<br />
			<small><?php _e( 'Sets the text or html that follows the link text inside &lt;a&gt; tag.', 'bizznis' ); ?></small>
		</p>
		<?php
	}
}