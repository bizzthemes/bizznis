<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

/**
 * Prevent direct access to this file
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( _( 'Sorry, you are not allowed to access this file directly.', 'bizznis' ) );
}

/**
 * Load the Bizznis-fied templates, instead of the WooCommerce defaults.
 *
 * Hooked to 'template_include' filter
 *
 * This template loader determines which template file will be used for the requested page, and uses the
 * following hierarchy to find the template:
 * 1. First looks in the child theme's 'woocommerce' folder.
 * 2. If no template found, falls back to BWC's templates.
 *
 * For taxonomy templates, first looks in child theme's 'woocommerce' folder and searches for term specific template,
 * then taxonomy specific template, then taxonomy.php. If no template found, falls back to BWC's taxonomy.php.
 *
 * BWC provides three templates in the plugin's 'templates' directory:
 * - single-product.php
 * - archive-product.php
 * - taxonomy.php
 *
 * Users can override BWC templates by placing their own templates in their child theme's 'woocommerce' folder.
 * The 'woocommerce' folder must be a folder in the child theme's root directory, eg themes/my-child-theme/woocommerce
 * Permitted user templates (as per WP Template Hierarchy) are:
 * - single-product.php
 * - archive-product.php
 * - taxonomy-{taxonomy-name}-{term-name}.php
 * - taxonomy-{taxonomy-name}.php
 * - taxonomy.php
 *
 * Note that in the case of taxonomy templates, this function accommodates ALL taxonomies registered to the
 * 'product' custom post type. This means that it will cater for users' own custom taxonomies as well as WooC's.
 *
 * @since 1.0.0
 */
function bizznis_wc_template_loader( $template ) {
	if ( is_single() && 'product' == get_post_type() ) {
		$template = locate_template( array( 'woocommerce/single-product.php' ) );
		if ( ! $template ) {
			$template = BIZZNIS_WC_TEMPLATES_DIR . '/single-product.php';
		}
	}
	elseif ( is_post_type_archive( 'product' ) ||  is_page( get_option( 'woocommerce_shop_page_id' ) ) ) {
		$template = locate_template( array( 'woocommerce/archive-product.php' ) );
		if ( ! $template ) {
			$template = BIZZNIS_WC_TEMPLATES_DIR . '/archive-product.php';
		}
	}
	elseif ( is_tax() ) {
		$term = get_query_var( 'term' );
		$tax = get_query_var( 'taxonomy' );
		# Get an array of all relevant taxonomies
		$taxonomies = get_object_taxonomies( 'product', 'names' );
		if ( in_array( $tax, $taxonomies ) ) {
			$tax = sanitize_title( $tax );
			$term = sanitize_title( $term );
			$templates = array(
				'woocommerce/taxonomy-'.$tax.'-'.$term.'.php',
				'woocommerce/taxonomy-'.$tax.'.php',
				'woocommerce/taxonomy.php',
			);
			$template = locate_template( $templates );
			# Fallback to BWC template
			if ( ! $template ) {
				$template = BIZZNIS_WC_TEMPLATES_DIR . '/taxonomy.php';
			}
		}
	}
	return $template;
}

/**
 * Displays shop items for archives (taxonomy and main shop page)
 *
 * Uses WooCommerce structure and contains all existing WooCommerce hooks
 *
 * Code based on WooCommerce templates/archive-product.php
 *
 * @since 1.0.0
 */
function bizznis_wc_content_product() {
	/**
	 * woocommerce_before_main_content hook
	 *
	 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
	 * @hooked woocommerce_breadcrumb - 20
	 */
	do_action( 'woocommerce_before_main_content' );
?>
	
	<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
	
		<header class="entry-header">
			<h1 class="entry-title" itemprop="headline"><?php woocommerce_page_title(); ?></h1>
		</header>
		
	<?php endif; ?>
			
	<?php do_action( 'woocommerce_archive_description' ); ?>
			
	<?php if ( have_posts() ) : ?>

		<?php
			/**
			 * woocommerce_before_shop_loop hook
			 *
			 * @hooked woocommerce_result_count - 20
			 * @hooked woocommerce_catalog_ordering - 30
			 */
			do_action( 'woocommerce_before_shop_loop' );
		?>
		
		<?php woocommerce_product_loop_start(); ?>
	
			<?php woocommerce_product_subcategories(); ?>
		
			<?php while ( have_posts() ) : the_post(); ?>

				<?php wc_get_template_part( 'content', 'product' ); ?>

			<?php endwhile; // end of the loop. ?>
			
		<?php woocommerce_product_loop_end(); ?>

		<?php
			/**
			 * woocommerce_after_shop_loop hook
			 *
			 * @hooked woocommerce_pagination - 10
			 */
			do_action( 'woocommerce_after_shop_loop' );
		?>
	
	<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>

		<?php wc_get_template( 'loop/no-products-found.php' ); ?>

	<?php endif; ?>
	
<?php
	/**
	 * woocommerce_after_main_content hook
	 *
	 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
	 */
	do_action( 'woocommerce_after_main_content' );
}