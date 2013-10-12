<?php
/**
 * Implements all functionality to make Supersized work on the front end of the site.
 *
 * This file contains the front end class, which is responsible for loading necessary scripts, and outputting the data
 * about the images that should be displayed in the slideshow.
 *
 * @since 2.0.0
 *
 * @package Envoke_Supersized
 * @subpackage Front_End
 */

/**
 * Envoke Supersized Front End Class
 *
 * @since 2.0.0
 */
class ENSS_Front_End extends ENSS_Singleton {

	/**
	 * Called when class instantiated.
	 *
	 * @since 2.0.0
	 *
	 * @see ENSS_Singleton
	 * @see ENSS_Singleton::get_instance()
	 */
	public function _init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_footer', array( $this, 'output' ), 20 );
	}

	/**
	 * Enqueue scripts for front end.
	 *
	 * @since 2.0.0
	 */
	public function enqueue_scripts() {
		wp_register_style( 'enss', ENSS_URL . 'assets/css/envoke_supersized.min.css' );
		wp_register_script( 'enss-front-end', ENSS_URL . 'assets/js/enss-front-end.min.js', array('jquery'), false, true );

		$slide_class = ENSS_Slide::get_instance();
		$override_class = ENSS_Per_Post_Override::get_instance();

		if ( $slide_class->have_images() || $override_class->have_images() ) {
			wp_enqueue_style( 'enss' );
			wp_enqueue_script( 'enss-front-end' );

			$data = array(
				'image_url' => ENSS_URL . 'images/supersized/'
			);
			wp_localize_script( 'enss-front-end', 'ENSS', $data );
		}
	}

	/**
	 * Output the required HTML and javascript.
	 *
	 * @since 2.0.0
	 */
	public function output() {
		$slide_class = ENSS_Slide::get_instance();
		$override_class = ENSS_Per_Post_Override::get_instance();

		if ( $slide_class->have_images() || $override_class->have_images() ) {
			$this->output_javascript();
			$this->output_html();
		}
	}

	/**
	 * Output HTML for Supersized.
	 *
	 * @since 2.0.0
	 */
	public function output_html() {
		$settings = ENSS_Settings::get_instance();
		?>
		<div id="supersized_overlay" class="enss-overlay enss-overlay-<?php echo $settings->get_setting('overlay'); ?>"></div>

		<div class="enss-container">
			<?php //thumbnail navigation currently not used at all ?>
			<!--Thumbnail Navigation-->
			<div id="prevthumb"></div>
			<div id="nextthumb"></div>

			<?php
			if ( $settings->get_setting('arrow_navigation') ) {
				?>
				<!--Arrow Navigation-->
				<a id="prevslide" class="load-item"></a>
				<a id="nextslide" class="load-item"></a>
				<?php
			}
			?>

			<?php
			if ( $settings->get_setting('thumbnails') ) {
				?>
				<div id="thumb-tray" class="load-item">
					<div id="thumb-back"></div>
					<div id="thumb-forward"></div>
				</div>
				<?php
			}
			?>

			<?php
			if ( $settings->get_setting( 'progress_bar' ) ) {
				?>
				<!--Time Bar-->
				<div id="progress-back" class="load-item">
					<div id="progress-bar"></div>
				</div>
				<?php
			}
			?>

			<?php
			if ( $settings->get_setting('control_bar') ) {
				?>
				<!--Control Bar-->
				<div id="controls-wrapper" class="load-item">
					<div id="controls">

						<?php
						if ( $settings->get_setting('play_pause') ) {
							?>
							<a id="play-button"><img id="pauseplay" src="<?php echo ENSS_URL; ?>images/supersized/pause.png"/></a>
						<?php
						}
						?>

						<?php
						if ( $settings->get_setting('slide_counter') ) {
							?>
							<!--Slide counter-->
							<div id="slidecounter">
								<span class="slidenumber"></span> / <span class="totalslides"></span>
							</div>
						<?php
						}
						?>

						<!--Slide captions displayed here-->
						<div id="slidecaption">
							<?php
							if ( $settings->get_setting( 'slide_title' ) ) {
								?><span id="slidetitle"></span><?php
							}
							?>
							<?php
							if ( $settings->get_setting( 'slide_caption' ) ) {
								?><span id="slidecaptiontext"><span><?php
							}
							?>
						</div>

						<?php
						if ( $settings->get_setting( 'thumbnails' ) ) {
							?>
							<!--Thumb Tray button-->
							<a id="tray-button"><img id="tray-arrow" src="<?php echo ENSS_URL; ?>images/supersized/button-tray-up.png"/></a>

							<!--Navigation-->
							<ul id="slide-list"></ul>
						<?php
						}
						?>

					</div>
				</div>
				<?php
			}
			?>
		</div>
	<?php
	}

	/**
	 * Output javascript for Supersized.
	 *
	 * @since 2.0.0
	 */
	public function output_javascript() {
		$override_class = ENSS_Per_Post_Override::get_instance();
		if ( $override_class->have_images() ) {
			$images = $override_class->get_images();
		} else {
			$images = ENSS_Slide::get_instance()->get_images();
		}
		$settings = ENSS_Settings::get_instance()->get_settings_group( 'supersized' );
		?>
		<script type="text/javascript">
			jQuery(function($) {
				$.supersized({

					// Functionality
					<?php
					$count = 0;
					foreach ( $settings as $name => $value ) {
						$count++;
						if ( $count > 1 ) {
						echo ",\n";
						}
						echo $name . ':' . $value;
					}
                    echo ',';
                    ?>
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
					thumb_links				:	1,			// Individual thumb links for each slide
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
							image:'<?php echo $image['url']; ?>',
							title:'<?php echo $image['title']; ?>',
							caption:'<?php echo $image['caption']; ?>',
							url: '<?php echo $image['url']; ?>',
							thumb: '<?php echo $image['thumb']; ?>'
						}
						<?php
					} ?>
				],

					// Theme Options
					mouse_scrub				:	0

			});
			});
		</script>
		<?php
	}

}

ENSS_Front_End::get_instance();