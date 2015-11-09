<table class="wc-autoship-custom-meta-input-fields">
	<tbody>
	<?php foreach ( $fields as $f => $field ): ?>
		<tr>
			<td><label for="wc-autoship-custom-meta-input-field-<?php echo $f; ?>"<?php echo esc_html( $field['key'] ); ?></label></td>
			<td><input type="text" id="wc-autoship-custom-meta-input-field-<?php echo $f; ?>" name="<?php echo esc_attr( $field['key'] ); ?>" class="wc-autoship-custom-meta-input-field" value="<?php echo esc_attr( $field['value'] ); ?>" /></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
