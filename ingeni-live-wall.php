<?php
/*
Plugin Name: Ingeni Live Wall
Version: 2023.02
Plugin URI: http://ingeni.net
Author: Bruce McKinnon - ingeni.net
Author URI: http://ingeni.net
Description: Animated live wall for Wordpress
*/

/*
Copyright (c) 2023 Ingeni Web Solutions
Released under the GPL license
http://www.gnu.org/licenses/gpl.txt

Disclaimer: 
	Use at your own risk. No warranty expressed or implied is provided.
	This program is free software; you can redistribute it and/or modify 
	it under the terms of the GNU General Public License as published by 
	the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 	See the GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


Requires : Wordpress 3.x or newer ,PHP 5 +

v2020.01 - Initial release
v2020.02 - Individual image anchor tags
v2020.03 - A couple of code typos on jQuery parameters
v2023.01 - Added large_cols, medium_cols and small_cols parameters for greater control
		 - Added support for Woo products
v2023.02 - Added the pool_thumbs parameter

*/

if (!function_exists("ingeni_live_wall_log")) {
	function ingeni_live_wall_log($msg) {
		$upload_dir = wp_upload_dir();
		$logFile = $upload_dir['basedir'] . '/' . 'ingeni_live_wall_log.txt';
		date_default_timezone_set('Australia/Sydney');

		// Now write out to the file
		$log_handle = fopen($logFile, "a");
		if ($log_handle !== false) {
			fwrite($log_handle, date("H:i:s").": ".$msg."\r\n");
			fclose($log_handle);
		}
	}
}

if (!function_exists("endsWith")) {
	function endsWith($haystack, $needle) {
			// search forward starting from end minus needle length characters
			return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
	}
}


add_shortcode( 'ingeni-live-wall','do_ingeni_livewall' );
function do_ingeni_livewall( $args ) {

	$params = shortcode_atts( array(
		'source_path' => '/images/live-wall/',
		'wrapper_class' => 'ingeni-live-wall-wrap',
		'shuffle' => 1,
		'start_path' => "",
		'speed' => 3000,
		'anim_type' => "fadeInOut",
		'max_thumbs' => 6,
		'pool_thumbs' => 10,
		'small_cols' => 1,
		'medium_cols' => 2,
		'large_cols' => 3,
		'category' => '',
	), $args );


	$titles = array();
	$links = array();

//ingeni_slick_log('params:'.print_r($params,true));

	
	if ($params['start_path'] != '') {
		chdir($params['start_path']);
	}

	if ($params['speed'] < 1000) {
		$params['speed'] = 1000;
	}
	if ($params['speed'] > 60000) {
		$params['speed'] = 60000;
	}
	$interval = $params['speed'];
	$anim_speed = intval($interval / 5);


//ingeni_slick_log('curr path:'.getcwd() .'|'.$params['source_path']);

	if ( $params['source_path'] != '' ) {
		try {
			$photos = array();
			$root_dir = getcwd();
			if (stripos($root_dir, '/wp-admin') !== FALSE ) {
				$root_dir = str_ireplace('/wp-admin','',$root_dir);
			}
			if ( file_exists($root_dir . $params['source_path']) ) {
				$photos = scandir( $root_dir . $params['source_path']);
			}
			if (!$photos) {
				throw new Exception('Error while scanning: '.$root_dir . $params['source_path']);
			}
		} catch (Exception $ex) {
			if ( function_exists("ingeni_slick_log") ) {
				ingeni_slick_log('Scanning folder '.$params['source_path'].' : '.$ex->message);
			}
		}
		$home_path = get_bloginfo('url') . $params['source_path'];

		$idx = 0;
		if ($params['shuffle'] > 0) {
			shuffle($photos);
		}
//ingeni_slick_log('photos to show: '.print_r($photos,true));

		$sync1 = '<div id="livewall" class="ri-grid ri-grid-size-2"><img class="ri-loading-image" src="'.plugins_url('loading.gif', __FILE__).'"/><ul>';
		foreach ($photos as $photo) {
			if ( (strpos(strtolower($photo),'.jpg') !== false) || (strpos(strtolower($photo),'.png') !== false)  || (strpos(strtolower($photo),'.mp4') !== false) ) {		


				//$tag = "0000".$idx;
				//$tag = "img_".substr($tag,strlen($tag)-4,4);
				$tag = str_pad($idx, 4, '0', STR_PAD_LEFT);
//ingeni_live_wall_log('photo to show: '.$tag.' = '.$photo);
				$sync1 .= '<li><a href="#'.$tag.'"><img id="'.$tag.'" src="'. $home_path . $photo .'"></img></a></li>';

				++$idx;
				if ( ($idx > $params['pool_thumbs']) && ($params['max_thumbs'] > 0) ) {
					break;
				}
			}
		}
		$sync1 .= '</ul></div>';
	} else {
		// Go and get the products
		$idx = 0;

		if ( ($params['max_thumbs']+2) > $params['pool_thumbs'] ) {
			$params['pool_thumbs'] = $params['max_thumbs']+2;
		}
		$args = array(
			'post_type' => 'product',
			'post_status' => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page' => $params['pool_thumbs'],
			'orderby' => 'rand',
		);
	
		if ( $params['category'] !== '' ) {
			array_push( $args, array ( 'category' => $params['category'] ) );
		}


		$products = wc_get_products( $args );

		if ( $products ) {
			$sync1 = '<div id="livewall" class="ri-grid ri-grid-size-2"><img class="ri-loading-image" src="'.plugins_url('loading.gif', __FILE__).'"/><ul>';

			foreach($products as $product) {
	
				//$retHtml .= print_r($product,true);
				$prod_image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'large' );
				$prod_url = get_permalink( $product->get_id() );

				$tag = str_pad($idx, 4, '0', STR_PAD_LEFT);
//ingeni_live_wall_log('photo to show: '.$tag.' = '.$photo);
				$sync1 .= '<li><a href="'.$prod_url.'"><img id="'.$tag.'" src="'. $prod_image_url[0] .'"></img></a></li>';

				++$idx;
				if ( ($idx > $params['max_thumbs']) && ($params['max_thumbs'] > 0) ) {
					break;
				}
			}
			$sync1 .= '</ul></div>';
		}
	}
	

	$large_rows = intval($params['max_thumbs']) / intval($params['large_cols']);
	$medium_rows = intval($params['max_thumbs']) / intval($params['medium_cols']);
	$small_rows = intval($params['max_thumbs']) / intval($params['small_cols']);

	$js = '<script type="text/javascript">';
	$js .= 'jQuery(document).ready(function() {';

		$js .= 'jQuery( "#livewall" ).delay(3000).gridrotator( {
			rows		: '.$large_rows.',
			columns		: '.$params['large_cols'].',
			animType	: "'.$params['anim_type'].'",
			animSpeed	: '.$anim_speed.',
			interval	: '.$interval.',
			step		: 4,
			preventClick : false,
			w1024			: {
				rows	: '.$large_rows.',
				columns	: '.$params['large_cols'].'
			},

			w768			: {
				rows	: '.$medium_rows.',
				columns	: '.$params['medium_cols'].'
			},

			w480			: {
				rows	: '.$medium_rows.',
				columns	: '.$params['medium_cols'].'
			},

			w320			: {
				rows	: '.$small_rows.',
				columns	: '.$params['small_cols'].'
			},

			w240			: {
				rows	: '.$small_rows.',
				columns	: '.$params['small_cols'].'
			},
		} );';
	
		$js .= '});';
		$js .= '</script>';

	return '<div class="'.$params['wrapper_class'].'">'.$sync1.'</div>'.$js;
}




function ingeni_load_livewall() {

	// Rotator
	wp_register_script( 'rotator_js', plugins_url('jquery.gridrotator.js', __FILE__), false, '1.0', true );
	wp_enqueue_script( 'rotator_js' );

	//wp_register_script( 'modernizer_js', plugins_url('modernizr.custom.26633.js', __FILE__), false, '1.0', true );
	//wp_enqueue_script( 'modernizer_js' );


	//
	// Plugin CSS
	//
	wp_enqueue_style( 'live-wall-css', plugins_url('ingeni-live-wall.css', __FILE__) );
}
add_action( 'wp_enqueue_scripts', 'ingeni_load_livewall' );


function ingeni_update_live_wall() {
	require 'plugin-update-checker/plugin-update-checker.php';
	$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
		'https://github.com/BruceMcKinnon/ingeni-live-wall',
		__FILE__,
		'ingeni-live-wall'
	);
	
	//Optional: If you're using a private repository, specify the access token like this:
	//$myUpdateChecker->setAuthentication('your-token-here');
	
	//Optional: Set the branch that contains the stable release.
	//$myUpdateChecker->setBranch('stable-branch-name');

}
add_action( 'init', 'ingeni_update_live_wall' );


// Plugin activation/deactivation hooks
function ingeni_live_wall_activation() {
	flush_rewrite_rules( false );
}
register_activation_hook(__FILE__, 'ingeni_live_wall_activation');

function ingeni_live_wall_deactivation() {
  flush_rewrite_rules( false );
}
register_deactivation_hook( __FILE__, 'ingeni_live_wall_deactivation' );

?>