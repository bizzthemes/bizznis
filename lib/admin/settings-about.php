<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

/**
 * Registers a new admin page, providing content and corresponding menu item for the "What's new" page.
 *
 * @since 1.0.0
 */
class Bizznis_Admin_About extends Bizznis_Admin_Basic {

	/**
	 * Create the page.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		$page_id = 'bizznis-about';
		$menu_ops = array(
			'submenu' => array(
				'parent_slug' => 'admin.php',
				'page_title'  => sprintf( __( 'Welcome to Bizznis %s', 'bizznis' ), PARENT_THEME_BRANCH ),
				'menu_title'  => __( 'About', 'bizznis' ),
			)
		);
		$page_ops = array(
			'screen_icon'       => 'options-bizznis'
		);		
		$this->create( $page_id, $menu_ops );
	}

	/**
	 * Callback for displaying the Bizznis Readme admin page.
	 *
	 * @since 1.0.0
	 */
	public function admin() {
		?>
		<div class="wrap about-wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<div class="about-text"><?php _e( 'This theme will make your website rock solid, secure and ideal to attract new customers.', 'bizznis' ); ?></div>
		<div class="bizznis-badge"></div>
		<div class="changelog">
			<h2 class="about-headline-callout"><?php _e( 'Forked the best theme framework <br/><small>and made it even better.</small>', 'bizznis' ); ?></h2>
			<img class="about-overview-img" src="<?php echo BIZZNIS_ADMIN_IMAGES_URL . '/admin-overview.png'; ?>" />
			<div class="feature-section col three-col about-updates">
				<div class="col-1">
					<h3><?php _e( 'Business Oriented', 'bizznis' ); ?></h3>
					<p><?php _e( 'Reliable and supported. Integrates best business website practices.', 'bizznis' ); ?></p>
				</div>
				<div class="col-2">
					<h3><?php _e( 'Secure and Lightning Fast', 'bizznis' ); ?></h3>
					<p><?php _e( 'Getting your website hacked can become a nightmare. It stops here. Now.', 'bizznis' ); ?></p>
				</div>
				<div class="col-3 last-feature">
					<h3><?php _e( 'Decisions, not Options', 'bizznis' ); ?></h3>
					<p><?php _e( 'Truckload of options are gone and every option is carefully thought out.', 'bizznis' ); ?></p>
				</div>
			</div>
			<hr/>
			<div class="feature-section col three-col about-updates">
				<div class="col-1">
					<h3><?php _e( 'HTML5, Mobile Ready', 'bizznis' ); ?></h3>
					<p><?php _e( 'It works even better on Mobile Devices. Cross-browser compatibility and app-like behaviour. ', 'bizznis' ); ?></p>
				</div>
				<div class="col-2">
					<h3><?php _e( 'Bizznis and WordPress', 'bizznis' ); ?></h3>
					<p><?php _e( 'Beautifully Integrated, incredibly Powerful. Together, they make the perfect match.', 'bizznis' ); ?></p>
				</div>
				<div class="col-3 last-feature">
					<h3><?php _e( 'Child-theme ready', 'bizznis' ); ?></h3>
					<p><?php _e( 'Do all customizations in form of a child theme and never modify Bizznis code.', 'bizznis' ); ?></p>
				</div>
			</div>
		</div>
		<div class="return-to-dashboard">
			<a href="<?php echo esc_url( menu_page_url( 'bizznis', 0 ) ); ?>"><?php _e( 'Go to &rarr; Theme Settings', 'bizznis' ); ?></a> |
			<a href="<?php echo esc_url( menu_page_url( 'bizznis-seo', 0 ) ); ?>"><?php _e( 'Go to &rarr; SEO Settings', 'bizznis' ); ?></a>
		</div>
		</div>
		<?php
	}

}