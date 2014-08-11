<?php
/**
 * Tweet Please
 *
 * @package   Tweet Please
 * @author    Javier Montes <javier@montesjmm.com> @montesjmm
 * @license   GPL-2.0
 * @link      http://montesjmm.com/tweet-please
 * @copyright 2014 Javier Montes
 */
?>

<div class="wrap">

	<h2>Tweet Please Settings</h2>

	<form method="post">

		<?php settings_fields('tp-settings-group'); ?>
		<?php do_settings_sections('tp-settings-group'); ?>

		<table class="form-table">

			<?php foreach ($options as $option): ?>

				<tr valign="top">
					<th scope="row"><?=$option['name']?></th>
					<td>
						<?php switch($option['type']) {
							case 'text':
								echo '<input type="text" name="' . $option['slug'] . '" value="' . get_option($option['slug'] , $option['default']) . '">';
								break;
							case 'textarea':
								echo '<textarea name="' . $option['slug'] . '">' . get_option($option['slug'] , $option['default']) . '</textarea>';
								break;
							case 'select':
								echo '<select name="' . $option['slug'] . '">';
								$savedValue = get_option($option['slug']);
								foreach ($option['default'] as $option) {
									if ($savedValue == $option['value'])
										echo '<option selected value="' . $option['value'] . '">' . $option['name'] . '</option>';
									else
										echo '<option value="' . $option['value'] . '">' . $option['name'] . '</option>';
								}
								echo '</select>';
								break;
						}
						?>
					</td>
				</tr>

			<?php endforeach; ?>

		</table>

		<?php submit_button(); ?>

	</form>

	<h2>Tweet Please Log</h2>

	<table class="wp-list-table widefat">
		<thead>
			<tr>
				<th>Action</th>
				<th>Date/Time</th>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($log as $logLine): ?>

				<tr>
					<td><?=$logLine['text']?></td>
					<td><?=$logLine['date']?></td>
				</tr>

			<?php endforeach; ?>
		</tbody>
	</table>

</div>
