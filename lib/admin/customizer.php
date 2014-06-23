<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

/**
 * 
 */
abstract class Bizznis_Customizer_Base {
		
	/**
	 * Define defaults, call the `register` method, add css to head.
	 */
	public function __construct() {
		
		//* Register new customizer elements
		if ( method_exists( $this, 'register' ) ) {
			add_action( 'customize_register', array( $this, 'register'), 15 );
		} else {
			_doing_it_wrong( 'Bizznis_Customizer_Base', __( 'When extending Bizznis_Customizer_Base, you must create a register method.', 'bizznis' ) );
		}
			
		//* Register the default settings
		if ( method_exists( $this, 'default_settings' ) ) {
			add_action( 'admin_init', array( $this, 'register_settings' ) );
		}
		
		//* Sanitize settings
		if ( method_exists( $this, 'sanitizer_filters' ) ) {
			add_action( 'bizznis_settings_sanitizer_init', array( $this, 'sanitizer_filters' ) );
		}

		//* Customizer preview scripts & styles
		if ( method_exists( $this, 'preview_scripts' ) ) {
			add_action( 'customize_preview_init', array( $this, 'preview_scripts') );
		}
		
		//* Customizer controls scripts & styles
		if ( method_exists( $this, 'controls_scripts' ) ) {
			add_action( 'customize_controls_enqueue_scripts', array( $this, 'controls_scripts') );
		}
		
	}
	
	public function register_settings() {
		# If this page doesn't store settings, no need to register them
		if ( ! $this->settings_field ) {
			return;
		}
		register_setting( $this->settings_field, $this->settings_field );
		add_option( $this->settings_field, $this->default_settings() );
	}

	protected function get_field_name( $name ) {
		return sprintf( '%s[%s]', $this->settings_field, $name );
	}
	
	protected function get_field_id( $id ) {
		return sprintf( '%s[%s]', $this->settings_field, $id );
	}
	
	protected function get_field_value( $key ) {
		return bizznis_get_option( $key, $this->settings_field );		
	}
	
	protected function get_default_value( $name ) {
		$defaults = $this->default_settings();
		return $defaults[$name];
	}
	
}

/**
 * 
 */
class Bizznis_Customizer extends Bizznis_Customizer_Base {

	/**
	 * Settings field.
	 */
	public $settings_field = BIZZNIS_SETTINGS_FIELD;
	
	/**
	 * Register default settings.
	 */
	public function default_settings() {

		return apply_filters( 'bizznis_theme_settings_defaults',
			array(
				'site_layout'				=> bizznis_get_default_layout(),
				'blog_title'                => 'text',
				'hide_site_title'     		=> 0,
				'hide_tagline' 	      		=> 0,
				'style_selection'			=> '',
				'header_right'              => 0,
				'nav_extras'                => '',
				'nav_extras_enable'         => 0,
				'nav_extras_twitter_id'     => '',
				'nav_extras_twitter_text'   => __( 'Follow us on Twitter', 'bizznis' ),
				'comments_pages'            => 0,
				'comments_posts'            => 1,	
				'trackbacks_posts'			=> 0,
				'trackbacks_pages'			=> 0,
				'breadcrumb_home'           => 0,
				'breadcrumb_front_page'     => 0,
				'breadcrumb_posts_page'     => 0,
				'breadcrumb_single'         => 0,
				'breadcrumb_page'           => 0,
				'breadcrumb_archive'        => 0,
				'breadcrumb_404'            => 0,
				'breadcrumb_attachment'		=> 0,
				'content_archive'           => 'full',
				'content_archive_limit'     => '',
				'content_archive_thumbnail' => 0,
				'image_size'                => '',
				'image_alignment'           => 'alignleft',
				'posts_nav'                 => 'numeric',
				'header_scripts'            => '',
				'footer_scripts'            => '',
				'theme_version'             => PARENT_THEME_VERSION,
				'db_version'                => PARENT_DB_VERSION,
			)
		);
		
	}
	
	/**
	 * Registers each of the settings with a sanitization filter type.
	 */
	public function sanitizer_filters() {
	
		bizznis_add_option_filter(
			'one_zero',
			$this->settings_field,
			array(
				'breadcrumb_front_page',
				'breadcrumb_home',
				'breadcrumb_single',
				'breadcrumb_page',
				'breadcrumb_posts_page',
				'breadcrumb_archive',
				'breadcrumb_404',
				'breadcrumb_attachment',
				'header_right',
				'comments_posts',
				'comments_pages',
				'trackbacks_posts',
				'trackbacks_pages',
				'hide_site_title',
				'hide_tagline',
				'nav_extras_enable',
				'content_archive_thumbnail',
			)
		);
		
		bizznis_add_option_filter(
			'no_html',
			$this->settings_field,
			array(
				'content_archive',
				'nav_extras',
				'nav_extras_twitter_id',
				'image_alignment',
				'posts_nav',
				'site_layout',
				'blog_title',
				'style_selection',
				'theme_version',
			)
		);
		
		bizznis_add_option_filter(
			'absint',
			$this->settings_field,
			array(
				'content_archive_limit',
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
	 * Register preview scripts.
	 */
	public function preview_scripts() {

		wp_enqueue_script( 'bizznis_preview_customizer_js', BIZZNIS_ADMIN_JS_URL . '/customizer_preview.js', array( 'jquery', 'customize-preview' ), PARENT_THEME_VERSION, true );
		
	}
	
	/**
	 * Register controls scripts.
	 */
	public function controls_scripts() {

		wp_enqueue_script( 'bizznis_controls_customizer_js', BIZZNIS_ADMIN_JS_URL . '/customizer_controls.js', array( 'jquery', 'customize-controls' ), PARENT_THEME_VERSION, true );
		
	}

	/**
	 * Register controls.
	 */
	public function register( $wp_customize ) {
		
		$this->styles( $wp_customize );
		$this->layout( $wp_customize );
		$this->menu_extras( $wp_customize );
		$this->breadcrumbs( $wp_customize );
		$this->comments( $wp_customize );
		$this->archives( $wp_customize );
		$this->custom_scripts( $wp_customize );
		$this->header( $wp_customize );
		$this->background( $wp_customize );
		
	}

	private function styles( $wp_customize ) {
			
		//* Color Selector
		if ( ! current_theme_supports( 'bizznis-style-selector' ) ) {
			return;
		}
		
		//* Setting the priority
		$priority = new Bizznis_Prioritizer();

		//* Add Section
		$wp_customize->add_section(
			'bizznis_color_scheme',
			array(
				'title'    => __( 'Custom Styles', 'bizznis' ),
				'priority' => $priority->add(),
			)
		);

		$wp_customize->add_setting(
			$this->get_field_name( 'style_selection' ),
			array(
				'default' => $this->get_default_value( 'style_selection' ),
				'type'    => 'option',
			)
		);
		
		//* Get custom styles
		$styles  = get_theme_support( 'bizznis-style-selector' );
		$custom_styles[''] = __( 'Default', 'bizznis' );
		if ( ! empty( $styles ) ) {
			$styles = array_shift( $styles );
			foreach ( (array) $styles as $style => $title ) {
				$custom_styles[esc_attr( $style )] = esc_html( $title );
			}
		}

		$wp_customize->add_control(
			'bizznis_color_scheme',
			array(
				'label'    => __( 'Select Color Style', 'bizznis' ),
				'section'  => 'bizznis_color_scheme',
				'settings' => $this->get_field_name( 'style_selection' ),
				'type'     => 'select',
				'choices'  => $custom_styles,
				'priority' => $priority->add(),
			)
		);
		
	}
	
	private function layout( $wp_customize ) {
				
		//* Setting the priority
		$priority = new Bizznis_Prioritizer( 20, 1 );
		
		$wp_customize->add_section(
			'bizznis_layout',
			array(
				'title'    => __( 'Site Layout', 'bizznis' ),
				'priority' => $priority->add(),
			)
		);

		$wp_customize->add_setting(
			$this->get_field_name( 'site_layout' ),
			array(
				'default' => $this->get_default_value( 'site_layout' ),
				'type'    => 'option',
			)
		);

		$wp_customize->add_control(
			'bizznis_layout',
			array(
				'label'    => __( 'Select Default Layout', 'bizznis' ),
				'section'  => 'bizznis_layout',
				'settings' => $this->get_field_name( 'site_layout' ),
				'type'     => 'select',
				'choices'  => bizznis_get_layouts_for_customizer(),
				'priority' => $priority->add(),
			)
		);
		
		$wp_customize->add_control(
			new Bizznis_Customize_Misc_Control(
				$wp_customize,
				'bizznis_layout_info',
				array(
					'section'     => 'bizznis_layout',
					'type'        => 'info',
					'description' => __( 'This layout can also be overridden in the post/page/term layout options on each post/page/term.', 'bizznis' ),
					'priority'    => $priority->add(),
				)
			)
		);
		
		
		//* bbPress layout
		
		if ( ! in_array( 'bbpress/bbpress.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			return;
		}
		
		$wp_customize->add_setting(
			$this->get_field_name( 'bizznis_bbp_layout' ),
			array(
				'default' => $this->get_default_value( 'site_layout' ),
				'type'    => 'option',
			)
		);

		$wp_customize->add_control(
			'bizznis_bbp_layout',
			array(
				'label'    => __( 'Select bbPress Layout', 'bizznis' ),
				'section'  => 'bizznis_layout',
				'settings' => $this->get_field_name( 'bizznis_bbp_layout' ),
				'type'     => 'select',
				'choices'  => bizznis_get_layouts_for_customizer(),
				'priority' => $priority->add(),
			)
		);
		
	}

	private function menu_extras( $wp_customize ) {
		
		//* Reposition Navigation section below Layout
		$wp_customize->get_section( 'nav' )->priority = 30;
	
		//* Nav Extras Selector
		if ( ! current_theme_supports( 'bizznis-menus' ) && ! bizznis_nav_menu_supported( 'primary' ) ) {
			return;
		}
		//* Nav Extras Selector
		if ( ! has_nav_menu( 'primary' ) ) {
			return;
		}
		
		//* Setting the priority
		$priority = new Bizznis_Prioritizer( 11, 1 );
		
		$wp_customize->add_control(
			new Bizznis_Customize_Misc_Control(
				$wp_customize,
				'extras_info',
				array(
					'section'  => 'nav',
					'type'     => 'info',
					'label'    => __( 'Primary Menu Extra', 'bizznis' ),
					'priority' => $priority->add(),
				)
			)
		);
		
		//* Setting key and default value array
		$settings = array(
			'nav_extras_enable'       => 0,
			'nav_extras'       	      => 'date',
			'nav_extras_twitter_id'   => '',
			'nav_extras_twitter_text' => '',
		);

		foreach ( $settings as $setting => $default ) {

			$wp_customize->add_setting(
				$this->get_field_name( $setting ),
				array(
					'default' => $this->get_default_value( $setting ),
					'type'    => 'option',
				)
			);

		}
		
		$wp_customize->add_control(
			'bizznis_nav_extras_enable',
			array(
				'label'    => __( 'Enable Extras on Right Side?', 'bizznis' ),
				'section'  => 'nav',
				'settings' => $this->get_field_name( 'nav_extras_enable' ),
				'type'     => 'checkbox',
				'priority' => $priority->add(),
			)
		);
		
		$wp_customize->add_control(
			'bizznis_nav_extras',
			array(
				'label'    => __( 'Display the following', 'bizznis' ),
				'section'  => 'nav',
				'settings' => $this->get_field_name( 'nav_extras' ),
				'type'     => 'select',
				'choices'  => array(
					'date' 			=> __( 'Today\'s date', 'bizznis' ),
					'search' 		=> __( 'Search form', 'bizznis' ),
					'twitter'     	=> __( 'Twitter link', 'bizznis' ),
				),
				'priority' => $priority->add(),
			)
		);
		
		$wp_customize->add_control(
			'bizznis_nav_extras_twitter_id',
			array(
				'label'    => __( 'Enter Twitter ID', 'bizznis' ),
				'section'  => 'nav',
				'settings' => $this->get_field_name( 'nav_extras_twitter_id' ),
				'priority' => $priority->add(),
			)
		);

		$wp_customize->add_control(
			'bizznis_nav_extras_twitter_text',
			array(
				'label'    => __( 'Twitter Link Text', 'bizznis' ),
				'section'  => 'nav',
				'settings' => $this->get_field_name( 'nav_extras_twitter_text' ),
				'priority' => $priority->add(),
			)
		);

	}
	
	private function breadcrumbs( $wp_customize ) {
	
		//* Breadcrumbs Selector
		if ( ! current_theme_supports( 'bizznis-breadcrumbs' ) ) {
			return;
		}
		
		//* Setting the priority
		$priority = new Bizznis_Prioritizer( 16, 1 );

		$wp_customize->add_control(
			new Bizznis_Customize_Misc_Control(
				$wp_customize,
				'breadcrumbs_info',
				array(
					'section'     => 'nav',
					'type'        => 'info',
					'label'       => __( 'Breadcrumbs', 'bizznis' ),
					'description' => __( 'Breadcrumbs are a great way of letting your visitors find out where they are on your site with just a glance.', 'bizznis' ),
					'priority' => $priority->add(),
				)
			)
		);
		
		$settings = array(
			'breadcrumb_home'       => 'Homepage',
			'breadcrumb_front_page' => 'Front Page',
			'breadcrumb_posts_page' => 'Posts Page',
			'breadcrumb_single'     => 'Single',
			'breadcrumb_page'       => 'Page',
			'breadcrumb_archive'    => 'Archive',
			'breadcrumb_404'        => '404',
			'breadcrumb_attachment' => 'Attachment/Media',
		);

		foreach ( $settings as $setting => $label ) {
			
			$wp_customize->add_setting(
				$this->get_field_name( $setting ),
				array(
					'default' => $this->get_default_value( $setting ),
					'type'    => 'option',
				)
			);

			$wp_customize->add_control(
				'bizznis_' . $setting,
				array(
					'label'    => $label,
					'section'  => 'nav',
					'settings' => $this->get_field_name( $setting ),
					'type'     => 'checkbox',
					'priority' => $priority->add(),
				)
			);

		}

	}

	private function comments( $wp_customize ) {

		//* Setting the priority
		$priority = new Bizznis_Prioritizer();
		
		$wp_customize->add_section(
			'bizznis_comments',
			array(
				'title'    => __( 'Comments and Trackbacks', 'bizznis' ),
				'priority' => $priority->add(),
			)
		);
		
		$settings = array(
			'comments_posts' => __( 'Enable Comments on Posts?', 'bizznis' ),
			'comments_pages' => __( 'Enable Comments on Pages?', 'bizznis' ),
			'trackbacks_posts' => __( 'Enable Trackbacks on Posts?', 'bizznis' ),
			'trackbacks_pages' => __( 'Enable Trackbacks on Pages?', 'bizznis' ),
		);

		foreach ( $settings as $setting => $label ) {

			$wp_customize->add_setting(
				$this->get_field_name( $setting ),
				array(
					'default' => $this->get_default_value( $setting ),
					'type'    => 'option',
				)
			);

			$wp_customize->add_control(
				'bizznis_' . $setting,
				array(
					'label'    => $label,
					'section'  => 'bizznis_comments',
					'settings' => $this->get_field_name( $setting ),
					'type'     => 'checkbox',
					'priority' => $priority->add(),
				)
			);

		}

	}

	private function archives( $wp_customize ) {

		//* Setting the priority
		$priority = new Bizznis_Prioritizer();
		
		$wp_customize->add_section(
			'bizznis_archives',
			array(
				'title'       => __( 'Content Archives', 'bizznis' ),
				'description' => __( 'These options will affect any blog listings page, including archive, author, blog, category, search, and tag pages.', 'bizznis' ),
				'priority' => $priority->add(),
			)
		);

		//* Setting key and default value array
		$settings = array(
			'content_archive'           => 'full',
			'content_archive_limit'     => '',
			'content_archive_thumbnail' => 0,
			'image_size'                => '',
			'image_alignment'           => 'alignleft',
			'posts_nav'                 => 'numeric',
		);

		foreach ( $settings as $setting => $default ) {

			$wp_customize->add_setting(
				$this->get_field_name( $setting ),
				array(
					'default' => $this->get_default_value( $setting ),
					'type'    => 'option',
				)
			);

		}

		$wp_customize->add_control(
			'bizznis_content_archive',
			array(
				'label'    => __( 'Select one of the following', 'bizznis' ),
				'section'  => 'bizznis_archives',
				'settings' => $this->get_field_name( 'content_archive' ),
				'type'     => 'select',
				'choices'  => array(
					'full'     => __( 'Display post content', 'bizznis' ),
					'excerpts' => __( 'Display post excerpts', 'bizznis' ),
				),
				'priority' => $priority->add(),
			)
		);

		$wp_customize->add_control(
			'bizznis_content_archive_limit',
			array(
				'label'    => __( 'Limit content to how many characters? (0 for no limit)', 'bizznis' ),
				'section'  => 'bizznis_archives',
				'settings' => $this->get_field_name( 'content_archive_limit' ),
				'priority' => $priority->add(),
			)
		);
		
		$wp_customize->add_control(
			new Bizznis_Customize_Misc_Control(
				$wp_customize,
				'featured_image_info',
				array(
					'section'  => 'bizznis_archives',
					'type'     => 'info',
					'label'    => __( 'Featured image', 'bizznis' ),
					'priority' => $priority->add(),
				)
			)
		);

		$wp_customize->add_control(
			'bizznis_content_archive_thumbnail',
			array(
				'label'    => __( 'Display the featured image?', 'bizznis' ),
				'section'  => 'bizznis_archives',
				'settings' => $this->get_field_name( 'content_archive_thumbnail' ),
				'type'     => 'checkbox',
				'priority' => $priority->add(),
			)
		);

		$wp_customize->add_control(
			'bizznis_image_size',
			array(
				'label'    => __( 'Featured image size', 'bizznis' ),
				'section'  => 'bizznis_archives',
				'settings' => $this->get_field_name( 'image_size' ),
				'type'     => 'select',
				'choices'  => bizznis_get_image_sizes_for_customizer(),
				'priority' => $priority->add(),
			)
		);

		$wp_customize->add_control(
			'bizznis_image_alignment',
			array(
				'label'    => __( 'Featured image alignment', 'bizznis' ),
				'section'  => 'bizznis_archives',
				'settings' => $this->get_field_name( 'image_alignment' ),
				'type'     => 'select',
				'choices'  => array(
					'' 			 => __( '- None -', 'bizznis' ),
					'alignleft'  => __( 'Left', 'bizznis' ),
					'alignright' => __( 'Right', 'bizznis' ),
				),
				'priority' => $priority->add(),
			)
		);

		$wp_customize->add_control(
			'bizznis_posts_nav',
			array(
				'label'    => __( 'Navigation technique', 'bizznis' ),
				'section'  => 'bizznis_archives',
				'settings' => $this->get_field_name( 'posts_nav' ),
				'type'     => 'select',
				'choices'  => array(
					'prev-next' => __( 'Previous / Next', 'bizznis' ),
					'numeric'   => __( 'Numeric', 'bizznis' ),
				),
				'priority' => $priority->add(),
			)
		);

	}
	
	private function custom_scripts( $wp_customize ) {
	
		//* Setting the priority
		$priority = new Bizznis_Prioritizer( 110, 1 );
		
		$wp_customize->add_section(
			'bizznis_acripts',
			array(
				'title'    => __( 'Scripts', 'bizznis' ),
				'priority' => $priority->add(),
			)
		);

		$settings = array(
			'header_scripts' => array(
				'label' 		=> sprintf( __( '%s scripts', 'bizznis' ), '<code>wp_head()</code>' ),
				'description'   => sprintf( __( 'The %1$s hook executes immediately before the closing %2$s tag in the document source.', 'bizznis' ), '<code>wp_head()</code>', '<code>&lt;/head&gt;</code>' ),
			),
			'footer_scripts' => array(
				'label' 		=> sprintf( __( '%s scripts', 'bizznis' ), '<code>wp_footer()</code>' ),
				'description'   => sprintf( __( 'The %1$s hook executes immediately before the closing %2$s tag in the document source.', 'bizznis' ), '<code>wp_footer()</code>', '<code>&lt;/body&gt;</code>' ),
			),
		);

		foreach ( $settings as $setting => $name ) {

			$wp_customize->add_setting(
				$this->get_field_name( $setting ),
				array(
					'default' => $this->get_default_value( $setting ),
					'type'    => 'option',
				)
			);
			
			$wp_customize->add_control(
				new Bizznis_Customize_Misc_Control(
					$wp_customize,
					'bizznis_' . $setting,
					array(
						'label'        => $name['label'],
						'description'  => $name['description'],
						'section'      => 'bizznis_acripts',
						'type'         => 'textarea',
						'settings'     => $this->get_field_name( $setting ),
						'priority'     => $priority->add(),
					)
				)
			);	

		}
		
	}
	
	private function header( $wp_customize ) {
		
		//* Allows these settings to update asynchronously in the Preview pane.
		$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
		$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
	
		//* Header Selector
		if ( current_theme_supports( 'bizznis-custom-header' ) || current_theme_supports( 'custom-header' ) ) {
			return;
		}
		
		//* Setting the priority
		$priority = new Bizznis_Prioritizer( 10, 1 );
		
		//* Setting key and default value array
		$settings = array(
			'blog_title'          => 'text',
			'hide_site_title'     => 0,
			'hide_tagline' 	      => 0,
		);

		foreach ( $settings as $setting => $default ) {

			$wp_customize->add_setting(
				$this->get_field_name( $setting ),
				array(
					'default' => $this->get_default_value( $setting ),
					'type'    => 'option',
				)
			);

		}

		$wp_customize->add_control(
			'bizznis_blog_title',
			array(
				'label'    => __( 'Display Option', 'bizznis' ),
				'section'  => 'title_tagline',
				'settings' => $this->get_field_name( 'blog_title' ),
				'type'     => 'select',
				'choices'  => array(
					'text' 		=> __( 'Site Title &amp; Tagline', 'bizznis' ),
					'image' 	=> __( 'Image logo', 'bizznis' ),
				),
				'priority' => $priority->add(),
			)
		);
		
		$wp_customize->add_control(
			new Bizznis_Customize_Misc_Control(
				$wp_customize,
				'header_info',
				array(
					'section'     => 'title_tagline',
					'type'        => 'info',
					'description' => __( 'This option will either pick the header image logo.png in your child theme\'s style.css file or show site title and tagline.', 'bizznis' ),
					'priority'    => $priority->add()
				)
			)
		);
		
		//* Change priority for Site Title
		$site_title           = $wp_customize->get_control( 'blogname' );
		$site_title->priority = $priority->add();

		$wp_customize->add_control(
			'bizznis_hide_site_title',
			array(
				'label'    => __( 'Remove Site Title?', 'bizznis' ),
				'section'  => 'title_tagline',
				'settings' => $this->get_field_name( 'hide_site_title' ),
				'type'     => 'checkbox',
				'priority' => $priority->add(),
			)
		);
		
		//* Change priority for Tagline
		$site_description = $wp_customize->get_control( 'blogdescription' );
		$site_description->priority = $priority->add();
		
		$wp_customize->add_control(
			'bizznis_hide_tagline',
			array(
				'label'    => __( 'Remove Tagline?', 'bizznis' ),
				'section'  => 'title_tagline',
				'settings' => $this->get_field_name( 'hide_tagline' ),
				'type'     => 'checkbox',
				'priority' => $priority->add(),
			)
		);
		
	}
	
	private function background( $wp_customize ) {
	
		//* Color Selector
		if ( ! current_theme_supports( 'custom-background' ) ) {
			return;
		}
		
		//* Setting the priority
		$priority = new Bizznis_Prioritizer( 10, 5 );
		
		//* Rename Background Image section to Background
		$wp_customize->get_section( 'background_image' )->title = __( 'Background', 'bizznis' );

		//* Move Background Color to Background section
		$wp_customize->get_control( 'background_color' )->section = 'background_image';
		
		//* Reset priorities on existing controls
		$wp_customize->get_control( 'background_color' )->priority = $priority->add();
		$wp_customize->get_control( 'background_image' )->priority = $priority->add();
		$wp_customize->get_control( 'background_repeat' )->priority = $priority->add();
		$wp_customize->get_control( 'background_position_x' )->priority = $priority->add();
		$wp_customize->get_control( 'background_attachment' )->priority = $priority->add();
		
	}

}

/**
 * 
 */
add_action( 'init', 'bizznis_customizer_init' );
function bizznis_customizer_init() {
	new Bizznis_Customizer;
}
