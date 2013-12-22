This integration allows you to seamlessly integrate WooCommerce with the Bizznis Parent and Bizznis child themes.

== Description ==

This integration replaces WooCommerce's built-in shop templates with its own Bizznis-ready versions,
specifically the `single-product.php`, `archive-product.php` and `taxonomy.php` templates needed to 
display the single product page, the main shop page, and Product Category and Product Tag archive pages.

To allow easy customization of these templates, and ensure that you do not lose your customizations
when the integration is updated, you can place your own copies of these templates in your child theme's
'woocommerce' folder and customize these copies as much as you like. You can also create your own
`taxonomy-{taxonomy}.php` and `taxonomy-{taxonomy}-{term}.php` templates in the same location and
this integration will find them and use them to display your shop's Product Category and Product Tag archives.
See the [Template Hierarchy](http://codex.wordpress.org/Template_Hierarchy#Custom_Taxonomies_display)
to learn more about naming requirements for taxonomy templates.

**This version is compatible with WooCommerce 2.0+**

== Installation ==

1. Add this code to your Bizznis child theme's `functions.php` file: `add_theme_support( 'bizznis-woocommerce' );`
2. That's it. Navigate to your shop pages and you should see the new templates in action.

== Template Customization ==

It's not recommended to customize the integration's templates because, if you do, you will lose any customizations
the next time the integration is updated. Instead, take copies of the integration's `single-product.php`,
`archive-product.php` and `taxonomy.php` files, and place these copies in a folder called `woocommerce`
in the root of your child theme's main folder, like this: `wp-content/themes/my-child-theme/woocommerce/`

Make sure you keep the same file names!

== Breadcrumbs ==

The modified Bizznis breadcrumbs will reflect all your existing Bizznis breadcrumb customizations too. Users
who prefer to use WooCommerce's breadcrumbs can do so by adding this to their child theme's functions.php:
- `add_theme_support( 'bizznis-woo-breadcrumbs' );`
And this to the relevant templates:
- `remove_action( 'bizznis_loop', 'bizznis_do_breadcrumbs' );`

== Shop on front page ==

1. Go to the *Dashboard > Settings > Reading* page select A Static Page and select "Shop" as the front page.
2. It is recommended to turn off Bizznis breadcrumbs for the Home page in *Dashboard > Bizznis > Theme Settings > Breadcrumb options*.

== How does the integration handle WooCommerce's CSS? ==

Bizznis Integration for WooCommerce does not modify WooCommerce's way of working with CSS. By default,
WooCommerce provides its own `woocommerce.css` file containing basic styles for the shop pages which is located here:
`wp-content/plugins/woocommerce/assets/css/woocommerce.css`.

To use this stylesheet, check the "*Enable WooCommerce CSS styles*" checkbox in the *WooCommerce Settings page > General tab*.
Alternatively, you can add this code to your child theme's `functions.php` file: `define( 'WOOCOMMERCE_USE_CSS', true );`

Note that this code takes precedence over the checkbox in the *WooCommerce Settings page > General tab*;
in other words, when you use this code, the checkbox is ignored.

If you decide to use the WooCommerce CSS and wish to customize its styles, do *not* edit the `woocommerce.css` file.
Instead, make a copy of this file, rename it `style.css` and place it in your child theme's `woocommerce` folder,
and make all your edits in this file. This ensures that you do not lose your CSS customizations when WooCommerce is updated.

Alternatively, you can add your WooCommerce styles to your child theme's main style.css stylesheet. In this case,
you should disable the WooCommerce built-in stylesheet: either uncheck the "*Enable WooCommerce CSS styles*" checkbox
in the *WooCommerce Settings page > General tab*, or a better option, add this code to your child theme's `functions.php` file:
`define( 'WOOCOMMERCE_USE_CSS', false );`

If you are using a Bizznis child theme specially designed for WooCommerce, refer to the theme's documentation to find out
if all of the above has been been taken care of for you already.

== Technical Info ==

For more technically minded users, this is what the integration does:

* Unhooks the WooCommerce template loader function
* Adds its own template loader function to control the templates used by the single product, archive product and Product Category and Product Tag (taxonomy) archive pages.
* Adds Bizznis Layouts and SEO support to the WooCommerce `Product` custom post type
* Provides three Bizznis-ready templates to display the shop pages, located in the integration's `templates` folder:
	* single-product.php
	* archive-product.php
	* taxonomy.php
* These templates use WooCommerce core functions to display the shop loops which:
	* unhook WooCommerce's built-in breadcrumbs
	* unhook the Bizznis Loop and replace it with the relevant WooCommerce shop loop
	* remove WooCommerce's #container and #content divs, which are not required or wanted by Bizznis
* The shop loop function in each template is heavily based on its WooCommerce counterpart, but has been modified to accommodate certain Bizznis features such as the Taxonomy term headings and descriptions feature.
* The templates contain the `genesis();` function and therefore are fully customisable using Bizznis hooks and filters. 
* The template loader allows users to use their own templates in the child theme's 'woocommerce' folder. These user templates, if they exist in the child theme's `woocommerce' folder, will be loaded in place of the supplied Bizznis Integration for WooCommerce templates
* Using appropriate filters, modifies the Bizznis breadcrumbs output to mimic the breadcrumb structure provided by WooCommerce's built-in breadcrumbs.

== More about breadcrumbs ==

By default, the Bizznis breadcrumbs do not provide the same breadcrumb structure as those built-in to WooCommerce.
Bizznis Integration for WooCommerce modifies the normal Bizznis Breadcrumbs output on shop pages to mimic the structure of those built-in to WooCommerce.

Note that the templates provided in this integration automatically unhook WooCommerce's built-in breadcrumbs via this code in each template:
`remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );`

== Filters ==

This integration provides some filters which may be useful for developers.

`bizznis_wc_product_archive_crumb`
Located in `bizznis_wc_get_archive_crumb_filter()` in `lib/breadcrumb.php`.
Allows further modification of the single product page breadcrumbs.

`bizznis_wc_single_product_crumb`
Located in `bizznis_wc_get_single_crumb()` in `lib/breadcrumb.php`.
Allows further modification of the product archive (shop page) breadcrumbs.

== More info about WooCommerce CSS handling ==

For the benefit of theme developers and customizers, here is a summary of possible scenarios for dealing with WooCommerce CSS:

* Case 1: If the *WooCommerce > General settings > Enable WooCommerce CSS* option is checked, the default stylesheet supplied with WooCommerce will be loaded (see `wp-content/integrations/woocommerce/assets/css/woocommerce.css`).
* Case 2: If *WooCommerce > General settings > Enable WooCommerce CSS* option is unchecked, no stylesheet is loaded.
* Case 3: If the user (or theme developer) sets `define( 'WOOCOMMERCE_USE_CSS', true );` in the child theme functions.php the options setting is ignored and the default WooCommerce stylesheet is loaded, ie has same effect as checking the settings box.
* Case 4: If the user (or theme developer) sets `define( 'WOOCOMMERCE_USE_CSS', false );` in the child theme functions.php the options setting is ignored and NO stylesheet is loaded, ie has same effect as unchecking the settings box. Note: the value of WOOCOMMERCE_USE_CSS always takes precedence over the WooCommerce Settings page option!
* If either Case 1 or Case 3 applies, if themes/my-child-theme/woocommerce/styles.css exists it will be loaded in place of the default woocommerce stylesheet (integrations/woocommerce/assets/css/woocommerce.css).
* If either Case 2 or 4 applies, as no built-in stylesheet is loaded, all WooCommerce CSS styles need to be added to the theme's main style.css stylesheet
* Note for Bizznis child theme developers: For new themes, theme developers can use `define( 'WOOCOMMERCE_USE_CSS', false );` and place all WooCommerce styles in the theme's main stylesheet, or do nothing and let the user handle this via Case 1 or 3.
* The above information is based on WooCommerce 2.0.0
