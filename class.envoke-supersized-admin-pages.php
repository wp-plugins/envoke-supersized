<?php
/**
 * Created by christophermarslender
 */

class Envoke_Supersized_Admin_Pages extends Envoke_Supersized
{

	public static function admin_enqueue_scripts() {
		wp_register_script('envoke-ss-admin', plugin_dir_url(__FILE__) . 'library/js/envoke-ss-admin.js',array('jquery'),self::$version,true);
		wp_register_style('envoke-ss-admin', plugin_dir_url(__FILE__) . 'library/css/envoke-ss-admin.css');

		wp_enqueue_script('envoke-ss-admin');
		wp_enqueue_style('envoke-ss-admin');
	}

	public static function settings() {
		if ( ! empty($_POST) ) {
			$settings = array();
			foreach ( self::$settings as $slug => $label ) {
				/*
				 * all the settings actually have a numeric value, so we can just get the intval() of them all, to make sure
				 * we dont have any funny business going on here..It will just be 0, if for some reason someone is trying to
				 * pass a <script> or something else besides a number
				 */
				$settings[$slug] = isset($_POST[$slug]) ? intval($_POST[$slug]) : self::$defaults[$slug];
			}

			update_option('envoke-supersized-settings', $settings);
			self::load_settings(true);
		} else {
			self::load_settings();
		}

		?>
		<div class="wrap">
			<div class="icon32" id="icon-options-general"></div>
			<h2>Supersized by Envoke</h2>

			<form method="POST">
				<table class="form-table envoke-supersized-settings">
					<tbody>
					<?php
					//no need to escape '$slug', since its value is not derived from user input
					//'$data' should be however. Its most likely user input.
					foreach ( self::$settings as $slug => $data ) {
						?>
						<tr valign="top">
							<th scope="row">
								<label for="envoke-supersized-<?php echo $slug; ?>"><?php echo htmlentities($data['name']); ?></label>
							</th>
							<td>
								<?php
									switch($data['type']) {
										case 'text':
											echo '<input type="text" name="'.$slug.'" id="envoke-supersized-'.$slug.'" class="regular-text" value="'.intval(self::${$slug}).'" />';
											break;
										case 'select':
											echo '<select name="'.$slug.'" id="envoke-supersized-'.$slug.'" class="regular-text">';
											foreach( $data['options'] as $value => $name ) {
												$selected = (integer)$value == (integer)self::${$slug} ? 'selected' : '';
												echo '<option value="'.htmlentities($value).'" '.$selected.'>'.htmlentities($name).'</option>';
											}
											echo '</select>';
											break;
										case 'number':
											echo '<input type="number" name="'.$slug.'" id="envoke-supersized-'.$slug.'" class="regular-text" value="'.intval(self::${$slug}).'" />';
											break;
										case 'boolean':
											$selectedyes = (integer)self::${$slug} ? 'checked' : '';
											$selectedno = (integer)self::${$slug} ? '' : 'checked';
											echo '<fieldset>';
											echo '<label for="envoke-supersized-'.$slug.'-yes">';
											echo '<input type="radio" name="'.$slug.'" id="envoke-supersized-'.$slug.'-yes" class="" value="1" '.$selectedyes.'/>';
											echo '<span>Yes</span>';
											echo '</label><br />';
											echo '<label for="envoke-supersized-'.$slug.'-no">';
											echo '<input type="radio" name="'.$slug.'" id="envoke-supersized-no'.$slug.'-no" class="" value="0" '.$selectedno.'/>';
											echo '<span>No</span>';
											echo '</label>';
											echo '</fieldset>';

											break;
										default:
											//this should never get reaced in a production version. This is here for debugging during development.
											throw new Exception("Type is not definied in " . __FILE__ . ' ' . __LINE__);
											break;
									}
								?>
							</td>
						</tr>
						<?php
					}
					?>
					</tbody>
				</table>
				<p class="submit">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
				</p>
			</form>
		</div>
		<?php
	}

	public static function per_page_metaboxes() {
		$screens = array( 'post', 'page' );
		foreach ($screens as $screen) {
			add_meta_box(
				'envoke-supersized-per-page-image',
				__( 'Supersized - Unique Page Image', 'envoke-supersized' ),
				array('Envoke_Supersized_Admin_Pages','per_page_metaboxes_content'),
				$screen
			);
		}
	}

	public static function per_page_metaboxes_content($post) {
		ob_start();

		$image_url = get_post_meta($post->ID,self::$per_page_image_url,true);
		?>
		<div class="envoke-supersized-per-page-image-inputs">
			<div class="image-preview">
				<img width="300" height="200" src="<?php echo htmlentities($image_url); ?>" id="image-preview-1" />
			</div>
			<div class="image-selector">
				<label for="image-input-1">Image Url</label>
				<input type="text" id="image-input-1" name="envoke-ss-per-page-image-url" value="<?php echo htmlentities($image_url); ?>" placeholder="Image URL" />
				<button class="button" data-envoke-ss-action="choose-image" data-for="#image-input-1" data-preview="#image-preview-1">Choose Image</button>
				<button class="button" data-envoke-ss-action="clear-image" data-for="#image-input-1" data-preview="#image-preview-1">Clear</button>
			</div>
		</div>
		<?php
		echo ob_get_clean();
	}

	public static function save_post($post_id) {
		if ( isset($_POST['envoke-ss-per-page-image-url']) ) {
			$url = sanitize_text_field($_POST['envoke-ss-per-page-image-url']);
			update_post_meta($post_id,self::$per_page_image_url,$url);
		}
	}

	/*
	 * Prevents issues with duplicate meta keys being POSTed to server
	 *
	 * So that we dont duplicate our inputs, we will make the meta keys with their own sections not show up in the custom field section
	 * by setting them as private, and we will handle saving their data ourselves
	 */
	public static function is_protected_meta($protected, $meta_key) {
		$protected = array();
		$protected[] = self::$per_page_image_content;
		$protected[] = self::$per_page_image_title;
		$protected[] = self::$per_page_image_url;

		return in_array($meta_key,$protected) ? true : $protected;
	}

}