$(document).ready(function() {
	$('.mailbox-compose .summernote').summernote(
		{
			height: '300',
			toolbar: [
				['style', ['bold', 'italic', 'underline', 'clear']],
				['font', ['strikethrough', 'superscript', 'subscript']],
				['fontsize', ['fontsize']],
				['para', ['ul', 'ol', 'paragraph']],
				['height', ['height']]
			],
			focus: true
		}
	);

	$('.mailbox-reply .summernote').summernote(
		{
			height: '100',
			toolbar: [
				['style', ['bold', 'italic', 'underline', 'clear']],
				['font', ['strikethrough', 'superscript', 'subscript']],
				['fontsize', ['fontsize']],
				['para', ['ul', 'ol', 'paragraph']],
				['height', ['height']]
			]
		}
	);

	$('.selectize_to').selectize({
		plugins: ['remove_button'],
		options: [
			{value: 'payne@indie.com', text: 'Julie Payne'},
			{value: 'watsonq@webs.com', text: 'Stephen Olson'},
			{value: 'stewart@wufoo.com', text: 'Jean Romero'},
			{value: 'rfish@gmail.com', text: 'Ronald Fisher'},
			{value: 'encer1p@paypal.com', text: 'Marie Pierce'}
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
