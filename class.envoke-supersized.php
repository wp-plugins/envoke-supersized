<?php
/**
 * Created by christophermarslender
 */
class Envoke_Supersized
{

	protected static $settings = array(
		'slideshow' => 'Slideshow',
		'autoplay' => 'Auto Play',
		'start_slide' => 'Start Slide (number)',
		'random' => 'Random Start Slide',
		'slide_interval' => 'Slide Interval (ms)',
		'transition' => 'Transition Effect',
		'transition_speed' => 'Transition Speed (ms)',
		'progress_bar' => 'Progress Bar',
	);

	protected static $defaults = array(
		'slideshow' => '1',
		'autoplay' => '1',
		'start_slide' => '1',
		'random' => '0',
		'slide_interval' => '3000',
		'transition' => '1',
		'transition_speed' => '1000',
		'progress_bar' => '1',
	);



	protected static $got_settings = false; //internal flag
	protected static $slideshow = null;
	protected static $autoplay = null;
	protected static $start_slide = null;
	protected static $random = null;
	protected static $slide_interval = null;
	protected static $transition = null;
	protected static $transition_speed = null;
	protected static $progress_bar = null;


	public static function admin_menu() {
		add_submenu_page('edit.php?post_type=slides',__('Settings'),__('Settings'),'edit_posts','envoke-supersized-settings',array('Envoke_Supersized_Admin_Pages','settings'));
	}

	public static function enqueue_scripts() {
		wp_enqueue_style('supersized', plugin_dir_url(__FILE__) . 'supersized_assets/css/supersized.css');
		wp_enqueue_style('supersized-theme', plugin_dir_url(__FILE__) . 'supersized_assets/theme/supersized.shutter.css');
		wp_enqueue_script('supersized', plugin_dir_url(__FILE__) . 'supersized_assets/js/supersized.3.2.7.min.js', array('jquery') );
		wp_enqueue_script('supersized-theme', plugin_dir_url(__FILE__) . 'supersized_assets/theme/supersized.shutter.min.js', array('supersized'));
		wp_enqueue_script('cmi_supersized', plugin_dir_url(__FILE__) . 'supersized_assets/js/supersized.js', array('supersized') );
	}

	public static function load_settings($force = false) {
		if ( self::$got_settings && ! $force ) {
			return;
		}
		$settings = get_option('envoke-supersized-settings');
		if ( ! $settings ) {
			$settings = self::$defaults;
			update_option('envoke-supersized-settings',$settings);
		}

		foreach ( self::$settings as $slug => $label ) {
			self::${$slug} = is_null($settings[$slug]) ? self::$defaults[$slug] : $settings[$slug];
		}

		self::$got_settings = true;
	}

	public static function localize_script() {
		self::load_settings();

		$images = array();
		$args = array(
			'post_type' => 'slides',
		);
		$query = new WP_Query($args);
		global $post;
		while ( $query->have_posts() ):
			$query->the_post();
			$image = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );
			error_log(print_r($post,true),0);
			$formatted = '<h4 class="slide-title">'.$post->post_title.'</h4>';
			$formatted .= '<div class="slide-content">'.$post->post_content.'</div>';
			$images[] = array(
				'image' =>  $image[0],
				'title' => $post->post_title,
				//'content' => $post->post_content
				'content' => $formatted
			);
		endwhile;

		?>
		<script type="text/javascript">
			jQuery(function($){

				$.supersized({

					// Functionality
					<?php
					foreach ( self::$settings as $slug => $label ) {
						echo $slug . ':' . self::${$slug} . ',';
					}
                    ?>

					stop_loop				:	0,			// Pauses slideshow on last slide
					new_window				:	1,			// Image links open in new window/tab
					pause_hover             :   0,			// Pause slideshow on hover
					keyboard_nav            :   0,			// Keyboard navigation on/off
					performance				:	1,			// 0-Normal, 1-Hybrid speed/quality, 2-Optimizes image quality, 3-Optimizes transition speed // (Only works for Firefox/IE, not Webkit)
					image_protect			:	1,			// Disables image dragging and right click with Javascript

					// Size & Position
					min_width		        :   0,			// Min width allowed (in pixels)
					min_height		        :   0,			// Min height allowed (in pixels)
					vertical_center         :   1,			// Vertically center background
					horizontal_center       :   1,			// Horizontally center background
					fit_always				:	0,			// Image will never exceed browser width or height (Ignores min. dimensions)
					fit_portrait         	:   1,			// Portrait images will not exceed browser height
					fit_landscape			:   0,			// Landscape images will not exceed browser width

					// Components
					slide_links				:	'blank',	// Individual links for each slide (Options: false, 'num', 'name', 'blank')
					thumb_links				:	0,			// Individual thumb links for each slide
					thumbnail_navigation    :   0,			// Thumbnail navigation
					slides 					:  	[
						<?php
					$count = 0;
					foreach( $images as $image ) {
						$count++;
						if ( $count > 1 ) {
						echo ',';
						}
					?>
						{
							image:'<?php echo $image['image']; ?>',
							title:'<?php echo $image['content']; ?>',
							caption:'<?php echo $image['content']; ?>',
							url: '<?php echo $image['image']; ?>'
						}
						<?php
						} ?>
					],

					// Theme Options
					mouse_scrub				:	0

				});
			});
		</script>
		<div id="supersized_overlay"></div>

		<div class="envoke-supersized-container">
			<!--Thumbnail Navigation-->
			<div id="prevthumb"></div>
			<div id="nextthumb"></div>

			<!--Arrow Navigation-->
			<a id="prevslide" class="load-item"></a>
			<a id="nextslide" class="load-item"></a>

			<div id="thumb-tray" class="load-item">
				<div id="thumb-back"></div>
				<div id="thumb-forward"></div>
			</div>

			<!--Time Bar-->
			<div id="progress-back" class="load-item">
				<div id="progress-bar"></div>
			</div>

			<!--Slide captions displayed here-->
			<div id="slidecaption"></div>

			<!--Control Bar-->
			<div id="controls-wrapper" class="load-item">
				<div id="controls">

					<a id="play-button"><img id="pauseplay" src="<?php echo plugin_dir_url(__FILE__); ?>img/pause.png"/></a>

					<!--Slide counter-->
					<div id="slidecounter">
						<span class="slidenumber"></span> / <span class="totalslides"></span>
					</div>

					<!--Thumb Tray button-->
					<a id="tray-button"><img id="tray-arrow" src="<?php echo plugin_dir_url(__FILE__); ?>img/button-tray-up.png"/></a>

					<!--Navigation-->
					<ul id="slide-list"></ul>

				</div>
			</div>
		</div>

	<?php
	}

	public static function register_post_types() {
		$labels = array(
			'name' => __('Slides'),
			'singular_name' => __('Slides'),
			'add_new' => __('Add New'),
			'add_new_item' => __('Add New Slide'),
			'edit_item' => __('Edit Slide'),
			'new_item' => __('New Slide'),
			'all_items' => __('All Slides'),
			'view_item' => __('View Slide'),
			'search_items' => __('Search Slides'),
			'not_found' =>  __('No slides found'),
			'not_found_in_trash' => __('No slides found in Trash'),
			'parent_item_colon' => '',
			'menu_name' => __('Supersized Slides')
		);

		$args = array(
			'labels' => $labels,
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
			'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
		);

		register_post_type( 'slides', $args );

		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name' => _x( 'Categories', 'taxonomy general name' ),
			'singular_name' => _x( 'Category', 'taxonomy singular name' ),
			'search_items' =>  __( 'Search Categories' ),
			'all_items' => __( 'All Categories' ),
			'parent_item' => __( 'Parent Category' ),
			'parent_item_colon' => __( 'Parent Category:' ),
			'edit_item' => __( 'Edit Category' ),
			'update_item' => __( 'Update Category' ),
			'add_new_item' => __( 'Add New Category' ),
			'new_item_name' => __( 'New Category Name' ),
			'menu_name' => __( 'Categories' ),
		);

		register_taxonomy('slide-category',array('slides'), array(
			'hierarchical' => true,
			'labels' => $labels,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'slide-category' ),
		));

	}

	public static function taxonomy_filter_post_type_request( $query ) {
		global $pagenow, $typenow;

		if ( 'edit.php' == $pagenow ) {
			$filters = get_object_taxonomies( $typenow );
			foreach ( $filters as $tax_slug ) {
				$var = &$query->query_vars[$tax_slug];
				if ( isset( $var ) ) {
					$term = get_term_by( 'id', $var, $tax_slug );
					$var = $term->slug;
				}
			}
		}
	}

	public static function taxonomy_filter_restrict_manage_posts() {
		global $typenow;

		$post_types = get_post_types( array( '_builtin' => false ) );

		if ( in_array( $typenow, $post_types ) ) {
			$filters = get_object_taxonomies( $typenow );

			foreach( $filters as $tax_slug ) {
				$tax_obj = get_taxonomy( $tax_slug );
				wp_dropdown_categories( array(
					'show_option_all' => sprintf( __( 'Show All %s', 'cyracom' ), $tax_obj->label ),
					'taxonomy' => $tax_slug,
					'name' => $tax_obj->name,
					'orderby' => 'name',
					'selected' => $_GET[$tax_slug],
					'hierarchical' => $tax_obj->hierarchical,
					'show_count' => false,
					'hide_empty' => true
				) );
			}
		}

	}
}