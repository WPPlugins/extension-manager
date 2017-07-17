=== Extension Manager ===
Contributors: chschenk
Donate link: http://www.christianschenk.org/donation/
Tags: install, update, upgrade, delete, search, plugins, themes, admin
Requires at least: 2.0
Tested up to: 2.6.3
Stable tag: 0.6.6

Update (17. November 2008): I keep this plugin here for reference but since WordPress 2.7 there's a [Plugin Installer](http://codex.wordpress.org/Version_2.7#Plugin_Installer) and even a [WordPress Upgrader](http://codex.wordpress.org/Version_2.7#WordPress_Upgrader). This made this plugin obsolete and I won't spend any time on it any more.

This plugin helps you to install, upgrade, delete and search for plugins and themes.

== Description ==

If you're a WordPress admin you probably want a plugin with the following features:

* install, update and delete plugins and themes
* it should be able to handle various locations, i.e. at least these [plugin repositories](http://codex.wordpress.org/Plugins#Plugin_Repositories "WordPress Codex Plugin Repositories") and maybe some from [somewhere](http://www.google.com/search?q=wordpress%20plugins "Google search on WordPress plugins") else.
* basic search functionality for all these plugins and themes

This plugins lets you do (almost) all of these things.

Have a look at this [video](http://www.youtube.com/watch?v=-BzX7bv3DgM "Introduction to Extension Manager for WordPress") to see how it works.

== Installation ==

Before you begin with the installation read the [notes on compatibility](http://wordpress.org/extend/plugins/extension-manager/other_notes/).

1. Unzip the plugin into your wp-content/plugins directory
2. Create these directories inside wp-content/plugins/extension-manager:
	* plugins
	* themes
3. Change the permissions of the wp-content/plugins directory to 0777. Alternatively you can change the owner of this directory to the same user who is running the webserver.
4. Do the same thing you did in the last step with these directories:
	* wp-content/themes
	* wp-content/plugins/extension-manager/plugins
	* wp-content/plugins/extension-manager/themes
5. Activate the plugin at the plugin administration page
6. Start using the plugin (Options -> Extension Manager).

== Frequently Asked Questions ==

= I've successfully installed a plugin/theme but it doesn't show up on the plugins/themes screen. =

If this happens you can't activate the plugin/theme because the content
of the ZIP file that contains the plugin/theme is broken. There are some
plugins/themes around that have some extra directories in their ZIP
files, e.g.:

* "wp-content/plugins/[plugin-name]/[files]"
* "wp-content/themes/[theme-name]/[files]"
* "[plugin-name]/[plugin-name]/[files]"

I don't really want to support this stuff since this would lead to some
extra guess-work.

I recommend this: if you encounter a "broken" plugin/theme just contact
the author and politely ask him whether he might fix this. The standard
layout of a ZIP file should be "[plugin/theme-name]/[files...]".

== Screenshots ==

1. Extension Manager start screen
2. Install plugins
3. Install themes (not implemented yet)
4. Remove installed and downloaded plugins and themes

== Compatibility ==

This plugin was tested with PHP 5.2.x. Currently it won't work with
older versions, i.e. PHP 4. Please be patient until I've fixed that.

But it's possible that this plugin will never work with PHP 4 because
it's likely that I will not find the time to implement that. So please
consider updating your installation of PHP.

If you'd like to help me fixing compatibility issues write me an [e-mail](http://www.christianschenk.org/legal-notice/#contact "Contact me").

== Videos ==

Have a look at this [video](http://www.christianschenk.org/projects/wordpress-extension-manager/video-for-this-plugin/ "Introduction to Extension Manager for WordPress") to see how it works.

== Licence ==

This plugin is released under the GPL.

== Translation ==

This plugin is available in these languages:

* English
* German (I'm working on it)

If you want to help me translating it into other languages [drop me a line](http://www.christianschenk.org/legal-notice/#contact "Contact me").
