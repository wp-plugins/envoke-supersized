<?php
/**
 * Implements settings
 *
 * This file contains a class responsible for managing plugin settings, including setting defaults, retreiving setting
 * values, and storing settings to the database.
 *
 * @since 2.0.0
 *
 * @package Envoke_Supersized
 * @subpackage Settings
 */

/**
 * Envoke Supersized Settings Class.
 *
 * @since 2.0.0
 */
class ENSS_Settings {

	/**
	 * Contains the instance of this class
	 *
	 * @var ENSS_Settings
	 */
	protected static $_instance;

	/**
	 * The settings page hook.
	 *
	 * @since 2.0.0
	 * @access protected
	 * @var string $settings_page_hook The settings page hook.
	 */
	protected $settings_page_hook;

	/**
	 * The settings menu slug.
	 *
	 * @since 2.0.0
	 * @access protected
	 * @var string The settings menu slug.
	 */
	protected $settings_page_slug = 'enss-settings';

	/**
	 * The database option name.
	 *
	 * @since 2.0.0
	 * @access protected
	 * @var string $settings_option_name The database option name.
	 */
	protected $settings_option_name = 'enss-settings';

	/**
	 * The settings nonce action name.
	 *
	 * @since 2.0.0
	 * @access protected
	 * @var string $nonce_action The settings nonce action name.
	 */
	protected $nonce_action = 'enss-save-settings';

	/**
	 * The settings nonce form field name.
	 *
	 * @since 2.0.0
	 * @access protected
	 * @var string $nonce_name The settings nonce form field name.
	 */
	protected $nonce_name = 'enss-settings-nonce';

	/**
	 * Tracks if settings have been loaded.
	 *
	 * This will be set to true after settings have been loaded from the database, to make sure we don't load them
	 * more times than necessary.
	 *
	 * @since 2.0.0
	 * @access protected
	 * @var bool $settings_loaded Tracks if settings have been loaded.
	 */
	protected $settings_loaded = false;

	/**
	 * Contains settings, setting type, defaults, and labels.
	 *
	 * @since 2.0.0
	 * @access protected
	 * @see ENSS_Settings::build_settings()
	 * @var array $settings Contains settings, setting type, defaults, and labels.
	 */
	protected $settings = array();

	/**
	 * The default args available to functions that get settings.
	 *
	 * @since 2.0.0
	 * @access protected
	 * @var array $_get_settings_default_args
	 */
	protected $_get_settings_default_args = array(
		'return' => 'value',
	);

	/**
	 * Returns the instance of this class
	 *
	 * @return ENSS_Settings
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new ENSS_Settings();
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
		add_action( 'init', array( $this, 'build_settings' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
	}

	/**
	 * Load admin scripts.
	 *
	 * @since 2.0.0
	 */
	public function admin_enqueue_scripts() {
		if ( isset( $_GET['page'] ) && $this->settings_page_slug == $_GET['page'] ) {
			wp_register_script( 'enss-settings', ENSS_URL . 'assets/js/enss-settings.min.js', array( 'jquery' ), ENSS_VERSION, true );
			wp_enqueue_script( 'enss-settings' );
		}
	}

	/**
	 * Add plugin settings inside post type menu.
	 *
	 * @since 2.0.0
	 */
	public function add_settings_page() {
		$this->settings_page_hook = add_submenu_page(
			'edit.php?post_type=' . ENSS_Slide::get_instance()->get_post_type(),
			__( 'Settings', 'enss' ),
			__( 'Settings', 'enss' ),
			'edit_posts' ,
			$this->settings_page_slug,
			array( $this, 'settings_page' )
		);
	}

	/**
	 * Render the settings page.
	 *
	 * @since 2.0.0
	 */
	public function settings_page() {
		$this->update_settings();
		?>
		<div class="wrap">
			<form method="POST">
				<h2 class="nav-tab-wrapper" id="enss-tab-wrapper">
					<a href="#supersized-settings" class="nav-tab nav-tab-active enss-tab"><?php _e( 'Supersized Settings', 'enss' ); ?></a>
					<a href="#enss-settings" class="nav-tab enss-tab"><?php _e( 'Plugin Settings', 'enss' ); ?></a>
					<a href="#display-settings" class="nav-tab enss-tab"><?php _e( 'Display Settings', 'enss' ); ?></a>
				</h2>
				<div id="supersized-settings" class="enss-panel active">
					<?php $this->get_settings_panel( 'supersized' ); ?>
				</div>

				<div id="enss-settings" class="enss-panel hidden">
					<?php $this->get_settings_panel( 'enss' ); ?>
				</div>

				<div id="display-settings" class="enss-panel hidden">
					<?php $this->get_settings_panel( 'display' ); ?>
				</div>

				<p class="submit">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes', 'enss' ); ?>">
				</p>
				<?php
				wp_nonce_field( $this->nonce_action, $this->nonce_name );
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Get settings panel HTML, for specified group.
	 *
	 * @since 2.0.0
	 *
	 * @param string $group The settings group to generate HTML for.
	 */
	public function get_settings_panel( $group ) {
		?>
		<table class="form-table enss-settings">
			<tbody>
				<?php
				foreach ( $this->get_settings_group( $group, array( 'return' => 'all' ) ) as $name => $values ) {
					?>
					<tr>
						<th scope="row">
							<?php $this->get_form_label( $name, $values ); ?>
						</th>
						<td>
							<?php $this->get_form_field( $name, $values ); ?>
						</td>
					</tr>
				<?php
				}
				?>
			</tbody>
		</table>
	<?php
	}

	/**
	 * Get an HTML label.
	 *
	 * @since 2.0.0
	 *
	 * @see ENSS_Settings::get_settings_panel()
	 *
	 * @param string $name The setting name.
	 * @param array $values The setting values.
	 */
	public function get_form_label( $name, $values ) {
		?>
		<label for="enss-<?php echo $name; ?>"><?php echo $values['label']; ?></label>
		<?php
	}

	/**
	 * Get an HTML form field.
	 *
	 * @since 2.0.0
	 *
	 * @see ENSS_Settings::get_settings_panel()
	 *
	 * @param string $name The setting name.
	 * @param array $values The setting values.
	 *
	 * @return void|WP_Error Returns an error if an unsupported field.
	 */
	public function get_form_field( $name, $values ) {
		switch( $values['type'] ) {
			case 'text':
				?>
				<input type="text" name="<?php echo $name; ?>" id="enss-<?php echo $name; ?>" class="regular-text" value="<?php echo esc_attr( $this->get_setting( $name ) ); ?>"/>
				<?php
				break;
			case 'select':
				?>
				<select name="<?php echo $name; ?>" id="enss-<?php echo $name; ?>" class="regular-text">
					<?php
					foreach( $values['options'] as $option_value => $option_label ) {
						?>
						<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $this->get_setting($name), $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
					<?php
					}
					?>
				</select>
				<?php
				break;
			case 'number':
				?>
				<input type="number" name="<?php echo $name; ?>" id="enss-<?php echo $name; ?>" class="regular-text" value="<?php echo esc_attr( $this->get_setting( $name ) ); ?>"/>
				<?php
				break;
			case 'boolean':
				?>
				<fieldset>
					<label for="enss-<?php echo $name; ?>-yes">
						<input type="radio" name="<?php echo $name; ?>" id="enss-<?php echo $name; ?>-yes" value="1" <?php checked( $this->get_setting( $name ), '1' ); ?>/>
						<span><?php _e( 'Yes', 'enss' ); ?></span>
					</label>
					<br>
					<label for="enss-<?php echo $name; ?>-no">
						<input type="radio" name="<?php echo $name; ?>" id="enss-<?php echo $name; ?>-no" value="0" <?php checked( $this->get_setting( $name ), '0' ); ?>/>
						<span><?php _e( 'No', 'enss' ); ?></span>
					</label>
				</fieldset>
				<?php
				break;
				break;
			default:
				return new WP_Error( 'enss-error', __( 'Invalid Setting Type', 'enss' ) );
				break;
		}
	}

	/**
	 * Processes user submited settings.
	 *
	 * @since 2.0.0
	 *
	 * @return void|WP_Error Returns an error if settings are not able to be saved.
	 */
	public function update_settings() {
		//return if nonce doesn't verify. Since this checks if nonce exists, it also returns if there was no data POSTed
		if (
			! isset( $_POST[ $this->nonce_name ] )
			|| ! wp_verify_nonce( $_POST[ $this->nonce_name ], $this->nonce_action )
			|| ! current_user_can( apply_filters( 'enss-update-settings-capability', 'manage_options' ) )
		) {
			return new WP_Error( 'enss-error', __( 'Unable to save settings', 'enss' ) );
		}

		foreach( $this->settings as $name => $values ) {
			if ( isset( $_POST[ $name ] ) ) {
				$this->set_setting( $name, $_POST[ $name ] );
			}
		}

		$this->save_settings();
	}

	/**
	 * Saves settings to the database
	 *
	 * @since 2.0.0
	 */
	public function save_settings() {
		$values = $this->get_all_settings();
		update_option( $this->settings_option_name, json_encode( $values ) );
	}

	/**
	 * Load settings from the database
	 *
	 * @since 2.0.0
	 *
	 * @param bool $force If true, loads settings even if already loaded.
	 */
	public function load_settings( $force = false ) {
		if ( ! $this->settings_loaded || $force ) {
			$settings = json_decode( get_option( $this->settings_option_name ), true );

			if ( empty( $settings ) ) {
				$settings = ENSS_Back_Compat::get_instance()->load_settings( $settings );
			}

			$defaults = $this->get_default_settings();

			$merged = wp_parse_args( $settings, $defaults );

			$this->settings_loaded = true; //need to set this before overwriting the values, or we get in a load settings loop

			$this->set_settings_bulk( $merged );
		}
	}

	/**
	 * Get the value of a setting.
	 *
	 * @since 2.0.0
	 *
	 * @param string $setting Setting name.
	 * @param array $args Optional. Arguments to change what is returned.
	 *
	 * @return mixed The value of the setting, or an error if the setting does not exist.
	 */
	public function get_setting( $setting, $args = array() ) {
		$this->load_settings();

		$defaults = $this->_get_settings_default_args;
		$args = wp_parse_args( $args, $defaults );

		if ( isset( $this->settings[ $setting ] ) ) {
			$setting = $this->settings[ $setting ];
			if ( 'all' == $args['return'] ) {
				return $setting;
			} else {
				return $setting[ $args['return'] ];
			}
		}

		return new WP_Error( 'enss-error', __( 'Invalid Setting', 'enss' ) );
	}

	/**
	 * Get the values of all settings.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args Optional. Arguments to change what is returned.
	 *
	 * @return array The values of all settings.
	 */
	public function get_all_settings( $args = array() ) {
		$this->load_settings();

		$defaults = $this->_get_settings_default_args;
		$args = wp_parse_args( $args, $defaults );

		if ( 'all' == $args['return'] ) {
			return $this->settings;
		} else {
			return wp_list_pluck( $this->settings, $args['return'] );
		}
	}

	/**
	 * Get the values for settings in a particular group
	 *
	 * @since 2.0.0
	 *
	 * @param string $group The settings group to return.
	 * @param array $args Optional. Arguments to change what is returned.
	 *
	 * @return array Settings from the requested group.
	 */
	public function get_settings_group( $group, $args = array() ) {
		$this->load_settings();

		$defaults = $this->_get_settings_default_args;
		$args = wp_parse_args( $args, $defaults );

		$settings = wp_list_filter( $this->settings, array( 'group' => $group ) );
		if ( 'all' == $args['return'] ) {
			return $settings;
		} else {
			return wp_list_pluck( $settings, $args['return'] );
		}
	}

	/**
	 * Get the default values for all settings.
	 *
	 * @since 2.0.0
	 *
	 * @return array Default values for all settings.
	 */
	public function get_default_settings() {
		return wp_list_pluck( $this->settings, 'default' );
	}

	/**
	 * Set a particular setting value.
	 *
	 * @since 2.0.0
	 *
	 * @param $setting string Setting name.
	 * @param $value mixed Value of the setting.
	 *
	 * @return boolean|WP_Error True if successful, or an error if something goes wrong.
	 */
	public function set_setting( $setting, $value ) {
		$this->load_settings();

		if ( ! isset( $this->settings[ $setting ] ) ) {
			return new WP_Error( 'enss-error', __( 'Invalid Setting', 'enss' ) );
		}

		$this->settings[ $setting ]['value'] = sanitize_text_field( $value );
	}

	/**
	 * Set multiple settings at once.
	 *
	 * @since 2.0.0
	 *
	 * @param array Settings, provided as name / value pairs.
	 *
	 * @return boolean|WP_Error True if successful, or an error if something goes wrong.
	 */
	public function set_settings_bulk( $settings ) {
		foreach ( $settings as $setting => $value ) {
			$this->set_setting( $setting, $value );
		}
	}

	/**
	 * Builds the settings array
	 *
	 * @since 2.0.0
	 */
	public function build_settings() {
		$this->settings = array(
			/* Supersized Settings Group */
			'slideshow' => array(
				'group' => 'supersized',
				'label' => __( 'Slideshow', 'enss' ),
				'type' => 'boolean',
				'default' => '1',
				'value' => null,
			),
			'autoplay' => array(
				'group' => 'supersized',
				'label' => __( 'Auto Play', 'enss' ),
				'type' => 'boolean',
				'default' => '1',
				'value' => null,
			),
			'start_slide' => array(
				'group' => 'supersized',
				'label' => __( 'Start Slide (number)', 'enss' ),
				'type' => 'number',
				'min' => '1',
				'max' => '',
				'default' => '1',
				'value' => null,
			),
			'random' => array(
				'group' => 'supersized',
				'label' => __( 'Random Start Slide', 'enss' ),
				'type' => 'boolean',
				'default' => '0',
				'value' => null,
			),
			'slide_interval' => array(
				'group' => 'supersized',
				'label' => __( 'Slide Interval (ms)', 'enss' ),
				'type' => 'number',
				'min' => '1',
				'max' => '',
				'default' => '3000',
				'value' => null,
			),
			'transition' => array(
				'group' => 'supersized',
				'label' => __( 'Transition Effect', 'enss' ),
				'type' => 'select',
				'options' => array(
					'0' => __( 'No transition effect', 'enss' ),
					'1' => __( 'Fade effect (Default)', 'enss' ),
					'2' => __( 'Slide in from top', 'enss' ),
					'3' => __( 'Slide in from right', 'enss' ),
					'4' => __( 'Slide in from bottom', 'enss' ),
					'5' => __( 'Slide in from left', 'enss' ),
					'6' => __( 'Carousel from right to left', 'enss' ),
					'7' => __( 'Carousel from left to right', 'enss' ),
				),
				'default' => '1',
				'value' => null,
			),
			'transition_speed' => array(
				'group' => 'supersized',
				'label' => __( 'Transition Speed (ms)', 'enss' ),
				'type' => 'number',
				'min' => '1',
				'max' => '',
				'default' => '1000',
				'value' => null,
			),
			'progress_bar' => array(
				'group' => 'supersized',
				'label' => __( 'Progress Bar', 'enss' ),
				'type' => 'boolean',
				'default' => '1',
				'value' => null,
			),
			'stop_loop' => array(
				'group' => 'supersized',
				'label' => __( 'Stop Slideshow After Last Slide', 'enss' ),
				'type' => 'boolean',
				'default' => '0',
				'value' => null,
			),
			/* ENSS Settings Group */
			'back_compat' => array(
				'group' => 'enss',
				'label' => __( 'Enable Backwards Compatibility', 'enss' ),
				'description' => __( 'Useful if you previously had an older version of this plugin installed, and still have data in an older format', 'enss' ),
				'type' => 'boolean',
				'default' => '1',
				'value' => null
			),
			/* ENSS Display Setting Group */
			'overlay' => array(
				'group' => 'display',
				'label' => __( 'Image Overlay', 'enss' ),
				'description' => __( 'Allows customization of the overlay that appears on top of the images', 'enss' ),
				'type' => 'select',
				'options' => array(
					'none' => __( 'None', 'enss' ),
					'dots' => __( 'Dots', 'enss' ),
					'hlines' => __( 'Horizontal Lines', 'enss' ),
					'vlines' => __( 'Vertical Lines', 'enss' ),
					'grid' => __( 'Grid', 'enss' ),
				),
				'default' => 'dots',
				'value' => null,
			),
			'arrow_navigation' => array(
				'group' => 'display',
				'label' => __( 'Show Arrows for Navigation', 'enss' ),
				'type' => 'boolean',
				'default' => '1',
				'value' => null,
			),
			'control_bar' => array(
				'group' => 'display',
				'label' => __( 'Show Control Bar', 'enss' ),
				'description' => __( 'If this is turned off, it will also hide elements that are within it, such as thumbnails and the play/pause button', 'enss' ),
				'type' => 'boolean',
				'default' => '1',
				'value' => null,
			),
			'play_pause' => array(
				'group' => 'display',
				'label' => __( 'Show Play/Pause Button', 'enss' ),
				'type' => 'boolean',
				'default' => '1',
				'value' => null,
			),
			'slide_counter' => array(
				'group' => 'display',
				'label' => __( 'Show Slide Counter', 'enss' ),
				'type' => 'boolean',
				'default' => '1',
				'value' => null,
			),
			'slide_title' => array(
				'group' => 'display',
				'label' => __( 'Show Slide Title', 'enss' ),
				'type' => 'boolean',
				'default' => '1',
				'value' => null,
			),
			'slide_caption' => array(
				'group' => 'display',
				'label' => __( 'Show Slide Caption', 'enss' ),
				'type' => 'boolean',
				'default' => '1',
				'value' => null,
			),
			'thumbnails' => array(
				'group' => 'display',
				'label' => __( 'Show Thumbnails', 'enss' ),
				'type' => 'boolean',
				'default' => '1',
				'value' => null,
			),
		);
	}
}

ENSS_Settings::get_instance();