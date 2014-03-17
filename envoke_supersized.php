<?php
/**
 * Plugin Name: Envoke Supersized
 * Plugin URI:  https://bitbucket.org/envokedesign/envoke-supersized
 * Description: This plugin creates an easy to use interface for managing the Supersized jQuery Plugin on your site.
 * Version:     2.1.4
 * Author:      Chris Marslender, Dillon McCallum
 * License:     GPLv2+
 * Text Domain: enss
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2013 Chris Marslender (email : chrismarslender@gmail.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// Useful global constants
define( 'ENSS_VERSION', '2.1.4' );
define( 'ENSS_URL',     plugin_dir_url( __FILE__ ) );
define( 'ENSS_PATH',    dirname( __FILE__ ) . '/' );

// Load required files
require ENSS_PATH . 'includes/class-enss-singleton.php';
require ENSS_PATH . 'includes/class-enss-slide.php';
require ENSS_PATH . 'includes/class-enss-settings.php';
require ENSS_PATH . 'includes/class-enss-per-post-override.php';
require ENSS_PATH . 'includes/class-enss-front-end.php';
require ENSS_PATH . 'includes/class-enss-back-compat.php';

/**
 * Default initialization for the plugin:
 * - Registers the default textdomain.
 */
function enss_init() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'enss' );
	load_textdomain( 'enss', WP_LANG_DIR . '/enss/enss-' . $locale . '.mo' );
	load_plugin_textdomain( 'enss', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

/**
 * Activate the plugin
 */
function enss_activate() {
	// First load the init scripts in case any rewrite functionality is being loaded
	enss_init();

	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'enss_activate' );

// Wireup actions
add_action( 'init', 'enss_init' );
