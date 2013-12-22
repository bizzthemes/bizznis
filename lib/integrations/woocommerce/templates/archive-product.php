<?php
/**
 * This template displays the archive for Products
 *
 * Note for customisers/users: Do not edit this file!
 * ==================================================
 * If you want to customise this template, copy this file (keep same name) and place the
 * copy in the child theme's woocommerce folder, ie themes/my-child-theme/woocommerce
 * (Your theme may not have a 'woocommerce' folder, in which case create one.)
 * The version in the child theme's woocommerce folder will override this template, and
 * any future updates to this plugin won't wipe out your customisations.
 *
 * @since 1.0.0
 *
 */

//* Remove default Bizznis loop
remove_action( 'bizznis_loop', 'bizznis_do_loop' );

//* Remove WooCommerce breadcrumbs
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

//* Uncomment the below line of code to add back WooCommerce breadcrumbs
//add_action( 'bizznis_loop', 'woocommerce_breadcrumb', 4, 0 );

//* Remove Woo #container and #content divs
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

/**
 * Manage page layout for the Product archive (Shop) page
 *
 * Set the layout in the Bizznis layouts metabox in the Page Editor
 *
 * @since 1.0.0
 */
add_filter( 'bizznis_pre_get_option_site_layout', 'bizznis_wc_archive_layout' );
function bizznis_wc_archive_layout( $layout ) {
	$shop_page_id = get_option( 'woocommerce_shop_page_id' );
	$layout = get_post_meta( $shop_page_id, '_bizznis_layout', true );
	return $layout;
}

/**
 * Display shop items (product custom post archive)
 *
 * This is needed thanks to substantial changes to WooC template contents
 * introduced in WooC 1.6.0.
 *
 * @since 1.0.0
 */
add_action( 'bizznis_loop', 'bizznis_wc_archive_product_loop', 5 );
function bizznis_wc_archive_product_loop() {
	global $woocommerce;	
	bizznis_wc_content_product();
}

bizznis(); #Fire the engine