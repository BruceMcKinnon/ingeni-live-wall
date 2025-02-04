=== Ingeni Live Wall ===

Contributors: Bruce McKinnon
Tags: live wall
Requires at least: 4.8
Tested up to: 5.1.1
Stable tag: 2025.01

A live wall of logos, sourced from a common folder, or Woocommerce product images



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

max_thumbs: Number of images to be rendered. Default = 6

pool_thumbs: The size of the pool from which random images will be selected. Default = 10

small_cols, medium_cols, large_cols: Number of columns to be rendered for small, medium and large breakpoints. Defaults are 1, 2 and 3 images horizontally.

cat_ids: Product category IDs to include. Default is empty, so all product categories are included.

exclude_cat_ids: Product category IDs to exclude. Default is empty, so no product categories are excluded.



== Changelog ==


v2020.01 - Initial version.

v2020.02 - Added individual image anchor tags

v2020.03 - A couple of code types on jQuery parameters.

v2023.01 - Added large_cols, medium_cols and small_cols parameters for greater control
	 - Added support for Woo products

v2023.02 - Added the pool_thumbs parameter

v2025.01 - Added the exclude_cat_ids parameter - it is a comma-seperated list of category IDs
	 - Renamed the category parameter to cat_ids parameter - it is a comma-seperated list of category IDs



== Examples ==


Display images from a folder:

[ingeni-live-wall source_path="/assets/images" max_thumbs=6 speed=4000 pool_thumbs=10 cat_ids="52,50"]


Display product images:

[ingeni-live-wall source_path="" cat_ids="52,50"]


Display product images, but exclude product category #50 and #52 products:

[ingeni-live-wall source_path="" speed=5000 exclude_cat_ids="52,50"]

