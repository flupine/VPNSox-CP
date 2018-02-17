$(document).ready(function() {

	var month = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'];

	Morris.Line({
		element: $('.ld-widget-main__chart'),
		data: [
			{d: new Date('2015-01-01').getTime(), a: 15, b: 5, c: 75},
			{d: new Date('2015-02-01').getTime(), a: 60, b: 15, c: 90},
			{d: new Date('2015-03-01').getTime(), a: 30, b: 10, c: 80},
			{d: new Date('2015-04-01').getTime(), a: 50, b: 20, c: 90},
			{d: new Date('2015-05-01').getTime(), a: 35, b: 10, c: 95},
			{d: new Date('2015-06-01').getTime(), a: 90, b: 5, c: 15},
			{d: new Date('2015-07-01').getTime(), a: 35, b: 15, c: 50},
			{d: new Date('2015-08-01').getTime(), a: 50, b: 10, c: 100},
			{d: new Date('2015-09-01').getTime(), a: 30, b: 5, c: 75},
			{d: new Date('2015-10-01').getTime(), a: 95, b: 15, c: 30},
			{d: new Date('2015-11-01').getTime(), a: 30, b: 20, c: 45}
		],
		xkey: 'd',
		ykeys: ['a', 'b', 'c'],
		dateFormat: function (x) {
			return new Date(x).toDateString();
		},
		xLabelFormat: function (x) {
			return month[new Date(x).getMonth()];
		},
		labels: ['Light', 'Pro', 'Free'],
		lineColors: ['#ed4949', '#FED42A', '#20c05c', '#1e59d9'],
		pointSize: 0,
		pointStrokeColors: ['#ed4949', '#FED42A', '#20c05c', '#1e59d9'],
		lineWidth: 3,
		resize: true
	});

	Morris.Donut({
		element: $('.ld-widget-side__chart'),
		data: [
			{label: "Light", value: 30},
			{label: "Pro", value: 20},
			{label: "Free", value: 45}
		],
		colors: ['#ed4949', '#FED42A', '#20c05c', '#1e59d9'],
		backgroundColor: '#30363c',
		labelColor: '#88939C',
		resize: true
	});

	$('.selectpicker').selectpicker();
});
