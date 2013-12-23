Parent Business Theme for WordPress
----

Developed and owned by Željan Topiæ, http://bizzthemes.com/

Quick Overview
----
	If you are familiar with WordPress and the many plugins and themes available for it, 
	you’ve probably come across some strangely named files like .mo, .po, and .pot. 
	This file explains how to take a .po file that is included with this theme and 
	translate it into your native language.

How to translate
----
	1. Download a gettext file editor like poedit and install it.
	2. Open the English .po file that came with this theme (custom/lang/en_US.po).
	3. Now go through it and translate all the text one line at a time in the bottom box.
	4. Then 'File' => 'Save as' to languages/' folder, where .po file is located. 
	   This will output a .po and .mo file.
	5. Open wp-config.php, located in WordPress root directory and search for this
	   line of code: define ('WPLANG', '');
	   Add your own country code, Brasil as an example: define ('WPLANG', 'pt_BR');
	6. Add the same country code to .mo file you've saved in 'languages/' folder.
	   Example for Brasil: pt_BR.mo
	7. You're done!

Support
----

Please visit http://bizzthemes.com/support/ for theme support.
