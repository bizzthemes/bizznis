#Bizznis Translation Instructions

##Quick Overview
	If you are familiar with WordPress and the many plugins and themes available for it, 
	you've probably come across some strangely named files like .mo, .po, and .pot. 
	This file explains how to take a .po file that is included with this theme and 
	translate it into your native language.

##How to translate

###1. Download Poedit

To be able to translate a theme, you'll need a program called Poedit. It's available for Windows, Mac and Linux. If you're using Windows or Mac, you can download it [here] (http://www.poedit.net/download.php). If you're on Linux, get it through your distributions package distribution system.

###2. Edit the English .pot file that came with this theme.

Open file that is located inside `lib/languages/` folder and edit it's contents using the Poedit editor. Go through it and translate all the text one line at a time in the bottom box.

###3. Save the translated strings

####a) Save Bizznis languages folder (will be lost with theme update)

When you are finished translation the text strings, save the file in same folder, original .pot is located: 'File' => 'Save as' to `/lib/languages/` folder. Poedit will now ask you to name your file. This is very important (although easy to fix later). The naming convention for these files is the two letter language code, in lowercase, followed by an underscore, followed by the country code in uppercase. By default it would be `en_US` for US English, but you can use any language code. For instance, for Brazilian Portuguese it would be called `pt_BR`, and for a non-specific Portuges file it would be jut `pt`. For UK English it would be en_UK. This will output a `.po` and `.mo` file. No need to worry why two files were created, let's just say .mo is extremely compressed for faster execution and can only be read on machine-level.

####b) Save into WP languages folder (will not be lost with theme update)

When you are finished translation the text strings, save the file in WordPress languages folder: 'File' => 'Save as' to `/wp-content/languages/bizznis/` folder. If folder `languages` and subfolder `bizznis` do not exist, create them. Poedit will now ask you to name your file. This is very important (although easy to fix later). The naming convention for these files is the two letter language code, in lowercase, followed by an underscore, followed by the country code in uppercase. By default it would be `en_US` for US English, but you can use any language code. For instance, for Brazilian Portuguese it would be called `pt_BR`, and for a non-specific Portuges file it would be jut `pt`. For UK English it would be en_UK. This will output a `.po` and `.mo` file. No need to worry why two files were created, let's just say .mo is extremely compressed for faster execution and can only be read on machine-level.

###4. Define your language

Open `wp-config.php`, located in WordPress root directory and search for this line of code: `define ('WPLANG', '');`
Add your own country code, Brasil as an example: `define ('WPLANG', 'pt_BR');`
Add the same country code to .mo file you've saved in `/lib/languages/` folder.

###5. You're done!

##Support

Please visit http://bizzthemes.com/support/ for theme support.
