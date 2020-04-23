<?php
/*
Plugin Name: Ingeni Live Wall
Version: 2020.03
Plugin URI: http://ingeni.net
Author: Bruce McKinnon - ingeni.net
Author URI: http://ingeni.net
Description: Animated live wall for Wordpress
*/

/*
Copyright (c) 2020 Ingeni Web Solutions
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
	), $args );


	$titles = array();
	$links = array();

//ingeni_slick_log('params:'.print_r($params,true));

	try {
		if ($params['start_path'] != '') {
			chdir($params['start_path']);
		}

		if ($params['speed'] < 1000) {
			$params['speed'] = 1000;
		}
		if ($params['speed'] > 10000) {
			$params['speed'] = 10000;
		}
		$interval = $params['speed'];
		$anim_speed = intval($interval / 3);


//ingeni_slick_log('curr path:'.getcwd() .'|'.$params['source_path']);
		$root_dir = getcwd();
		if (stripos($root_dir, '/wp-admin') !== FALSE ) {
			$root_dir = str_ireplace('/wp-admin','',$root_dir);
		}
		$photos = scandir( $root_dir . $params['source_path']);
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
//ingeni_slick_log('photo to show: '.$home_path . $photo);

			$tag = "0000".$idx;
			$tag = "img_".substr($tag,strlen($tag)-4,4);
			$sync1 .= '<li><a href="#'.$tag.'"><img id="'.$tag.'" src="'. $home_path . $photo .'"></img></a></li>';

			++$idx;
			if ( ($idx > $params['max_thumbs']) && ($params['max_thumbs'] > 0) ) {
				break;
			}
		}
	}
	$sync1 .= '</ul></div>';




	

	$js = '<script type="text/javascript">';
	$js .= 'jQuery(function() {';
	
		$js .= 'jQuery( "#livewall" ).gridrotator( {
			rows		: 2,
			columns		: 6,
			animType	: "'.$params['anim_type'].'",
			animSpeed	: '.$anim_speed.',
			interval	: '.$interval.',
			step		: 4,
			w1024			: {
				rows	: 2,
				columns	: 5
			},

			w768			: {
				rows	: 2,
				columns	: 4
			},

			w480			: {
				rows	: 2,
				columns	: 2
			},

			w320			: {
				rows	: 2,
				columns	: 1
			},

			w240			: {
				rows	: 2,
				columns	: 1
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