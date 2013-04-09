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
			<h4>For now, use 1 for TRUE and 2 for FALSE</h4>

			<form method="POST">
				<table class="form-table envoke-supersized-settings">
					<tbody>
					<?php
					foreach ( self::$settings as $slug => $name ) {
						?>
						<tr valign="top">
							<th scope="row">
								<label for="envoke-supersized-<?php echo $slug; ?>"><?php echo $name; ?></label>
							</th>
							<td>
								<input type="text" name="<?php echo $slug; ?>" id="envoke-supersized-<?php echo $slug; ?>" class="regular-text" value="<?php echo self::${$slug}; ?>" />
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