<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

/**
 * This file creates main theme Settings panel.
 *
 * @since 1.0.0
 */
class Bizznis_Admin_Settings extends Bizznis_Admin_Form {

	/**
	 * Create an admin menu item and settings page.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		$page_id  = 'bizznis';
		$menu_ops = apply_filters( 'bizznis_theme_settings_menu_ops',
			array( 
				'theme_menu' => array(
					'page_title' 	=> __( 'Theme Settings', 'bizznis' ),
					'menu_title' 	=> __( 'Theme Settings', 'bizznis' ),
					'capability' 	=> 'edit_theme_options',
				)
			)
		);
		$page_ops = apply_filters( 'bizznis_theme_settings_page_ops',
			array(
				'screen_icon'       => 'options-bizznis',
				'save_button_text'  => __( 'Save Settings', 'bizznis' ),
				'reset_button_text' => __( 'Reset Settings', 'bizznis' ),
				'saved_notice_text' => __( 'Settings saved.', 'bizznis' ),
				'reset_notice_text' => __( 'Settings reset.', 'bizznis' ),
				'error_notice_text' => __( 'Error saving settings.', 'bizznis' ),
			)
		);
		$settings_field = BIZZNIS_SETTINGS_FIELD;
		$default_settings = apply_filters( 'bizznis_theme_settings_defaults',
			array(
				'blog_title'                => 'text',
				'style_selection'			=> '',
				'header_right'              => 0,
				'site_layout'               => bizznis_get_default_layout(),
				'nav_extras'                => '',
				'nav_extras_twitter_id'     => '',
				'nav_extras_twitter_text'   => __( 'Follow me on Twitter', 'bizznis' ),
				'comments_pages'            => 0,
				'comments_posts'            => 1,
				'breadcrumb_home'           => 0,
				'breadcrumb_front_page'     => 0,
				'breadcrumb_posts_page'     => 0,
				'breadcrumb_single'         => 0,
				'breadcrumb_page'           => 0,
				'breadcrumb_archive'        => 0,
				'breadcrumb_404'            => 0,
				'breadcrumb_attachment'		=> 0,
				'content_archive'           => 'full',
				'content_archive_thumbnail' => 0,
				'posts_nav'                 => 'numeric',
				'blog_cat'                  => '',
				'blog_cat_exclude'          => '',
				'blog_cat_num'              => 10,
				'header_scripts'            => '',
				'footer_scripts'            => '',
				'theme_version'             => PARENT_THEME_VERSION,
				'db_version'                => PARENT_DB_VERSION,
			)
		);
		$this->create( $page_id, $menu_ops, $page_ops, $settings_field, $default_settings );
		# Sanitize before saving
		add_action( 'bizznis_settings_sanitizer_init', array( $this, 'sanitizer_filters' ) );
	}

	/**
	 * Registers each of the settings with a sanitization filter type.
	 *
	 * @since 1.0.0
	 */
	public function sanitizer_filters() {
		bizznis_add_option_filter(
			'one_zero',
			$this->settings_field,
			array(
				'breadcrumb_front_page',
				'breadcrumb_home',
				'breadcrumb_single',
				'breadcrumb_posts_page',
				'breadcrumb_archive',
				'breadcrumb_404',
				'breadcrumb_attachment',
				'comments_posts',
				'comments_pages',
				'content_archive_thumbnail',
				'redirect_feed',
				'redirect_comments_feed',
			)
		);
		bizznis_add_option_filter(
			'no_html',
			$this->settings_field,
			array(
				'blog_cat_exclude',
				'blog_title',
				'content_archive',
				'nav_extras',
				'nav_extras_twitter_id',
				'posts_nav',
				'site_layout',
				'style_selection',
				'theme_version',
			)
		);
		bizznis_add_option_filter(
			'absint',
			$this->settings_field,
			array(
				'blog_cat',
				'blog_cat_num',
				'db_version',
			)
		);
		bizznis_add_option_filter(
			'safe_html',
			$this->settings_field,
			array(
				'nav_extras_twitter_text',
			)
		);
		bizznis_add_option_filter(
			'requires_unfiltered_html',
			$this->settings_field,
			array(
				'header_scripts',
				'footer_scripts',
			)
		);
		bizznis_add_option_filter(
			'url',
			$this->settings_field,
			array(
				// empty
			)
		);
	}
	
	/**
	 * Contextual help content.
	 *
	 * @since 1.0.0
	 */
	public function help() {
		$screen = get_current_screen();
		$theme_settings_help =
			'<h3>' . __( 'Theme Settings' , 'bizznis' ) . '</h3>' .
			'<p>'  . __( 'Bizznis Theme Settings provide control over how the theme works. You will be able to control a lot of common and even advanced features from this menu. Some child themes may add additional settings fields, including the ability to select different color styles or set theme specific features such as a slider. Below you\'ll find the items common to every child theme...' , 'bizznis' ) . '</p>';
		$information_help =
			'<h3>' . __( 'Information' , 'bizznis' ) . '</h3>' .
			'<p>'  . __( 'The information field allows you to see the current Bizznis theme information.' , 'bizznis' ) . '</p>' .
		$layout_help =
			'<h3>' . __( 'Default Layout' , 'bizznis' ) . '</h3>' .
			'<p>'  . __( 'This lets you select the default layout for your entire site. On most of the child themes you\'ll see these options:' , 'bizznis' ) . '</p>' .
			'<ul>' .
				'<li>' . __( 'Content | Sidebar' , 'bizznis' ) . '</li>' .
				'<li>' . __( 'Sidebar | Content' , 'bizznis' ) . '</li>' .
				'<li>' . __( 'Sidebar | Content | Sidebar' , 'bizznis' ) . '</li>' .
				'<li>' . __( 'Content | Sidebar | Sidebar' , 'bizznis' ) . '</li>' .
				'<li>' . __( 'Sidebar | Sidebar | Content' , 'bizznis' ) . '</li>' .
				'<li>' . __( 'Full Width Content' , 'bizznis' ) . '</li>' .
			'</ul>' .
			'<p>'  . __( 'These options can be extended or limited by the child theme. Additionally, many of the child themes do not allow different layouts on the home page as they have been designed for a specific home page layout.' , 'bizznis' ) . '</p>' .
			'<p>'  . __( 'This layout can also be overridden in the post/page/term layout options on each post/page/term.' , 'bizznis' ) . '</p>';
		$archives_help =
			'<h3>' . __( 'Content Archives' , 'bizznis' ) . '</h3>' .
			'<p>'  . __( 'In the Bizznis Theme Settings you may change the site wide Content Archives options to control what displays in the site\'s Archives.' , 'bizznis' ) . '</p>' .
			'<p>'  . __( 'Archives include any pages using the blog template, category pages, tag pages, date archive, author archives, and the latest posts if there is no custom home page.' , 'bizznis' ) . '</p>' .
			'<p>'  . __( 'The first option allows you to display the post content or the post excerpt. The Display post content setting will display the entire post including HTML code up to the <!--more--> tag if used (this is HTML for the comment tag that is not displayed in the browser).' , 'bizznis' ) . '</p>' .
			'<p>'  . __( 'It may also be coupled with the second field "Limit content to [___] characters" to limit the content to a specific number of letters or spaces. This will strip any HTML, but allows for more precise and easily changed lengths than the excerpt.' , 'bizznis' ) . '</p>' .
			'<p>'  . __( 'The Display post excerpt setting will display the first 55 words of the post after also stripping any included HTML or the manual/custom excerpt added in the post edit screen.' , 'bizznis' ) . '</p>' .
			'<p>'  . __( 'The \'Featured Image\' setting allows you to show a thumbnail of the first attached image or currently set featured image.' , 'bizznis' ) . '</p>' .
			'<p>'  . __( 'This option should not be used with the post content unless the content is limited to avoid duplicate images.' , 'bizznis' ) . '</p>' .
			'<p>'  . __( 'The \'Featured Image Size\' list is populated by the available image sizes defined in the theme.' , 'bizznis' ) . '</p>' .
			'<p>'  . __( 'Post Navigation Technique allows you to select one of two navigation methods.' , 'bizznis' ) . '</p>';
		$navigation_help =
			'<h3>' . __( 'Primary Menu', 'bizznis' ) . '</h3>' .
			'<p>'  . __( 'The Primary Navigation Extras typically display on the right side of your Primary Navigation menu.', 'bizznis' ) . '</p>' .
			'<ul>' .
				'<li>' . __( 'Today\'s date displays the current date', 'bizznis' ) . '</li>' .
				'<li>' . __( 'Search form displays a small search form utilizing the WordPress search functionality.', 'bizznis' ) . '</li>' .
				'<li>' . __( 'Twitter link displays a link to your Twitter profile, as indicated in Twitter ID setting. Enter only your user name in this setting.', 'bizznis' ) . '</li>' .
			'</ul>' .
			'<p>'  . __( 'These options can be extended or limited by the child theme.', 'bizznis' ) . '</p>';
		$breadcrumbs_help =
			'<h3>' . __( 'Breadcrumbs' , 'bizznis' ) . '</h3>' .
			'<p>'  . __( 'This field lets you define where the "Breadcrumbs" display. The Breadcrumb is the navigation tool that displays where a visitor is on the site at any given moment.' , 'bizznis' ) . '</p>';
		$comments_help =
			'<h3>' . __( 'Comments' , 'bizznis' ) . '</h3>' .
			'<p>'  . __( 'This allows to decide whether comments are enabled for posts and pages.' , 'bizznis' ) . '</p>' .
			'<p>'  . __( 'Even If you enable comments here, it can be disabled on an individual post or page. If you disable here, they cannot be enabled on an individual post or page.' , 'bizznis' ) . '</p>';
		$scripts_help =
			'<h3>' . __( 'Header and Footer Scripts' , 'bizznis' ) . '</h3>' .
			'<p>'  . __( 'This provides you with two fields that will output HTML scripts to either header or the footer of your website. These will appear on every page of the site and are a great way to add scripts. You cannot use PHP in these fields. If you need to use PHP then add it via hooks inside a child theme.' , 'bizznis' ) . '</p>';
		$home_help =
			'<h3>' . __( 'How Home Pages Work' , 'bizznis' ) . '</h3>' .
			'<p>'  . __( 'Most Bizznis child themes include a custom home page.' , 'bizznis' ) . '</p>' .
			'<p>'  . __( 'To use this type of home page, make sure your latest posts are set to show on the front page. You can setup a page with the Blog page template to show a blog style list of your latest posts on another page.' , 'bizznis' ) . '</p>' .
			'<p>'  . __( 'This home page is typically setup via widgets in the sidebars for the home page. This can be accessed via the Widgets menu item under Appearance.' , 'bizznis' ) . '</p>' .
			'<p>'  . __( 'Child themes that include this type of home page typically include additional theme-specific tutorials which can be accessed via a sticky post at the top of that child theme support forum.' , 'bizznis' ) . '</p>' .
			'<p>'  . __( 'If your theme uses a custom home page and you want to show the latest posts in a blog format, do not use the blog template. Instead, you need to rename the home.php file to home-old.php instead.' , 'bizznis' ) . '</p>' .
			'<p>'  . __( 'Another common home page is the "blog" type home page, which is common to most of the free child themes. This shows your latest posts and requires no additional setup.' , 'bizznis' ) . '</p>' .
			'<p>'  . __( 'The third type of home page is the new dynamic home page. This is common on the newest child themes. It will show your latest posts in a blog type listing unless you put widgets into the home page sidebars.' , 'bizznis' ) . '</p>' .
			'<p>'  . __( 'This setup is preferred because it makes it easier to show a blog on the front page (no need to rename the home.php file) and does not have the confusion of no content on the home page when the theme is initially installed.' , 'bizznis' ) . '</p>';
		$screen->add_help_tab( array(
			'id'      => $this->pagehook . '-theme-settings',
			'title'   => __( 'Theme Settings' , 'bizznis' ),
			'content' => $theme_settings_help,
		) );
		$screen->add_help_tab( array(
			'id'      => $this->pagehook . '-information',
			'title'   => __( 'Information' , 'bizznis' ),
			'content' => $information_help,
		) );
		$screen->add_help_tab( array(
			'id'      => $this->pagehook . '-layout',
			'title'   => __( 'Default Layout' , 'bizznis' ),
			'content' => $layout_help,
		) );
		$screen->add_help_tab( array(
			'id'      => $this->pagehook . '-navigation',
			'title'   => __( 'Primary Menu' , 'bizznis' ),
			'content' => $navigation_help,
		) );
		$screen->add_help_tab( array(
			'id'      => $this->pagehook . '-breadcrumbs',
			'title'   => __( 'Breadcrumbs' , 'bizznis' ),
			'content' => $breadcrumbs_help,
		) );
		$screen->add_help_tab( array(
			'id'      => $this->pagehook . '-comments',
			'title'   => __( 'Comments and Trackbacks' , 'bizznis' ),
			'content' => $comments_help,
		) );
		$screen->add_help_tab( array(
			'id'      => $this->pagehook . '-archives',
			'title'   => __( 'Content Archives' , 'bizznis' ),
			'content' => $archives_help,
		) );
		$screen->add_help_tab( array(
			'id'      => $this->pagehook . '-scripts',
			'title'   => __( 'Header and Footer Scripts' , 'bizznis' ),
			'content' => $scripts_help,
		) );
		$screen->add_help_tab( array(
			'id'      => $this->pagehook . '-home',
			'title'   => __( 'Home Pages' , 'bizznis' ),
			'content' => $home_help,
		) );
		# Add help sidebar
		$screen->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'bizznis' ) . '</strong></p>' .
			'<p><a href="' . sprintf( __( '%s', 'bizznis' ), 'http://bizzthemes.com/support/' ) . '" target="_blank" title="' . __( 'Get Support', 'bizznis' ) . '">' . __( 'Get Support', 'bizznis' ) . '</a></p>'
		);
	}
	
	/**
	 * Callback for Theme Settings Information meta box.
	 *
	 * @since 1.0.0
	 */
	function form() {
		# Outputs hidden form fields before the metaboxes.
		printf( '<input type="hidden" name="%s" value="%s" />', $this->get_field_name( 'theme_version' ), esc_attr( $this->get_field_value( 'theme_version' ) ) );
		printf( '<input type="hidden" name="%s" value="%s" />', $this->get_field_name( 'db_version' ), esc_attr( $this->get_field_value( 'db_version' ) ) );
		?>
		<!-- Information -->
		<!--<h3><?php _e( 'Information', 'bizznis' ); ?></h3>-->
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row" valign="top"><?php _e( 'Information', 'bizznis' ); ?></th>
					<td>
						<p><em><?php _e( 'Version:', 'bizznis' ); ?></em> <?php echo $this->get_field_value( 'theme_version' ); ?> &#x000B7; <em><?php _e( 'Released:', 'bizznis' ); ?></em> <?php echo PARENT_THEME_RELEASE_DATE; ?></p>
					</td>
				</tr>
			</tbody>
		</table>
		<!-- Default Layout -->
		<!--<h3><?php _e( 'Default Layout', 'bizznis' ); ?></h3>-->
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row" valign="top"><?php _e( 'Default Layout', 'bizznis' ); ?></th>
					<td>
						<div class="bizznis-layout-selector">
							<p><?php bizznis_layout_selector( array( 'name' => $this->get_field_name( 'site_layout' ), 'selected' => $this->get_field_value( 'site_layout' ), 'type' => 'site' ) ); ?></p>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
		<?php if ( current_theme_supports( 'bizznis-style-selector' ) ) { ?>
		<!-- Style Select -->
		<!--<h3><?php _e( 'Color Style:', 'bizznis' ); ?></h3>-->
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row" valign="top"><?php _e( 'Color Style', 'bizznis' ); ?></th>
					<td>
						<select name="<?php echo $this->get_field_name( 'style_selection' ); ?>" id="<?php echo $this->get_field_id( 'style_selection' ); ?>">
							<option value=""><?php _e( 'Default', 'bizznis' ); ?></option>
							<?php
							$current = $this->get_field_value( 'style_selection' );
							$styles  = get_theme_support( 'bizznis-style-selector' );
							if ( ! empty( $styles ) ) {
								$styles = array_shift( $styles );
								foreach ( (array) $styles as $style => $title ) {
									?><option value="<?php echo esc_attr( $style ); ?>"<?php selected( $current, $style ); ?>><?php echo esc_html( $title ); ?></option><?php
								}
							}
							?>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		<?php } ?>
		<?php if ( ! current_theme_supports( 'bizznis-custom-header' ) && ! current_theme_supports( 'custom-header' ) ) { ?>
		<!-- Header -->
		<h3><?php _e( 'Header:', 'bizznis' ); ?></h3>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row" valign="top"><?php _e( 'Site Title/Logo', 'bizznis' ); ?></th>
					<td>
						<select name="<?php echo $this->get_field_name( 'blog_title' ); ?>" id="<?php echo $this->get_field_id( 'blog_title' ); ?>">
							<option value="text"<?php selected( $this->get_field_value( 'blog_title' ), 'text' ); ?>><?php _e( 'Site Title &amp; Tagline', 'bizznis' ); ?></option>
							<option value="image"<?php selected( $this->get_field_value( 'blog_title' ), 'image' ); ?>><?php _e( 'Image logo', 'bizznis' ); ?></option>
						</select>
						<div id="bizznis_blog_title_image">
							<p class="description"><?php _e( 'By default, this option will pick the header image in your child theme\'s style.css file.', 'bizznis' ); ?></p>
							<p class="description"><?php _e( 'The logo can be saved as logo.png to the images folder of your child theme and will be shown instead of your site title and tagline.', 'bizznis' ); ?></p>
							<p class="description"><?php _e( 'If you want to use more advanced options, make sure you\'ve added <code>add_theme_support( \'custom-header\' );</code> to your child theme\'s functions.php file.', 'bizznis' ); ?></p>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
		<?php } ?>
		<!-- Content Archives -->
		<h3><?php _e( 'Content Archives:', 'bizznis' ); ?></h3>
		<p class="description"><?php _e( 'These options will affect any listings page, including archive, author, blog, category, search, and tag pages.', 'bizznis' ); ?></p>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row" valign="top"><?php _e( 'Display Option', 'bizznis' ); ?></th>
					<td>
						<select name="<?php echo $this->get_field_name( 'content_archive' ); ?>" id="<?php echo $this->get_field_id( 'content_archive' ); ?>">
						<?php
						$archive_display = apply_filters(
							'bizznis_archive_display_options',
							array(
								'full'     => __( 'Display post content', 'bizznis' ),
								'excerpts' => __( 'Display post excerpts', 'bizznis' ),
							)
						);
						foreach ( (array) $archive_display as $value => $name ) {
							echo '<option value="' . esc_attr( $value ) . '"' . selected( $this->get_field_value( 'content_archive' ), esc_attr( $value ), false ) . '>' . esc_html( $name ) . '</option>' . "\n";
						}
						?>
						</select>
					</div>
					</td>
				</tr>
				<tr id="bizznis_content_limit_setting">
					<th scope="row" valign="top"><?php _e( 'Limit content to', 'bizznis' ); ?></th>
					<td>
						<p>
							<input type="text" name="<?php echo $this->get_field_name( 'content_archive_limit' ); ?>" id="<?php echo $this->get_field_id( 'content_archive_limit' ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'content_archive_limit' ) ); ?>" size="3" />
							<label for="<?php echo $this->get_field_id( 'content_archive_limit' ); ?>"><?php _e( 'characters', 'bizznis' ); ?></label>
						</p>
						<p class="description"><?php _e( 'Limit the text and strip all formatting from the text displayed.', 'bizznis' ); ?></p>
						<p class="description"><?php _e( 'To use this option, choose "Display post content" in the select box above.', 'bizznis' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top"><?php _e( 'Featured Image', 'bizznis' ); ?></th>
					<td>
						<input type="checkbox" name="<?php echo $this->get_field_name( 'content_archive_thumbnail' ); ?>" id="<?php echo $this->get_field_id( 'content_archive_thumbnail' ); ?>" value="1"<?php checked( $this->get_field_value( 'content_archive_thumbnail' ) ); ?> />
						<label for="<?php echo $this->get_field_id( 'content_archive_thumbnail' ); ?>"><?php _e( 'Include the Featured Image?', 'bizznis' ); ?></label>
					</td>
				</tr>
				<tr id="bizznis_image_size">
					<th scope="row" valign="top"><?php _e( 'Featured Image Size', 'bizznis' ); ?></th>
					<td>
						<select name="<?php echo $this->get_field_name( 'image_size' ); ?>" id="<?php echo $this->get_field_id( 'image_size' ); ?>">
						<?php
						$sizes = bizznis_get_image_sizes();
						foreach ( (array) $sizes as $name => $size ) {
							echo '<option value="' . esc_attr( $name ) . '"' . selected( $this->get_field_value( 'image_size' ), $name, FALSE ) . '>' . esc_html( $name ) . ' (' . absint( $size['width'] ) . ' &#x000D7; ' . absint( $size['height'] ) . ')</option>' . "\n";
						}
						?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top"><?php _e( 'Navigation Technique', 'bizznis' ); ?></th>
					<td>
						<select name="<?php echo $this->get_field_name( 'posts_nav' ); ?>" id="<?php echo $this->get_field_id( 'posts_nav' ); ?>">
							<option value="prev-next"<?php selected( 'prev-next', $this->get_field_value( 'posts_nav' ) ); ?>><?php _e( 'Previous / Next', 'bizznis' ); ?></option>
							<option value="numeric"<?php selected( 'numeric', $this->get_field_value( 'posts_nav' ) ); ?>><?php _e( 'Numeric', 'bizznis' ); ?></option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		<?php if ( current_theme_supports( 'bizznis-menus' ) && bizznis_nav_menu_supported( 'primary' ) ) { ?>
		<!-- Menus -->
		<h3><?php _e( 'Primary Menu:', 'bizznis' ); ?></h3>
		<p class="description"><?php printf( __( 'In order to use the navigation menus, you must build a <a href="%s">custom menu</a>, then assign it to the proper Menu Location.', 'bizznis' ), admin_url( 'nav-menus.php' ) ); ?></p>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row" valign="top"><?php _e( 'Extras', 'bizznis' ); ?></th>
					<td>
						<p>
							<input type="checkbox" name="<?php echo $this->get_field_name( 'nav_extras_enable' ); ?>" id="<?php echo $this->get_field_id( 'nav_extras_enable' ); ?>" value="1"<?php checked( $this->get_field_value( 'nav_extras_enable' ) ); ?> />
							<label for="<?php echo $this->get_field_id( 'nav_extras_enable' ); ?>"><?php _e( 'Enable Extras on Right Side?', 'bizznis' ); ?></label>
						</p>
						<div id="bizznis_nav_extras_settings">
							<p>
								<label for="<?php echo $this->get_field_id( 'nav_extras' ); ?>"><?php _e( 'Display the following:', 'bizznis' ); ?></label>
								<select name="<?php echo $this->get_field_name( 'nav_extras' ); ?>" id="<?php echo $this->get_field_id( 'nav_extras' ); ?>">
									<option value="date"<?php selected( $this->get_field_value( 'nav_extras' ), 'date' ); ?>><?php _e( 'Today\'s date', 'bizznis' ); ?></option>
									<option value="search"<?php selected( $this->get_field_value( 'nav_extras' ), 'search' ); ?>><?php _e( 'Search form', 'bizznis' ); ?></option>
									<option value="twitter"<?php selected( $this->get_field_value( 'nav_extras' ), 'twitter' ); ?>><?php _e( 'Twitter link', 'bizznis' ); ?></option>
								</select>
							</p>
							<div id="bizznis_nav_extras_twitter">
								<p>
									<label for="<?php echo $this->get_field_id( 'nav_extras_twitter_id' ); ?>"><?php _e( 'Enter Twitter ID:', 'bizznis' ); ?></label>
									<input type="text" name="<?php echo $this->get_field_name( 'nav_extras_twitter_id' ); ?>" id="<?php echo $this->get_field_id( 'nav_extras_twitter_id' ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'nav_extras_twitter_id' ) ); ?>" size="27" />
								</p>
								<p>
									<label for="<?php echo $this->get_field_id( 'nav_extras_twitter_text' ); ?>"><?php _e( 'Twitter Link Text:', 'bizznis' ); ?></label>
									<input type="text" name="<?php echo $this->get_field_name( 'nav_extras_twitter_text' ); ?>" id="<?php echo $this->get_field_id( 'nav_extras_twitter_text' ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'nav_extras_twitter_text' ) ); ?>" size="27" />
								</p>
							</div>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
		<?php } ?>
		<?php if ( current_theme_supports( 'bizznis-breadcrumbs' ) ) { ?>
		<!-- Breadcrumbs -->
		<h3><?php _e( 'Breadcrumbs:', 'bizznis' ); ?></h3>
		<p class="description"><?php _e( 'Breadcrumbs are a great way of letting your visitors find out where they are on your site with just a glance.', 'bizznis' ); ?></p>
		<p class="description"><?php _e( 'You can enable/disable them on certain areas of your site.', 'bizznis' ); ?></p>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row" valign="top"><?php _e( 'Enable on', 'bizznis' ); ?></th>
					<td>
						<p>
							<?php if ( 'page' == get_option( 'show_on_front' ) ) { ?>
								<input type="checkbox" name="<?php echo $this->get_field_name( 'breadcrumb_front_page' ); ?>" id="<?php echo $this->get_field_id( 'breadcrumb_front_page' ); ?>" value="1"<?php checked( $this->get_field_value( 'breadcrumb_front_page' ) ); ?> />
								<label for="<?php echo $this->get_field_id( 'breadcrumb_front_page' ); ?>"><?php _e( 'Front Page', 'bizznis' ); ?></label>
								<br />
								<input type="checkbox" name="<?php echo $this->get_field_name( 'breadcrumb_posts_page' ); ?>" id="<?php echo $this->get_field_id( 'breadcrumb_posts_page' ); ?>" value="1"<?php checked( $this->get_field_value( 'breadcrumb_posts_page' ) ); ?> />
								<label for="<?php echo $this->get_field_id( 'breadcrumb_posts_page' ); ?>"><?php _e( 'Posts Page', 'bizznis' ); ?></label>
							<?php } else { ?>
								<input type="checkbox" name="<?php echo $this->get_field_name( 'breadcrumb_home' ); ?>" id="<?php echo $this->get_field_id( 'breadcrumb_home' ); ?>" value="1"<?php checked( $this->get_field_value( 'breadcrumb_home' ) ); ?> />
								<label for="<?php echo $this->get_field_id( 'breadcrumb_home' ); ?>"><?php _e( 'Homepage', 'bizznis' ); ?></label>
							<?php } ?>
						</p>
						<br />
						<p>
							<input type="checkbox" name="<?php echo $this->get_field_name( 'breadcrumb_single' ); ?>" id="<?php echo $this->get_field_id( 'breadcrumb_single' ); ?>" value="1"<?php checked( $this->get_field_value( 'breadcrumb_single' ) ); ?> />
							<label for="<?php echo $this->get_field_id( 'breadcrumb_single' ); ?>"><?php _e( 'Posts', 'bizznis' ); ?></label>
							<br />
							<input type="checkbox" name="<?php echo $this->get_field_name( 'breadcrumb_page' ); ?>" id="<?php echo $this->get_field_id( 'breadcrumb_page' ); ?>" value="1"<?php checked( $this->get_field_value( 'breadcrumb_page' ) ); ?> />
							<label for="<?php echo $this->get_field_id( 'breadcrumb_page' ); ?>"><?php _e( 'Pages', 'bizznis' ); ?></label>
							<br />
							<input type="checkbox" name="<?php echo $this->get_field_name( 'breadcrumb_archive' ); ?>" id="<?php echo $this->get_field_id( 'breadcrumb_archive' ); ?>" value="1"<?php checked( $this->get_field_value( 'breadcrumb_archive' ) ); ?> />
							<label for="<?php echo $this->get_field_id( 'breadcrumb_archive' ); ?>"><?php _e( 'Archives', 'bizznis' ); ?></label>
							<br />
							<input type="checkbox" name="<?php echo $this->get_field_name( 'breadcrumb_404' ); ?>" id="<?php echo $this->get_field_id( 'breadcrumb_404' ); ?>" value="1"<?php checked( $this->get_field_value( 'breadcrumb_404' ) ); ?> />
							<label for="<?php echo $this->get_field_id( 'breadcrumb_404' ); ?>"><?php _e( '404 Page', 'bizznis' ); ?></label>
							<br />
							<input type="checkbox" name="<?php echo $this->get_field_name( 'breadcrumb_attachment' ); ?>" id="<?php echo $this->get_field_id( 'breadcrumb_attachment' ); ?>" value="1"<?php checked( $this->get_field_value( 'breadcrumb_attachment' ) ); ?> />
							<label for="<?php echo $this->get_field_id( 'breadcrumb_attachment' ); ?>"><?php _e( 'Attachment Page', 'bizznis' ); ?></label>
						</p>
					</td>
				</tr>
			</tbody>
		</table>
		<?php } ?>
		<!-- Comments -->
		<h3><?php _e( 'Comments:', 'bizznis' ); ?></h3>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row" valign="top"><?php _e( 'Enable Comments', 'bizznis' ); ?></th>
					<td>
						<input type="checkbox" name="<?php echo $this->get_field_name( 'comments_posts' ); ?>" id="<?php echo $this->get_field_id( 'comments_posts' ); ?>" value="1"<?php checked( $this->get_field_value( 'comments_posts' ) ); ?> />
						<label for="<?php echo $this->get_field_id( 'comments_posts' ); ?>" title="Enable comments on posts"><?php _e( 'on posts?', 'bizznis' ); ?></label>
						<input type="checkbox" name="<?php echo $this->get_field_name( 'comments_pages' ); ?>" id="<?php echo $this->get_field_id( 'comments_pages' ); ?>" value="1"<?php checked( $this->get_field_value( 'comments_pages' ) ); ?> />
						<label for="<?php echo $this->get_field_id( 'comments_pages' ); ?>" title="Enable comments on pages"><?php _e( 'on pages?', 'bizznis' ); ?></label>
					</td>
				</tr>
			</tbody>
		</table>
		<?php if ( current_user_can( 'unfiltered_html' ) ) { ?>
		<!-- Scripts -->
		<h3><?php _e( 'Scripts:', 'bizznis' ); ?></h3>
		<p class="description"><?php printf( __( 'Enter scripts or code you would like output to %s or %s:', 'bizznis' ), bizznis_code( 'wp_head()' ), bizznis_code( 'wp_footer()' ) ); ?></p>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row" valign="top"><?php printf( __( '%s scripts', 'bizznis' ), '<code>wp_head()</code>' ); ?></th>
					<td>
						<textarea name="<?php echo $this->get_field_name( 'header_scripts' ); ?>" id="<?php echo $this->get_field_id( 'header_scripts' ); ?>" class="widefat" cols="78" rows="8"><?php echo esc_textarea( $this->get_field_value( 'header_scripts' ) ); ?></textarea>
						<p class="description"><?php printf( __( 'The %1$s hook executes immediately before the closing %2$s tag in the document source.', 'bizznis' ), '<code>wp_head()</code>', '<code>&lt;/head&gt;</code>' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top"><?php printf( __( '%s scripts', 'bizznis' ), '<code>wp_footer()</code>' ); ?></th>
					<td>
						<textarea name="<?php echo $this->get_field_name( 'footer_scripts' ); ?>" id="<?php echo $this->get_field_id( 'footer_scripts' ); ?>" class="widefat" cols="78" rows="8"><?php echo esc_textarea( $this->get_field_value( 'footer_scripts' ) ); ?></textarea>
						<p class="description"><?php printf( __( 'The %1$s hook executes immediately before the closing %2$s tag in the document source.', 'bizznis' ), '<code>wp_footer()</code>', '<code>&lt;/body&gt;</code>' ); ?></p>
					</td>
				</tr>
			</tbody>
		</table>
		<?php }
		# The 'bizznis_theme_settings_form' action hook is called at the end of this function.
		do_action( 'bizznis_theme_settings_form', $this->pagehook );
	}

}
