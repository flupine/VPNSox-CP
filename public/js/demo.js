/*
 *
 *   Right - Responsive Admin Template
 *   v 0.3.0
 *   http://adminbootstrap.com
 *
 */

$(document).ready(function() {
	$('body').on('click', '.demo__ico', function() {
		$('.demo').toggleClass('demo_open');
	});

	$('body').on('click', '.demo__theme', function() {
		$('.demo__theme').removeClass('demo__theme_active');
		$(this).addClass('demo__theme_active');

		var demoCss = $(document.createElement('link')).addClass('demo__css').attr('rel', 'stylesheet').attr('href', $(this).data('css'));
		$('.demo__css').addClass('old');
		$('head').append(demoCss);
		setTimeout(function() {
			$('.demo__css.old').remove();
		}, 200);
	})
});