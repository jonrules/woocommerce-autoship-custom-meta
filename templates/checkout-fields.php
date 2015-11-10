<div class="wc-autoship-custom-meta-checkout-fields checkout-group">
	<?php foreach ( $fields as $f => $field ): ?>
		<p class="form-row">
			<label for="wc-autoship-custom-meta-checkout-field-<?php echo $f; ?>"><?php echo esc_html( $field['key'] ); ?></label>
			<input type="text" id="wc-autoship-custom-meta-checkout-field-<?php echo $f; ?>" name="<?php echo esc_attr( $field['key'] ); ?>" class="wc-autoship-custom-meta-checkout-field input-text" value="<?php echo esc_attr( $field['default_value'] ); ?>" />
		</p>
	<?php endforeach; ?>
</div>