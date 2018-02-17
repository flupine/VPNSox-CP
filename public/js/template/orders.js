$(document).ready(function() {

	var userSelect = $('.datalist-filter__user').selectize({
		create: false,
		sortField: 'text'
	});

	var productSelect = $('.datalist-filter__product').selectize({
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

		var order = $('.orders-preview');
		order.find('.orders-preview__name').text(data[0]).attr('title', data[0]);
		order.find('.orders-preview__sum').text(data[4]).attr('title', data[4]);
		order.find('.orders-preview__date').text(data[3]).attr('title', data[3]);
		order.find('.orders-preview__user').text($(data[1]).text()).attr('title', $(data[1]).text());
		order.find('.orders-preview__product').text($(data[2]).text()).attr('title', $(data[2]).text());
	}

	var tables = $('.datatable')
		.on('preInit.dt', function (e, settings) {
			var api = new $.fn.dataTable.Api( settings),
				orders = $(api.table().node()).data('orders');
			api.ajax.url('data/orders/'+orders+'.json');
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
			initComplete: function () {

				// User filter filling
				this.api().column(1)
					.cache( 'search' )
					.sort()
					.each( function ( d ) {
						userSelect[0].selectize.addOption({value: d, text: d});
						userSelect[0].selectize.refreshOptions();
					} );

				// Product filter filling
				this.api().column(2)
					.cache( 'search' )
					.sort()
					.each( function ( d ) {
						productSelect[0].selectize.addOption({value: d, text: d});
						productSelect[0].selectize.refreshOptions();
					} );

				// Date filter filling
				this.api().column(3)
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

				// Sum filter filling
				this.api().column(4)
					.cache( 'search' )
					.sort()
					.each( function ( sum ) {
						var options = slider.options,
							sum = parseFloat(sum.replace(/[^0-9\.]+/g, ''));

						if (sum < options.min) {
							slider.update({
								min: sum,
								from: sum
							});
						}

						if (sum > options.max) {
							slider.update({
								max: sum,
								to: sum
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

	userSelect[0].selectize.on('change',function(value) {
		$.fn.dataTable.tables()
			.forEach(function(t) {
				$(t).DataTable()
					.column(1)
					.search( value )
					.draw();
			})
	});

	productSelect[0].selectize.on('change',function(value) {
		$.fn.dataTable.tables()
			.forEach(function(t) {
				$(t).DataTable()
					.column(2)
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

	$.fn.dataTable.ext.search.push(
		function( settings, data, dataIndex ) {
			var from = new Date($('.datalist-filter__from').val());
			var to = new Date($('.datalist-filter__to').val());
			var date = new Date(data[3]) || 0;

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
			var slider = $('#datalist-filter__sum').val().split(';');
			var min = slider[0];
			var max = slider[1];
			var sum = parseFloat(data[4].replace(/[^0-9\.]+/g, ''));

			if ( ( min == undefined && max == undefined ) ||
				( isNaN(sum) ) ||
				( min == undefined && sum <= max ) ||
				( min <= sum && max == undefined ) ||
				( min <= sum && sum <= max ) )
			{
				return true;
			}
			return false;
		}
	);
});
