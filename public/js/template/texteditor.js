$(document).ready(function() {
	$('.summernote').summernote(
		{
			height: '300',
			codemirror: {
				theme: 'hopscotch',
				mode: 'htmlmixed',
				lineWrapping: true,
				scrollbarStyle: "simple"
			}
		}
	);
});
