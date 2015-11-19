<div class="wc-autoship-custom-meta-checkout-fields checkout-group">
	<?php if ( ! empty( $fields_title ) ): ?>
		<h3><?php echo esc_html( $fields_title ); ?></h3>
	<?php endif; ?>
	<?php foreach ( $fields as $f => $field ): ?>
		<p class="form-row">
			<?php $title = ( ! empty( $field['title'] ) ) ? $field['title'] : $field['key']; ?>
			<label for="wc-autoship-custom-meta-checkout-field-<?php echo $f; ?>"><?php echo esc_html( $title ); ?></label>
			<input type="text" id="wc-autoship-custom-meta-checkout-field-<?php echo $f; ?>" name="wc_autoship_custom_meta[<?php echo $f; ?>]" class="wc-autoship-custom-meta-checkout-field input-text" value="<?php echo esc_attr( $field['default_value'] ); ?>" />
		</p>
	<?php endforeach; ?>
</div>