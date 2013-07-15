<?php
/**
 * Plugin Name: Envoke Supersized
 * Description: This plugin creates an easy to use interface for managing the Supersized jQuery Plugin on your site.
 * Author: Envoke Design
 * Author URI: http://envokedesign.com
 * Plugin URI: http://envokedesign.com/wordpress-plugin-development/supersized
 * Version: 1.3.1
 */
//Change version in the class.envoke-supersized file to (class var)

require_once plugin_dir_path(__FILE__) . 'class.envoke-supersized.php';
require_once plugin_dir_path(__FILE__) . 'class.envoke-supersized-admin-pages.php';

add_action('init', array('Envoke_Supersized', 'register_post_types'));

add_action('admin_menu', array('Envoke_Supersized','admin_menu'));
add_action('admin_enqueue_scripts', array('Envoke_Supersized_Admin_Pages','admin_enqueue_scripts') );
add_action('save_post', array('Envoke_Supersized_Admin_Pages','save_post') );

add_action('wp_enqueue_scripts', array('Envoke_Supersized', 'enqueue_scripts'));
add_action('wp_footer', array('Envoke_Supersized', 'localize_script' ));

add_action('restrict_manage_posts', array('Envoke_Supersized', 'taxonomy_filter_restrict_manage_posts'));
add_filter('parse_query', array('Envoke_Supersized', 'taxonomy_filter_post_type_request'));

add_action( 'add_meta_boxes', array('Envoke_Supersized_Admin_Pages','per_page_metaboxes') );

if ( ! function_exists('dout') ) {
	function dout($content, $die = false) {
		echo '<pre>'.print_r($content, true).'</pre>';

		if ( $die ) {
			die();
		}
	}
}


add_filter('is_protected_meta', array('Envoke_Supersized_Admin_Pages','is_protected_meta'), 10, 2);