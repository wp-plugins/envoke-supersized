<?php
/**
 * Implements backwards compatibility to ensure data from old plugin versions still works.
 *
 * @since 2.0.0
 *
 * @package Envoke_Supersized
 * @subpackage Backwards_Compatibility
 */

/**
 * Envoke Supersized Backwards Compatibility Class
 *
 * @since 2.0.0
 */
class ENSS_Back_Compat extends ENSS_Singleton {

	/**
	 * Meta key that stored image url in 1.x versions.
	 *
	 * @since 2.0.0
	 * @access protected
	 * @var string $legacy_image_meta_key Meta key that stored image url in 1.x versions.
	 */
	protected $legacy_image_meta_key = 'envoke_ss_page_image_url';

	/**
	 * The option name that stored settings in 1.x versions.
	 *
	 * @since 2.0.0
	 * @access protected
	 * @var string $legacy_settings_key The option name that stored settings in 1.x versions.
	 */
	protected $legacy_settings_key = 'envoke-supersized-settings';

	/**
	 * Called when class instantiated.
	 *
	 * @since 2.0.0
	 *
	 * @see ENSS_Singleton
	 * @see ENSS_Singleton::get_instance()
	 */
	public function _init() {
		add_action( 'init', array( $this, 'check_if_enabled' ), 15 ); // Needs to come after the normal settings are setup
	}

	public function check_if_enabled() {
		$settings = ENSS_Settings::get_instance();
		if ( $settings->get_setting( 'back_compat' ) ) {
			add_filter( 'enss-per-post-have-images', array( $this, 'per_post_have_images' ), 10, 2 );
			add_filter( 'enss-per-post-get-images', array( $this, 'per_post_get_images' ), 10, 2 );
		}
	}

	/**
	 * Check if there is an override from an older plugin version.
	 *
	 * @since 2.0.0
	 *
	 * @param boolean $have_images Whether images have already been found for the post.
	 * @param integer $post_id The ID of the post to check.
	 *
	 * @return boolean True if there is an override from an old version, or else $have_images.
	 */
	public function per_post_have_images( $have_images, $post_id ) {
		if ( ! $have_images ) {
			$meta = get_post_meta( $post_id, $this->legacy_image_meta_key, true );
			if ( ! empty( $meta ) ) {
				$have_images = true;
			}
		}

		return $have_images;
	}

	/**
	 * Gets the override data from an older plugin version.
	 *
	 * @since 2.0.0
	 *
	 * @param array $images The current image data.
	 * @param integer $post_id The ID of the post to check.
	 *
	 * @return array Returns the image data.
	 */
	public function per_post_get_images( $images, $post_id ) {
		if ( !isset( $images[0] ) || empty($images[0]['url'] ) ) {
			$legacy_image = get_post_meta( $post_id, $this->legacy_image_meta_key, true );
			if ( ! empty( $legacy_image ) ) {
				$images[0]['url'] = $legacy_image;
				$images[0]['thumb'] = $legacy_image;
			}
		}

		return $images;
	}

	/**
	 * Loads settings from an older plugin version.
	 *
	 * @since 2.0.0
	 *
	 * @param array $settings Current settings
	 *
	 * @return array $settings if not empty, or else returns settings from old plugin version.
	 */
	public function load_settings( $settings ) {
		if ( ! empty( $settings ) ) {
			return $settings;
		}

		return get_option( $this->legacy_settings_key );
	}

}

ENSS_Back_Compat::get_instance();