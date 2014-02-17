=== Envoke Supersized ===
Contributors:      cmmarslender, dillonmccallum, envoke
Tags: 			   supersized, slideshow, fullscreen, background, gallery, image, images, plugin, custom post type, javascript, jquery, slider, media, picture, pictures
Requires at least: 3.5.1
Tested up to:      3.8
Stable tag:        2.1.3
License:           GPLv2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

This plugin creates an easy to use interface for managing the Supersized jQuery Plugin on your site.

== Description ==

This plugin creates an easy to use interface for managing the Supersized jQuery Plugin on your WordPress site. You have
the option to define a global set of images as well as set up an image, title, and caption on a per post basis. The per
post images will override the global images if set for a particular post.

Find a bug or have suggestions for improvment? You can contact us through our website
[Envoke Design](http://envokedesign.com/ "Web Design Portland") or create an issue using our
[Issue Tracker](https://bitbucket.org/envokedesign/envoke-supersized/issues)

== Installation ==

= Manual Installation =

1. Upload the entire `/envoke-supersized` directory to the `/wp-content/plugins/` directory.
2. Activate Envoke Supersized through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

= How many images can I use for my Supersized background? =

You can use as few or as many as you would like when using the Supersized Slides post type. Per post overrides currently
only support a single image, but supporting multiple images on a per post basis is a possibility in the future, if
enough people want the feature.

= What if I find a bug or have a suggestion for improvements? =

Let us know. You can contact us through our website [Envoke Design](http://envokedesign.com/ "Web Design Portland") or
create an issue using our [Issue Tracker](https://bitbucket.org/envokedesign/envoke-supersized/issues)

= Only some of my slides are loading. How can I load more? =

The plugin currently limits the number of slides that will load on any given page to 50. For most people, this is more
than plenty, but if you need to increase this, there is a filter available for this purpose - 'enss-max-images'. For
example, to increase the number of images to 75, you could add the following line to your functions.php file:
`add_filter( 'enss-max-images', function( $number ) { return 75; });`

= I was to be able to override the background for a custom post type. How can I do this? =

By default, only posts and pages have the override meta box available. To add it to other post types, you can use the
 'enss-override-post-types' filter. The filter gets passed an array of supported post types. To add one, you can just
 append the post type to the array, then return the modified array. See
 [this issue](https://bitbucket.org/envokedesign/envoke-supersized/issue/14/override-background-dont-show-in-custom)
 for an example of how to implement this.

== Screenshots ==

1. Example of the Envoke-Supersized plugin in action on our own website.

== Changelog ==

= 2.1.3 =
* Fix: Removed no longer used css that could conflict with some themes.

= 2.1.2 =
* Fix: Only check for overrides where is_singular() returns true, since they aren't supported anywhere else
* Fix: Call wp_reset_query() to account for themes and other plugins that modify the global objects, without resetting them
* Fix: Use get_post_type() instead of accessing _post_type directly, to make sure filters are always applied
* New: Added a filter to allow arbitrarily disabling the plugin by returning false (per page, etc)
* New: Added a body class 'enss' when the plugin is enabled and has slides for the page

= 2.1.1 =
* Fixed missing stylesheet issue with 2.1.0
* Adds an icon for the menu item

= 2.1.0 =
* Added thumbnails to the list of slides in the admin.

= 2.0.3 =
* Fixes display issues related to themes targeting all ul elements on a page with default margin and padding.

= 2.0.2 =
Version 2.0.2 fixes missing translations and a few bugfixes.

* Increased images that load by default on front end. Added filter 'enss-max-images' to easily alter this, if necessary.
* Made sure that all settings can be translated.

= 2.0.0 =
Version 2.0.0 brings many bug fixes and improvements, and a lot of under-the-hood improvements

* Title and Caption fields have been added for the images set on a per post basis
* Better support for translations
* Ability to choose between multiple image overlay options, or turn them off completely
* Ability to enable or disable UI elements on the front end
* Fixed positioning problem with title and caption on front end
* Fixed missing images on certain front end UI elements
* Fixed bug where the loader would never go away if no images were set up
* Created a public issue tracker, so that its easier for users to file issues, and watch any existing issues with the plugin

= 1.3.2 =
* Added a comma that was lost from last update

= 1.3.1 =
* Fixed bug that prevented the plugin from working correctly in some versions of Internet Explorer

= 1.3.0 =
* Removed some unnecessary debugging code
* Security Improvements

= 1.2.0 =
* Added ability to specify a single, custom image and a per page/post basis

= 1.1.0 =
* Improvements to the settings page

= 1.0.3 =
* Readme File Fixes and correcting some links

= 1.0.2 =
* Minor Bug Fixes

= 1.0.1 =
* Fixed broken links to images in the supersized_assets dir

= 1.0 =
* The first publicly available version of the Envoke Supersized plugin

== Upgrade Notice ==

= 2.1.3 =
Removed no longer used css that could conflict with some themes.

= 2.1.2 =
Fixes issue where overrides and slides would conflict on non-singular pages. Thanks Bozz for reporting the issue and
helping debug!
Adds a filter that allows arbitrarily disabling the plugin.
Adds a body class whenever we output slides to a page "enss"

= 2.1.1 =
Fixes a missing admin stylesheet

= 2.1.0 =
Version 2.1.0 adds thumbnails to the list of slides in the admin.

= 2.0.3 =
Version 2.0.3 fixes display issues that were seen in themes that targeted all ul elements with default styling.

= 2.0.0 =
Version 2.0.0 brings a lot of under-the-hood improvements, bugfixes, and better support for titles and captions. Image
overlays now can be turned off, and there are multiple options for what overlay to use.

= 1.3.1 =
Fixed bug that prevented the plugin from working correctly in some versions of Internet Explorer

= 1.3.0 =
Security Improvements

= 1.2.0 =
You can now set a single, custom image on a per page/post basis, to override the default slideshow
