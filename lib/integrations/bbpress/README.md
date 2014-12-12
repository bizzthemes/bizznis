This integration allows you to seamlessly integrate bbPress with the Bizznis Parent and Bizznis child themes.

##Description

This integration does the following:

* Adds option for a forum specific sidebar
* Adds option to control the layout of your forum, separate from Bizznis
* Adds Bizznis Layout Controls for Forums and Topics
* If a forum has a specific layout set, all topics in that forum will use that layout, unless topic also has a specific layout selected.
* Adds Bizznis SEO Controls for Forums and Topics
* Adds Bizznis Script Controls for Forums and Topics

The forum sidebar and layout options are located on the Bizznis Settings page, look for 'bbPress Integration'.

##Installation

1. Add this code to your Bizznis child theme's `functions.php` file: `add_theme_support( 'bizznis-bbpress' );`
2. That's it. Navigate to your forum pages and you should see the new bbPress in action.

##Template Customization

Take copies of bbPress theme files, located inside `wp-content/plugins/templates/default/bbpress/` 
and place these copies in a folder called `bbpress` in the root of your child theme's main folder, 
like this: `wp-content/themes/my-child-theme/bbpress/`. Now modify them however you like.

##How does the integration handle bbPress's CSS?

Bizznis Integration for bbPress does not modify bbPress's way of working with CSS. By default,
bbPress provides its own `bbpress.css` file containing basic styles for the shop pages which is located here:
`wp-content/plugins/templates/default/bbpress/css/bbpress.css`.

If you decide to use the bbPress CSS and wish to customize its styles, do *not* edit the `bbpress.css` file.
Instead, make a copy of this file, rename it `bbpress.css` and place it in your child theme's `bbpress/css` folder,
and make all your edits in this file. This ensures that you do not lose your CSS customizations when bbPress is updated.

Alternatively, you can add your bbPress styles to your child theme's main style.css stylesheet. In this case,
you should disable the bbPress built-in stylesheet: add this code to your child theme's `functions.php` file:
`add_filter( 'bbp_default_styles', 'remove_bbp_styles' );
function remove_bbp_styles(){
    return array();
}`

If you are using a Bizznis child theme specially designed for bbPress, refer to the theme's documentation to find out
if all of the above has been been taken care of for you already.

##Technical Info

Read all here: http://codex.bbpress.org/
