$(document).ready(function() {
	var source = CodeMirror.fromTextArea(
		$('.codemirror').get(0),
		{
			theme: 'hopscotch',
			readOnly: true,
			mode: 'htmlmixed',
			scrollbarStyle: "simple"
		});
});
