=== fotoslide ===
Author: kbradwick, markoheijnen
Tags: wps3slider, photos, image gallery, carousel, jquery, slideshow
Requires at least: 2.7
Tested up to: 3.3.1
Stable tag: 2.0

A plugin to render multiple slideshows on your site

== Description ==
This new plugin has been built from a branch of the wps3slider plugin that I developed
a little while ago. By listening to feedback from everyone I decided to rebuild a new
plugin with the same principles but with a newer approach. The result was a redeveloped
jQuery plugin for the slider and a new code structure.

The plugin is managed on Google Code. So if you have a bug to report or a feature request,
please visit the page at http://code.google.com/p/fotoslide/.

== Installation ==
1. Upload an extract to your plugins directory
2. Activate like you would any other plugin
3. Make sure the directories cache and temp directories are writable by your webserver (chmod 777 {assets/cache})
4. Galleries can be created and managed from Media > FotoSlide

== Changelog ==

= 2.0 =
* Bugfix slider so it works with recent jQuery versions
* Rewritten pagination class and added search box in add gallery item
* Show error when cache/temp folder isn't writeable
* Update timthumb.php
* Only load script on the correct pages
* Cleanup code

= 1.1 =
* Fixed admin layout for lower resolution screen sizes
* Gallery degrades properly when JavaScript is off
* Updated documentation

= 1.0 =
* The initial release