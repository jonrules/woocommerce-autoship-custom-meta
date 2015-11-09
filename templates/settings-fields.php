<table class="wc-autoship-custom-meta-settings-fields">
	<thead>
	<tr>
		<th><?php echo __( 'Meta Key', 'wc-autoship-custom-meta' ); ?></th>
		<th><?php echo __( 'Default Value', 'wc-autoship-custom-meta' ); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php for ( $row = 0; $row < 10; $row++ ): ?>
		<?php $key = isset( $fields[ $row ]['key'] ) ? $fields[ $row ]['key'] : ''; ?>
		<?php $default_value = isset( $fields[ $row ]['default_value'] ) ? $fields[ $row ]['default_value'] : ''; ?>
		<tr>
			<td><input type="text" name="wc_autoship_custom_meta_fields[<?php echo $row; ?>][key]" class="wc-autoship-custom-meta-field-key" value="<?php echo esc_attr( $key ); ?>" /></td>
			<td><input type="text" name="wc_autoship_custom_meta_fields[<?php echo $row; ?>][default_value]" class="wc-autoship-custom-meta-field-default-value" value="<?php echo esc_attr( $default_value ); ?>" /></td>
		</tr>
	<?php endfor; ?>
	</tbody>
</table>
