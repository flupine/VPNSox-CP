$(document).ready(function() {
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

	// Product chart init
	function productChart(el) {
		if (!$(el).find('canvas').length) {
			$(el).sparkline(
				'html',
				{
					width: '40px',
					height: 'auto',
					type: 'line',
					lineColor: '#ed4949',
					lineWidth: 1,
					fillColor: 'rgba(237, 73, 73, 0.6)',
					spotColor: '#FF5722',
					minSpotColor: '#FF5722',
					maxSpotColor: '#FF5722',
					highlightSpotColor: '#FF5722',
					spotRadius: 1
				}
			);
		}
	}

	// Preview update
	function previewUpdate(data) {

		var product = $('.products-preview');
		product.find('.products-preview__name').text(data[2]).attr('title', data[2]);
		product.find('.products-preview__salary').text(data[4]).attr('title', data[4]);
		product.find('.products-preview__date').text(data[3]).attr('title', data[3]);
		product.find('.products-preview__type').text(data[7]).attr('title', data[7]);
		product.find('.products-preview__status').text(data[5]).attr('title', data[5]);


		var chartData = JSON.parse('['+$(data[6]).text()+']');
		product.find('.products-preview__stat').sparkline(
			chartData,
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
				products = $(api.table().node()).data('products');
			api.ajax.url('data/products/'+products+'.json');
		})
		.on('init.dt', function () {
			tabInfo(this);
			var previewData = $.fn.dataTable.tables( {visible: true, api: true}).rows(0).data()[0];
			previewUpdate(previewData);
		})
		.on('draw.dt', function () {
			tabInfo(this);
			$(this).find('.products__stat').each(function() {
				productChart(this);
			});
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
					"targets": [ 7 ],
					"visible": false
				}
			],
			initComplete: function () {

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

				// Salary filter filling
				this.api().column(4)
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

		$($.fn.dataTable.tables({visible: true})).find('.products__stat').each(function() {
			productChart(this);
		});
	} );

	$('.datalist-filter__search input').on( 'keyup', function () {
		tables.search( this.value ).draw();
	} );


	$('.input-daterange').on('changeDate', function(e) {
		tables.draw();
	});

	$('#datalist-filter__actives').on('change', function() {
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
			var slider = $('#datalist-filter__salary').val().split(';');
			var min = slider[0];
			var max = slider[1];
			var salary = parseFloat(data[4].replace(/[^0-9\.]+/g, ''));

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
			var status = data[5].toLowerCase();

			if ( ( !actives ) || ( actives && status == 'active' ) )
			{
				return true;
			}
			return false;
		}
	);
});
