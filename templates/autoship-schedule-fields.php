<div class="wc-autoship-custom-meta-schedule-fields">
	<hr />

	<?php foreach ( $fields as $f => $field ): ?>
		<div class="form-group">
			<label for="wc-autoship-custom-meta-schedule-field-<?php echo $f; ?>"><?php echo esc_html( $field['key'] ); ?></label>
			<input type="text" id="wc-autoship-custom-meta-schedule-field-<?php echo $f; ?>" name="<?php echo esc_attr( $field['key'] ); ?>" class="wc-autoship-custom-meta-schedule-field form-control" value="<?php echo esc_attr( $field['default_value'] ); ?>" data-schedule-id="<?php echo esc_attr( $schedule_id ); ?>" />
		</div>
	<?php endforeach; ?>

	<hr />
</div>