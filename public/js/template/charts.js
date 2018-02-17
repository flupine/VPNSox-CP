$(document).ready(function() {
		// Mouse speed chart
	(function drawMouseSpeedDemo() {
		var mrefreshinterval = 500; // update display every 500ms
		var lastmousex=-1;
		var lastmousey=-1;
		var lastmousetime;
		var mousetravel = 0;
		var mpoints = [];
		var mpoints_max = 30;
		$('html').mousemove(function(e) {
			var mousex = e.pageX;
			var mousey = e.pageY;
			if (lastmousex > -1) {
				mousetravel += Math.max( Math.abs(mousex-lastmousex), Math.abs(mousey-lastmousey) );
			}
			lastmousex = mousex;
			lastmousey = mousey;
		});
		var mdraw = function() {
			var md = new Date();
			var timenow = md.getTime();
			if (lastmousetime && lastmousetime!=timenow) {
				var pps = Math.round(mousetravel / (timenow - lastmousetime) * 1000);
				mpoints.push(pps);
				if (mpoints.length > mpoints_max)
					mpoints.splice(0,1);
				mousetravel = 0;
				$('.template__sparkline-ms').sparkline(
					mpoints,
					{
						width: '100%',
						height: '60px',
						type: 'line',
						lineColor: '#1e59d9',
						lineWidth: 3,
						fillColor: false,
						spotColor: '#1e59d9',
						minSpotColor: '#1e59d9',
						maxSpotColor: '#1e59d9',
						highlightSpotColor: '#1e59d9',
						spotRadius: 2,
						tooltipSuffix: ' pixels per second'
					});
			}
			lastmousetime = timenow;
			setTimeout(mdraw, mrefreshinterval);
		}
		// We could use setInterval instead, but I prefer to do it this way
		setTimeout(mdraw, mrefreshinterval);
	})();


	$(".template__sparkline-bar").sparkline(
		[5,6,7,2,0,-1,-4,-2,4,5,3,5,2,-2,-4,2,6,8,7],
		{
			type: 'bar',
			height: '60px',
			barSpacing: 2,
			barColor: '#1e59d9',
			negBarColor: '#ed4949'
		}
	);

	$('.template__sparkline-composite').sparkline(
		[4,6,5,7,6,5,8,7,6,6,4,7,8,4,3,4,4,5,6,7],
		{
			type: 'bar',
			height: '60px',
			barSpacing: 2,
			barColor: '#20c05c',
			negBarColor: '#ed4949'
		}
	);
	$('.template__sparkline-composite').sparkline(
		[5,6,7,5,2,5,3,5,7,6,5,3],
		{
			composite: true,
			height: '60px',
			lineColor: '#FF5722',
			lineWidth: 3,
			fillColor: false,
			spotColor: '#ed4949',
			minSpotColor: '#ed4949',
			maxSpotColor: '#ed4949',
			highlightSpotColor: '#ed4949',
			spotRadius: 2
		}
	);

	$('.template__sparkline-area').sparkline(
		[5,6,7,9,9,5,3,5,7,6,8,7],
		{
			width: '100%',
			height: '60px',
			type: 'line',
			lineColor: '#ed4949',
			lineWidth: 3,
			fillColor: 'rgba(237, 73, 73, 0.6)',
			spotColor: '#FF5722',
			minSpotColor: '#FF5722',
			maxSpotColor: '#FF5722',
			highlightSpotColor: '#FF5722',
			spotRadius: 3
		}
	);

	function morrisChart(el) {
		var type = el.data('chart');
		switch (type) {
			case 'morris-line':
				Morris.Line({
					element: el[0],
					data: [
						{ y: '2006', a: 15 },
						{ y: '2007', a: 60  },
						{ y: '2008', a: 50  },
						{ y: '2009', a: 75  },
						{ y: '2010', a: 60 },
						{ y: '2011', a: 75  },
						{ y: '2012', a: 100 }
					],
					xkey: 'y',
					ykeys: ['a'],
					labels: ['Series A'],
					lineColors: ['#ed4949', '#FED42A', '#20c05c', '#FF5722'],
					pointSize: 0,
					pointStrokeColors: ['#ed4949', '#1e59d9', '#20c05c', '#FED42A', '#FF5722'],
					lineWidth: 6,
					resize: true
				});
				break;
			case 'morris-donut':
				Morris.Donut({
					element: el[0],
					data: [
						{label: "Download Sales", value: 12},
						{label: "In-Store Sales", value: 30},
						{label: "Mail-Order Sales", value: 20}
					],
					colors: ['#ed4949', '#1e59d9', '#20c05c', '#FF5722'],
					backgroundColor: '#30363c',
					labelColor: '#88939C',
					resize: true
				});
				break;
			case 'morris-area':
				Morris.Area.prototype.fillForSeries = function(i) {
					var color_original = this.colorFor(this.data[i], i, 'line');
					return color_original;
				};

				Morris.Area.prototype.drawFilledPath = function(path, fill) {
					return this.raphael.path(path).attr('fill', fill).attr('opacity', this.options.fillOpacity).attr('stroke', 'none');
				};

				Morris.Area({
					element: el[0],
					data: [
						{ y: '2006', a: 8, b: 15 },
						{ y: '2007', a: 60,  b: 50 },
						{ y: '2008', a: 50,  b: 40 },
						{ y: '2009', a: 75,  b: 65 },
						{ y: '2010', a: 70,  b: 50 },
						{ y: '2011', a: 88,  b: 35 },
						{ y: '2012', a: 110, b: 40 }
					],
					xkey: 'y',
					ykeys: ['a', 'b'],
					labels: ['Item A', 'Item B'],
					lineColors: ['#ed4949', '#1e59d9', '#20c05c', '#FED42A', '#FF5722'],
					pointStrokeColors: ['#ed4949', '#1e59d9', '#20c05c', '#FED42A', '#FF5722'],
					pointSize: 0,
					fillOpacity: '0.6',
					resize: true
				});
				break;
			case 'morris-bar':
				Morris.Bar({
					element: el[0],
					data: [
						{ y: '2012', a: 40, b: 50 },
						{ y: '2011', a: 75,  b: 65 },
						{ y: '2010', a: 100,  b: 90 },
						{ y: '2009', a: 75,  b: 65 },
						{ y: '2008', a: 50,  b: 40 },
					],
					xkey: 'y',
					ykeys: ['a', 'b'],
					labels: ['Item A', 'Item B'],
					barColors: ['#20c05c', '#FF5722'],
					stacked: true,
					resize: true
				});
				break;
		}
	}

	$('.chart__chart').each(function() {
		if ($(this).width() > 0) {
			morrisChart($(this));
		}
	});

	$('.chart__tabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		var tab = $($(this).attr('href')),
			chart = tab.find('.chart__chart');
		if (chart.height() == 0) {
			morrisChart(chart);
		}
	})
});
