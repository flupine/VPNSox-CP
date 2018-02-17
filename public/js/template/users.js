$(document).ready(function() {
	
	var select = $('.datalist-filter__email').selectize({
		create: false,
		sortField: 'text'
	});

	$('.input-daterange').datepicker();

	$('.slider').ionRangeSlider({
		type: "double",
		grid: false,
		min: 0,
		max: 0,
		from: 0,
		to: 0,
		prefix: "$: ",
		decorate_both: false,
		onChange: function (data) {
			tables.draw();
		}
	});

	var slider = $(".slider").data("ionRangeSlider");

	// Table tab count update
	function tabInfo(table) {
		var id = $(table).closest('.tab-pane').attr('id'),
			tab = $('.nav-tabs a[aria-controls='+id+']'),
			length = $(table).DataTable().page.info().recordsDisplay,
			label = tab.find('span.label');
		if (label.length) { label.remove(); }
		tab.append('<span class="label">'+length+'</span>');
	}

	// Preview update
	function previewUpdate(data) {
		var user = $('.users-preview');
		user.find('.users-preview__name').text(data[1]).attr('title', data[1]);
		user.find('.users-preview__location').text(data[2]).attr('title', data[2]);
		user.find('.users-preview__contact').text(data[3]).attr('title', data[3]);
		user.find('.users-preview__date').text(data[4]).attr('title', data[4]);
		user.find('.users-preview__position').text(data[6]).attr('title', data[6]);
		user.find('.users-preview__status').text(data[8]).attr('title', data[8]);

		user.find('.users-preview__stat').sparkline(
			JSON.parse('['+data[7]+']'),
			{
				type: 'bar',
				height: '34px',
				barSpacing: 2,
				barColor: '#1e59d9',
				negBarColor: '#ed4949'
			}
		);
	}

	var tables = $('.datatable')
		.on('preInit.dt', function (e, settings) {
			var api = new $.fn.dataTable.Api( settings),
				users = $(api.table().node()).data('users');
			api.ajax.url('data/users/'+users+'.json');
		})
		.on('init.dt', function () {
			tabInfo(this);
			var previewData = $.fn.dataTable.tables( {visible: true, api: true}).rows(0).data()[0];
			previewUpdate(previewData);
		})
		.on('draw.dt', function () {
			tabInfo(this);
		})
		.on('search.dt', function () {
			tabInfo(this);
		})
		.DataTable({
			ordering: true,
			lengthChange: false,
			pagingType: 'numbers',
			select: {
				style: 'single'
			},
			columnDefs: [
				{
					"targets": [ 6 ],
					"visible": false
				},
				{
					"targets": [ 7 ],
					"visible": false
				}
			],
			initComplete: function () {

				// Email filter filling
				this.api().column(3)
					.cache( 'search' )
					.sort()
					.each( function ( d ) {
						select[0].selectize.addOption({value: d, text: d});
						select[0].selectize.refreshOptions();
					} );

				// Date filter filling
				this.api().column(4)
					.cache( 'search' )
					.sort()
					.each( function ( date ) {
						var min = new Date($('.datalist-filter__from').val());
						var max = new Date($('.datalist-filter__to').val());
						var val = new Date(date);

						if ((val != 'Invalid Date') && ((val < min) || (min == 'Invalid Date'))) {
							$('.datalist-filter__from').datepicker('setDate', new Date(date));
						}
						if ((val != 'Invalid Date') && ((val > max) || (max == 'Invalid Date'))) {
							$('.datalist-filter__to').datepicker('setDate', new Date(date));
						}


					} );

				// Salary filter filling
				this.api().column(5)
					.cache( 'search' )
					.sort()
					.each( function ( salary ) {
						var options = slider.options,
							salary = parseFloat(salary.replace(/[^0-9\.]+/g, ''));

						if (salary < options.min) {
							slider.update({
								min: salary,
								from: salary
							});
						}

						if (salary > options.max) {
							slider.update({
								max: salary,
								to: salary
							});
						}
					} );
			}
		})
		.on( 'select', function ( e, dt, type, indexes ) {
			var data = $(this).DataTable().rows( indexes ).data()[0];
			previewUpdate(data);
		});


	$('a[data-toggle="tab"]').on( 'shown.bs.tab', function (e) {
		$.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
	} );

	select[0].selectize.on('change',function(value) {
		$.fn.dataTable.tables()
			.forEach(function(t) {
				$(t).DataTable()
					.column(3)
					.search( value )
					.draw();
			})
	});


	$('.datalist-filter__search input').on( 'keyup', function () {
		tables.search( this.value ).draw();
	} );


	$('.input-daterange').on('changeDate', function(e) {
		tables.draw();
	});

	$('#datalist-filter__actives').on('change', function() {
		tables.draw();
	});

	$('.datalist-filter__location').on('keyup', function() {
		var location = $(this).val();
		$.fn.dataTable.tables()
			.forEach(function(t) {
				$(t).DataTable().column(2)
					.search( location)
					.draw();
			})
	});

	$.fn.dataTable.ext.search.push(
		function( settings, data, dataIndex ) {
			var from = new Date($('.datalist-filter__from').val());
			var to = new Date($('.datalist-filter__to').val());
			var date = new Date(data[4]) || 0;

			if ( ( from == 'Invalid Date' && to == 'Invalid Date' ) ||
				( date == 'Invalid Date' ) ||
				( from == 'Invalid Date' && date <= to ) ||
				( from <= date && to == 'Invalid Date' ) ||
				( from <= date && date <= to ) )
			{
				return true;
			}
			return false;
		}
	);

	$.fn.dataTable.ext.search.push(
		function( settings, data, dataIndex ) {
			var slider = $('#datalist-filter__salary').val().split(';');
			var min = slider[0];
			var max = slider[1];
			var salary = parseFloat(data[5].replace(/[^0-9\.]+/g, ''));

			if ( ( min == undefined && max == undefined ) ||
				( isNaN(salary) ) ||
				( min == undefined && salary <= max ) ||
				( min <= salary && max == undefined ) ||
				( min <= salary && salary <= max ) )
			{
				return true;
			}
			return false;
		}
	);

	$.fn.dataTable.ext.search.push(
		function( settings, data, dataIndex ) {
			var actives = $('#datalist-filter__actives').prop('checked') || false;
			var status = data[8].toLowerCase();

			if ( ( !actives ) || ( actives && status == 'active' ) )
			{
				return true;
			}
			return false;
		}
	);

	$('.selectpicker').selectpicker();
});
