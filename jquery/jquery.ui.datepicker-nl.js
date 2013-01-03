/* Dutch (UTF-8) initialisation for the jQuery UI time picker addon. */
jQuery(function($){
	$.timepicker.regional['nl'] = {
		timeOnlyTitle: 'Kies tijd',
		timeText: 'Tijd',
		hourText: 'Uur',
		minuteText: 'Minuut',
		secondText: 'Seconde',
		millisecText: 'Milliseconde',
		timezoneText: 'Tijdzone',
		currentText: 'Nu',
		closeText: 'OK',
		timeFormat: 'HH:mm',
		amNames: ['AM', 'A'],
		pmNames: ['PM', 'P'],
		isRTL: false
	};
	$.timepicker.setDefaults($.timepicker.regional['nl']);
});
