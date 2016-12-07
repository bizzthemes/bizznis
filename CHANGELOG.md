# Bizznis theme Change Log

### 1.4.0
* Improved Allow custom post classes on Ajax requests to account for endless scroll.
* Improved Remove the top buttons (save and reset) from Bizznis admin classes.
* Improved Remove right float on admin buttons (settings screens, etc.).
* Improved Change "Save Settings" to "Save Changes", as WordPress core does.
* Improved Use version constant rather than database setting for reporting theme version in Settings.
* Improved Use sfHover for superfish hover state.
* Improved Apply identifying class to entry image link.
* Improved Prevent empty footer widgets markup.
* Improved Prevent empty spaces in entry footer of CPTs.
* Improved Trim filtered value of entry meta.
* Improved Add a toolbar link to edit CPT archive settings.
* Improved Add filter for the viewport meta tag value.
* Improved Add shortcodes for site title and home link.
* Improved Update and simplify favicon markup for the modern web.
* Improved Prevent author shortcode from outputting empty markup when no author is assigned.
* Improved Disable author box on entries where post type doesn't support author.
* Improved Change the label on the update setting to reflect what it actually does, check for updates.
* Improved Add filters for Bizznis default theme support items.
* Improved Update theme tags.
* Improved Enable after entry widget area for all post types via post type support.
* Improved Hide layout selector when only one layout is supported.
* Improved Disable author shortcode output if author is not supported by post type.
* Improved Improve image size retreival function and usage.
* Improved Add ability to specify post ID when using bizznis_custom_field().
* Improved Update to normalize.css 5.0
* Improved Add admin notice when Bizznis is activated directly.
* Improved Removed unnecessary warning from theme description in style.css.
* Improved Use TinyMCE for archive intro text input.
* Improved Allow foreign language characters in content limit functions.
* Improved Pass entry image link through markup API.
* Improved Add a11y to the paginaged post navigation.
* Improved Allow adjacent single entry navigation via post type support.
* Improved Fix issue with no sitemap when running html5 and no a11y support for 404 page.
* Improved Added relative_depth parameter to date shortcodes.
* Improved Exclude posts page from page selection dropdown in Featured Page widget.

### 1.3.5
* Improved - term meta queries now simplified

### 1.3.4
* Tweak - schema.org for blog post turnet to CreativeWork
* Tweak - taxonomy terms option work better

### 1.3.3
* Fix - Commments not displaying

### 1.3.2
* Feature - Responsive options metaboxe tables
* Tweak - Breadcrumbs options labels
* Tweak - Posts pagination rel
* Tweak - Comments now controlled by WP core options only
* Tweak - Moved header aside widget area after Primary Sidebar
* Fix - Options saving

### 1.3.0
* Fixed issue with Schema.org microdata when using Blog template.
* Add H1 to posts page when using static front page and theme supports a11y.
* Better logic for generating H1 on front page
* Removed incorrect usage of mainContentOfPage.
* Added helper function to filter markup to add .screen-reader-text class to markup.
* Fixed breadcrumb Schema.org microdata for breadcrumb items.
* Prevent duplicate H1 elements on author archives.
* Only output http://schema.org/WebSite on front page.
* Add boolean attribute option to markup API.
* Pass archive title/description wrappers through markup API.
* Remove a11y checks for titles that were previously output by default.
* Added accesibility for menus by default
* Moved all assets (images, css, js) to assets folder
* Updated admin images for Bizznis branding
* Updated HTML5 shiv file for old IE
* Reponsive extra menu option with integrated 'bizznis-responsive-menu' support
* Updated translations

### 1.2.6
* Post meta support for custom post types
* Fixed breadcrumbs
* Archive title rendered only when accessiblity is supported

### 1.2.5
* SCHEMA fixes
* Theme support functions now hooked to core instead of "after_setup_theme()" hook
* Return rem insted of px in CSS

### 1.2.4
* Moved all core support functions inside "after_setup_theme()" hook
* Fixed all texdomain issues for better WP.org language pack compatibility

### 1.2.3
* WooCommerce integration fixes
* Better design markup

### 1.2.2
* Adjusted customizer extra menu controls
* Better accessibility
* Better markup for SEO

### 1.2.1
* Fixed a bug with customizer

### 1.2.0
* Added accessibility to all areas
* Fixed header area widgets display
* Added print styles
* Better theme support handling
* Better customizer options

### 1.1.8
* Added hook to header section

### 1.1.7
* Added site wrappers as filterable functions
* Added more landing pages
* Added option to remove post title on individual posts

### 1.1.6
* Updated screenshot
* Added content boxes to editor-style.css
* removed logo images
* added back backgroud image
* restructured premium link
* removed update checks
* Fixed customizer link

### 1.1.5
* Modernized design
* Fixed some broken admin links
* Remove default background image
* Added new logo images
* Added link to child themes in customizer

### 1.1.4
* Added support for Jetpack logo
* Removed wp_title from WP 4.1 onwards

### 1.1.3
* Removed several $wp_query global variables for faster loading

### 1.1.2
* Removed Custom scripts from Customizer
* Fixed options query
* Optimized HTML5 markup

### 1.1.1
* Fixed breadcrumbs
* Fixed double DB menu query
* Added custom CSS support to customizer
* Removed rem units from CSS styling

### 1.1.0
* Moved all theme settings to customizer
* Moved all SEO to a plugin
* Moved all Import/export tools to a plugin
* Removed all globals and made everything load at least 30% faster
* Removed all clutter code and optimized the site structure
* More HTML5 declarations inside comments and posts

### 1.0.9.1
* Fixed favicon not defined notice

### 1.0.9
* Removed pingback options checking
* Removed theme about page
* Removed default shortcode

### 1.0.8

* Removed seo meta tags
* Removed blog template

### 1.0.7

* Removed default mobile responsive nav menu
* Created a bizznis.pot language file instead of en_EN.po

### 1.0.6

* Polished overall design
* Moved theme SEO into a plugin
* Removed theme link in footer, only theme author link by default
* Removed themu update email notification feature

### 1.0.5

* Updated overall design
* Added Page list widget
* Updated SEO controls: added Google+ publisher meta links
* Removed front-page.php template

### 1.0.4

* Added the_post_thumbnail to process featured images

### 1.0.3

* Added editor-style file to match TinyMCE with actual content
* Added theme settings to admin bar on front

### 1.0.2

* Added theme settings menu to admin bar on front
* Restructured theme settings from metaboxes to from fields

### 1.0.1

* Updated screenshot images of the theme
* Fixed outbound links to theme support pages

### 1.0.0

* Initial Public Release
