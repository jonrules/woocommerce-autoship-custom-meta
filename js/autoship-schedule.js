AutoshipSchedule.prototype.onBindScheduleActions.push(function ($schedule) {
	var context = this;
	// Custom meta
	var saveCustomMetaField = function() {
		var $input = jQuery(this);
		var schedule_id = $input.data('schedule-id');
		var data = {
			schedule_id: schedule_id,
			key: $input.attr('name'),
			value: $input.val()
		};
		context.sendRequest(schedule_id, AUTOSHIP_SCHEDULES.ajax_url + '?action=schedules_action_save_custom_meta_field', data, 700, function () {

		});
	};
	$schedule.find('.wc-autoship-custom-meta-schedule-field').keyup(saveCustomMetaField);
});