<?php
/**
 * Plugin Name: Envoke Supersized
 * Description: This plugin creates an easy to use interface for managing the Supersized jQuery Plugin on your site.
 * Author: Envoke Design
 * Author URI: http://envokedesign.com
 * Plugin URI: http://envokedesign.com/wordpress-plugin-development/supersized
 * Version: 1.0.3
 */



require_once plugin_dir_path(__FILE__) . 'class.envoke-supersized.php';
require_once plugin_dir_path(__FILE__) . 'class.envoke-supersized-admin-pages.php';

add_action('init', array('Envoke_Supersized', 'register_post_types'));

add_action('admin_menu', array('Envoke_Supersized','admin_menu'));

add_action('wp_enqueue_scripts', array('Envoke_Supersized', 'enqueue_scripts'));
add_action('wp_footer', array('Envoke_Supersized', 'localize_script' ));

add_action('restrict_manage_posts', array('Envoke_Supersized', 'taxonomy_filter_restrict_manage_posts'));
add_filter('parse_query', array('Envoke_Supersized', 'taxonomy_filter_post_type_request'));
