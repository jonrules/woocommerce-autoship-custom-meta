<div class="wc-autoship-custom-meta-schedule-fields">
	<?php if ( ! empty( $fields_title ) ): ?>
		<h4><?php echo esc_html( $fields_title ); ?></h4>
	<?php endif; ?>
	<hr />
	<?php foreach ( $fields as $f => $field ): ?>
		<div class="form-group">
			<?php $title = ( ! empty( $field['title'] ) ) ? $field['title'] : $field['key']; ?>
			<label for="wc-autoship-custom-meta-schedule-field-<?php echo $f; ?>"><?php echo esc_html( $title ); ?></label>
			<input type="text" id="wc-autoship-custom-meta-schedule-field-<?php echo $f; ?>" name="<?php echo esc_attr( $field['key'] ); ?>" class="wc-autoship-custom-meta-schedule-field form-control" value="<?php if ( isset( $values[ $field['key'] ] ) ) echo esc_attr( $values[ $field['key'] ] ); ?>" data-schedule-id="<?php echo esc_attr( $schedule_id ); ?>" />
		</div>
	<?php endforeach; ?>

	<hr />
</div>