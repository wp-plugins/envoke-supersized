<?php
/**
 * Implements the slide post type and related functionality
 *
 * This file contains a class which is responsible for registering the post type used for the slides, registering a
 * slide category taxonomy, and implementing some functions to retrieve the data we will need to assemble the
 * slideshow on the front end.
 *
 * @since 2.0.0
 *
 * @package Envoke_Supersized
 * @subpackage Slides
 */

/**
 * Envoke Supersized Slide Class.
 *
 * @since 2.0.0
 */
class ENSS_Slide extends ENSS_Singleton {

	/**
	 * The post type name.
	 *
	 * @since 2.0.0
	 * @access protected
	 * @var string $_post_type The post type name, used for slides.
	 */
	protected $_post_type = 'slides';

	/**
	 * Slide Category Taxonomy.
	 *
	 * @since 2.0.0
	 * @access protected
	 * @var string $_taxonomy The slide category taxonomy.
	 */
	protected $_taxonomy = 'slide-category';

	/**
	 * Called when class instantiated.
	 *
	 * @since 2.0.0
	 *
	 * @see ENSS_Singleton
	 * @see ENSS_Singleton::get_instance()
	 */
	function _init() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );
	}

	/**
	 * Gets the post type.
	 *
	 * @since 2.0.0
	 *
	 * @return string The post type.
	 */
	public function get_post_type() {
		return apply_filters( 'enss-slide-post-type', $this->_post_type );
	}

	/**
	 * Register slide post type.
	 *
	 * @since 2.0.0
	 */
	function register_post_type() {
		register_post_type(
			$this->_post_type,
			array(
				'labels' => array(
					'name' => __( 'Slides', 'enss' ),
					'singular_name' => __( 'Slides', 'enss' ),
					'add_new' => __( 'Add New'),
					'add_new_item' => __( 'Add New Slide', 'enss' ),
					'edit_item' => __( 'Edit Slide', 'enss' ),
					'new_item' => __( 'New Slide', 'enss' ),
					'all_items' => __( 'All Slides', 'enss' ),
					'view_item' => __( 'View Slide', 'enss' ),
					'search_items' => __( 'Search Slides', 'enss' ),
					'not_found' =>  __( 'No slides found', 'enss' ),
					'not_found_in_trash' => __( 'No slides found in Trash', 'enss' ),
					'parent_item_colon' => '',
					'menu_name' => __( 'Supersized Slides', 'enss' )
				),
				'public' => false,
				'publicly_queryable' => false,
				'show_ui' => true,
				'show_in_menu' => true,
				'query_var' => true,
				'rewrite' => array( 'slug' => 'slide' ),
				'capability_type' => 'post',
				'has_archive' => false,
				'hierarchical' => false,
				'menu_position' => null,
				'supports' => array( 'title', 'editor', 'thumbnail' )
			)
		);
	}

	/**
	 * Register category taxonomy.
	 *
	 * @since 2.0.0
	 */
	function register_taxonomies() {
		register_taxonomy(
			$this->_taxonomy,
			$this->_post_type,
			array(
				'hierarchical' => true,
				'labels' => array(
					'name' => _x( 'Categories', 'taxonomy general name', 'enss' ),
					'singular_name' => _x( 'Category', 'taxonomy singular name', 'enss' ),
					'search_items' =>  __( 'Search Categories', 'enss' ),
					'all_items' => __( 'All Categories', 'enss' ),
					'parent_item' => __( 'Parent Category', 'enss' ),
					'parent_item_colon' => __( 'Parent Category:', 'enss' ),
					'edit_item' => __( 'Edit Category', 'enss' ),
					'update_item' => __( 'Update Category', 'enss' ),
					'add_new_item' => __( 'Add New Category', 'enss' ),
					'new_item_name' => __( 'New Category Name', 'enss' ),
					'menu_name' => __( 'Categories', 'enss' ),
				),
				'show_ui' => true,
				'query_var' => true,
				'rewrite' => array( 'slug' => 'slide-category' ),
			)
		);
	}

	/**
	 * Checks if slides exist.
	 *
	 * Checks if there are any posts in the slide post type that have status publish.
	 *
	 * @since 2.0.0
	 *
	 * @return bool true if images, false if not.
	 */
	function have_images() {
		$counts = wp_count_posts( $this->_post_type );

		return $counts->publish > 0 ? true : false;
	}

	/**
	 * Get image details.
	 *
	 * Returns relevant data about the images needed to create the slideshow on the front end, such as URLs, Caption,
	 * and Title.
	 *
	 * @since 2.0.0
	 *
	 * @return array The image details for each image to use in the slideshow.
	 */
	function get_images() {
		$images = array();
		$args = array(
			'post_type' => $this->_post_type,
		);
		$query = new WP_Query( $args );
		if ( $query->have_posts() ) {
			while( $query->have_posts() ) {
				$query->the_post();
				$post = $query->post;
				$url = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
				$thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'thumbnail' );
				$images[] = array(
					'url' => $url[0],
					'title' => get_the_title(),
					'caption' => get_the_content(),
					'thumb' => $thumb[0],
				);
			}
		}
		wp_reset_postdata();

		return $images;
	}
}

ENSS_Slide::get_instance();
