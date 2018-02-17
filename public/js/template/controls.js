$(document).ready(function() {
	$('.selectpicker').selectpicker();

	$('.nav-pills, .nav-tabs').tabdrop();

	$('.date').datepicker();
	$('.input-daterange').datepicker();
	$('.datepicker-embed').datepicker();
	$('.timepicker input').timepicker({showMeridian: false, showSeconds: true});

	$('.slider').ionRangeSlider({
		type: "double",
		grid: false,
		min: 0,
		max: 1000,
		from: 200,
		to: 750,
		prefix: "$: ",
		decorate_both: false
	});

	$('.selectize').selectize({
		create: false,
		sortField: 'text'
	});

	$('.select-country').selectize();

	$('.selectize_tags').selectize({
		plugins: ['remove_button'],
		options: [
			{value: 'right', text: 'Right'},
			{value: 'bootstrap', text: 'Bootstrap'},
			{value: 'admin', text: 'Admin'},
			{value: 'template', text: 'Template'},
			{value: 'awesome', text: 'Awesome'}
		],
		delimiter: ',',
		persist: false,
		create: function (input) {
			return {
				value: input,
				text: input
			}
		}
	});
});
