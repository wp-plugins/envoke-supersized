<?php
/**
 * Provides the ability to override the global slides on a per post or per page basis.
 *
 * @since 2.0.0
 *
 * @package Envoke_Supersized
 * @subpackage Per_Post_Override
 */

/**
 * Envoke Supersized Per Post Override Class
 *
 * @since 2.0.0
 */
class ENSS_Per_Post_Override {

	/**
	 * Instance of this class
	 *
	 * @var ENSS_Per_Post_Override
	 */
	protected static $_instance;

	/**
	 * The post types to enable overrides for.
	 *
	 * @since 2.0.0
	 * @access protected
	 * @var array $post_types The post types to enable overrides for.
	 */
	protected $post_types = array( 'post', 'page' );

	/**
	 * The HTML ID of the meta box.
	 *
	 * @since 2.0.0
	 * @access protected
	 * @var string $metabox_id The HTML ID of the meta box.
	 */
	protected $metabox_id = 'enss-per-post-override-metabox';

	/**
	 * The name of the meta key where override data is stored.
	 *
	 * @since 2.0.0
	 * @access protected
	 * @var string $meta_key The name of the meta key where override data is stored.
	 */
	protected $meta_key = 'enss-per-post-override';

	/**
	 * The overrides nonce action name.
	 *
	 * @since 2.0.0
	 * @access protected
	 * @var string $nonce_action The overrides nonce action name.
	 */
	protected $nonce_action = 'enss-save-per-post-override';

	/**
	 * The overrides nonce form field name.
	 *
	 * @since 2.0.0
	 * @access protected
	 * @var string $nonce_name The overrides nonce form field name
	 */
	protected $nonce_name = 'enss-per-post-override-nonce';

	/**
	 * Returns the instance of this class
	 *
	 * @return ENSS_Per_Post_Override
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new ENSS_Per_Post_Override();
			self::$_instance->_init();
		}
		return self::$_instance;
	}

	/**
	 * Called when class instantiated.
	 *
	 * @since 2.0.0
	 *
	 * @see ENSS_Singleton
	 * @see ENSS_Singleton::get_instance()
	 */
	public function _init() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );
		add_filter('is_protected_meta', array( $this, 'is_protected_meta' ), 10, 2 );
	}

	/**
	 * Enqueue scripts for admin.
	 *
	 * @since 2.0.0
	 * @global string $pagenow
	 */
	public function admin_enqueue_scripts() {
		wp_register_script( 'enss-per-post-override', ENSS_URL . 'assets/js/enss-per-post-override.min.js', array( 'jquery' ), ENSS_VERSION, true );

		global $pagenow;
		$post_types = $this->get_post_types();
		$pages = array( 'post-new.php', 'post.php' );
		if ( in_array( get_post_type(), $post_types ) && in_array( $pagenow, $pages ) ) {
			wp_enqueue_script( 'enss-per-post-override' );
		}
	}

	/**
	 * Add meta box for overriding images.
	 *
	 * @since 2.0.0
	 */
	public function add_meta_boxes() {
		$post_types = $this->get_post_types();

		foreach ( $post_types as $post_type ) {
			add_meta_box(
				$this->metabox_id,
				__( 'Override Supersized Background', 'enss' ),
				array( $this, 'render_meta_box' ),
				$post_type
			);
		}
	}

	/**
	 * Gets post types that should support overrides.
	 *
	 * @since 2.0.0
	 *
	 * @return array Array of post types that support overrides.
	 */
	public function get_post_types() {
		return apply_filters( 'enss-override-post-types', $this->post_types );
	}

	/**
	 * Render content in the meta box for overriding images.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Post $post Post Object
	 */
	public function render_meta_box( $post ) {
		$images = $this->get_images( $post->ID );
		$image = $images[0];
		?>
		<div class="enss-per-post-override">
			<div class="enss-per-post-image-type">
				<h4><?php _e( 'Image Source', 'enss' ); ?></php></h4>
				<p>
					<input type="radio" name="enss-image-input-type" id="enss-image-input-type-internal" value="internal" <?php checked( $image['type'], 'internal' ); ?>/>
					<label for="enss-image-input-type-internal"><?php _e( 'Media Library / Upload', 'enss' ); ?></label>
				</p>

				<p>
					<input type="radio" name="enss-image-input-type" id="enss-image-input-type-external" value="external" <?php checked( $image['type'], 'external' ); ?>/>
					<label for="enss-image-input-type-external"><?php _e( 'External Image', 'enss' ); ?></label>
				</p>
			</div>
			<div class="enss-title-caption-input">
				<p>
					<label for="enss-image-input-title"><?php _e( 'Image Title', 'enss' ); ?></label>
					<input type="text" class="widefat" name="enss-image-input-title" name="enss-image-input-title" value="<?php echo esc_attr( $image['title'] ); ?>"/>
				</p>
				<p>
					<label for="enss-image-input-caption"><?php _e( 'Image Caption', 'enss' ); ?></label>
					<textarea name="enss-image-input-caption" id="enss-image-input-caption" class="widefat"><?php echo esc_textarea( $image['caption'] ); ?></textarea>
				</p>
			</div>
			<div class="enss-per-post-internal-input <?php echo $image['type'] == 'internal' ? '' : 'hidden'; ?>">
				<div class="enss-image-preview">
					<?php echo wp_get_attachment_image( $image['id'], 'thumbnail' ); ?>
				</div>
				<div class="image-selector">
					<input type="hidden" id="enss-image-input-id" name="enss-image-input-id" value="<?php echo esc_attr( $image['id'] ); ?>" placeholder="<?php _e( 'Image ID', 'enss' ); ?>"/>
					<input type="button" class="button" data-enss-action="choose-image" data-enss-for="#enss-image-input-id" data-enss-preview=".enss-image-preview" value="<?php _e( 'Choose Image', 'enss' ); ?>" />
					<input type="button" class="button" data-enss-action="clear-image" data-enss-for="#enss-image-input-id" data-enss-preview=".enss-image-preview img" value="<?php _e( 'Clear', 'enss' ); ?>" />
				</div>
			</div>
			<div class="enss-per-post-external-input <?php echo $image['type'] == 'external' ? '' : 'hidden'; ?>">
				<p>
					<label for="enss-image-input-url"><?php _e( 'Image URL', 'enss' ); ?></label>
					<input type="text" class="widefat" name="enss-image-input-url" name="enss-image-input-url" value="<?php echo esc_url( $image['external_url'] ); ?>"/>
				</p>
				<p>
					<label for="enss-image-input-thumb"><?php _e( 'Thumbnail URL', 'enss' ); ?></label>
					<em><?php _e( 'If not thumbnail url is provided, the main image URL will be used.', 'enss' ); ?></em>
					<input type="text" class="widefat" name="enss-image-input-thumb" name="enss-image-input-thumb" value="<?php echo esc_url( $image['external_thumb'] ); ?>"/>
				</p>
			</div>
		</div>
		<?php
		wp_nonce_field( $this->nonce_action, $this->nonce_name );
	}

	/**
	 * Save the data about overrides for this post.
	 *
	 * @since 2.0.0
	 *
	 * @param integer $post_id The ID of the post.
	 */
	public function save_post( $post_id ) {
		if (
			( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			|| ! isset( $_POST[ $this->nonce_name ] )
			|| ! wp_verify_nonce( $_POST[ $this->nonce_name ], $this->nonce_action )
			|| ! isset( $_POST['enss-image-input-type'] )
		) {
			return;
		}

		switch( $_POST['enss-image-input-type'] ) {
			case 'internal':
				$image_id = isset( $_POST['enss-image-input-id'] ) ? $_POST['enss-image-input-id'] : false;
				if ( ! $image_id ) {
					delete_post_meta( $post_id, $this->meta_key );
					return;
				}
				$image_data = array(
					'type' => 'internal',
					'id' => intval( $image_id ),
				);
				break;
			case 'external':
				$image_url = isset( $_POST['enss-image-input-url'] ) ? $_POST['enss-image-input-url'] : false;
				$image_thumb = isset( $_POST['enss-image-input-thumb'] ) ? $_POST['enss-image-input-thumb'] : '';
				if ( ! $image_url ) {
					delete_post_meta( $post_id, $this->meta_key );
					return;
				}
				$image_data = array(
					'type' => 'external',
					'external_url' => esc_url_raw( $image_url ),
					'external_thumb' => esc_url_raw( $image_thumb ),
				);
				break;
		}

		$image_data['title'] = isset( $_POST['enss-image-input-title'] ) ? $_POST['enss-image-input-title'] : '';
		$image_data['caption'] = isset( $_POST['enss-image-input-caption'] ) ? $_POST['enss-image-input-caption'] : '';

		/*
		 * Storing inside an array, so that if in the future, there were, say, support for multiple images per post,
		 * its already stored in a way that would be compatible.
		 */
		$images = array($image_data);

		update_post_meta( $post_id, $this->meta_key, json_encode( $images ) );
	}

	/**
	 * See if the post has an image override setup.
	 *
	 * @since 2.0.0
	 *
	 * @param bool|integer $post_id Optional. The ID of the post to check. Defaults to ID of queried object.
	 *
	 * @return bool true if there is an image, false if not.
	 */
	public function have_images( $post_id = false ) {
		/*
		 * Added is_singular() check in 2.1.2 - cant really override in other cases anyways, and in cases where ONLY
		 * overrides were being used, and no slides, things would break on archive type pages
		 */
		if ( ! $post_id && is_singular() ) {
			$post_id = get_queried_object_id();
		}
		if ( ! $post_id ) {
			return false;
		}

		$have_images = false;
		$meta = get_post_meta( $post_id, $this->meta_key, true );
		if ( ! empty( $meta ) ) {
			$have_images = true;
		}

		return apply_filters( 'enss-per-post-have-images', $have_images, $post_id );
	}

	/**
	 * Get information about the image set for this post
	 *
	 * @since 2.0.0
	 *
	 * @param boolean|integer $post_id Optional. The ID of the post to get images for. Defaults to ID of queried object.
	 *
	 * @return WP_Error|array Information about the images for the post, or an error if something goes wrong.
	 */
	public function get_images( $post_id = false ) {
		if ( ! $post_id ) {
			$post_id = get_queried_object_id();
		}
		if ( ! $post_id ) {
			return new WP_Error( 'enss-error', 'Could not determine post id. Please provide a post id.' );
		}

		$images = get_post_meta( $post_id, $this->meta_key, true );

		return apply_filters( 'enss-per-post-get-images', $this->get_image_data( $images ), $post_id );
	}

	/**
	 * Returns all necessary data for the images.
	 *
	 * @since 2.0.0
	 *
	 * @param string $images JSON string that contains the image data for the post
	 *
	 * @return array Array of images and their associated data.
	 */
	protected function get_image_data( $images ) {
		$defaults = array(
			'type' => 'internal',
			'id' => '',
			'url' => '',
			'thumb' => '',
			'alt' => '',
			'title' => '',
			'caption' => '',
			'external_url' => '',
			'external_thumb' => '',
		);

		if ( empty( $images ) ) {
			return array($defaults);
		}

		/*
		 * $image[0] since we are storing the image inside another array, for potential future support of multiple
		 * images per post.
		 */
		$images = json_decode( $images, true );
		$images = $images[0];

		switch( $images['type'] ) {
			case 'internal':
				$images['url'] = wp_get_attachment_url( $images['id'] );
				$images['alt'] = get_post_meta( $images['id'], '_wp_attachment_image_alt', true );
				$thumb = wp_get_attachment_image_src( $images['id'], 'thumbnail' );
				$images['thumb'] = $thumb[0];
				break;
			case 'external':
				$images['url'] = $images['external_url'];
				if ( isset( $images['external_url'] ) && ! empty( $images['external_url'] ) ) {
					$images['thumb'] = $images['external_thumb'];
				} else {
					$images['thumb'] = $images['external_url'];
				}
				break;
		}

		$merged = wp_parse_args( $images, $defaults );

		return array($merged);
	}

	/**
	 * Define what meta keys should be protected.
	 *
	 * @since 2.0.0
	 *
	 * @param boolean $protected Whether or not the key is currently set as protected.
	 * @param string $meta_key The meta key to check.
	 *
	 * @return bool True if the key is protected, or else returns $protected.
	 */
	public function is_protected_meta( $protected, $meta_key ) {
		$protected_metas = array();
		$protected_metas[] = $this->meta_key;

		return in_array( $meta_key, $protected_metas ) ? true : $protected;
	}
}

ENSS_Per_Post_Override::get_instance();