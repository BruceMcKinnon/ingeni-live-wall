=== Ingeni Live Wall ===

Contributors: Bruce McKinnon
Tags: live wall
Requires at least: 4.8
Tested up to: 5.1.1
Stable tag: 2020.03

A live wall of logos, sourced from a common folder.



== Description ==

* - Images are added by adding them to a folder (hosted on the web server).





== Installation ==

1. Upload the 'ingeni-live-wall' folder to the '/wp-content/plugins/' directory.

2. Activate the plugin through the 'Plugins' menu in WordPress.

3. Display the carousel using the shortcode



== Frequently Asked Questions ==



= How do a display the live wall? =

Use the shortcode [ingeni-live-wall]

The following parameters may be included:


source_path: Directory relative to the home page that contains the images to b displayed. Defaults to '/photos-bucket/',

wrapper_class: Wrapping class name. Defaults to 'ingeni-slick-wrap'.

shuffle: Randomly shuffle the order of the images. Defaults to 1 (shuffle images).

speed: msecs to display image. Defaults to 3000 (3 secs). The animation speed is 1/3 of this value.

anim_type: Animation type. Defaults to 'fadeInOut'. Possible values are:

showHide
fadeInOut
slideLeft, slideRight, slideTop, slideBottom
rotateBottom, rotateLeft, rotateRight, rotateTop
scale
rotate3d
rotateLeftScale, rotateRightScale, rotateTopScale, rotateBottomScale
random




== Changelog ==

v2020.01 - Initial version.

v2020.02 - Added individual image anchor tags

v2020.03 - A couple of code types on jQuery parameters.
