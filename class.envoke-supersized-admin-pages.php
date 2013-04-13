<?php
/**
 * Created by christophermarslender
 */

class Envoke_Supersized_Admin_Pages extends Envoke_Supersized
{

	public static function settings() {
		if ( ! empty($_POST) ) {
			$settings = array();
			foreach ( self::$settings as $slug => $label ) {
				$settings[$slug] = isset($_POST[$slug]) ? $_POST[$slug] : self::$defaults[$slug];
			}
			update_option('envoke-supersized-settings',$settings);
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
					foreach ( self::$settings as $slug => $data ) {
						?>
						<tr valign="top">
							<th scope="row">
								<label for="envoke-supersized-<?php echo $slug; ?>"><?php echo $data['name']; ?></label>
							</th>
							<td>
								<?php
									switch($data['type']) {
										case 'text':
											echo '<input type="text" name="'.$slug.'" id="envoke-supersized-'.$slug.'" class="regular-text" value="'.self::${$slug}.'" />';
											break;
										case 'select':
											echo '<select name="'.$slug.'" id="envoke-supersized-'.$slug.'" class="regular-text">';
											foreach( $data['options'] as $value => $name ) {
												$selected = (integer)$value == (integer)self::${$slug} ? 'selected' : '';
												echo '<option value="'.$value.'" '.$selected.'>'.$name.'</option>';
											}
											echo '</select>';
											break;
										case 'number':
											echo '<input type="number" name="'.$slug.'" id="envoke-supersized-'.$slug.'" class="regular-text" value="'.self::${$slug}.'" />';
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
}