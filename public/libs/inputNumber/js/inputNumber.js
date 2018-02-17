(function ($) {

	$.fn.inputNumber = function (options) {

		var inBox = '.input-number-box',
			newInput = '.input-number',
			moreVal = '.input-number-more',
			lessVal = '.input-number-less';

		this.each(function () {

			var el = $(this);
			$('<div class="' + inBox.substr(1) + '"></div>').insertAfter(el);
			var parent = el.find('+ ' + inBox);
			parent.append(el);
			var classes = el.attr('class');
			parent.append('<input class="' + newInput.substr(1) + '" type="text">');
			el.hide();
			var newEl = el.next();
			newEl.addClass(classes);
			var attrValue;

			function setInputAttr(attrName) {
				if (el.attr(attrName)) {
					attrValue = el.attr(attrName);
					newEl.attr(attrName, attrValue);
				}
			}

			setInputAttr('value');
			setInputAttr('placeholder');
			setInputAttr('min');
			setInputAttr('max');
			setInputAttr('step');

			parent.append('<div class=' + moreVal.substr(1) + '></div>');
			parent.append('<div class=' + lessVal.substr(1) + '></div>');

		}); //end each

		var value,
			step;

		var interval = null,
			timeout = null;

		function ToggleValue(input) {
			input.val(parseInt(input.val(), 10) + d);
			console.log(input);
		}

		$('body').on('mousedown', moreVal, function () {
			var el = $(this);
			var input = el.siblings(newInput);
			moreValFn(input);
			timeout = setTimeout(function(){
				interval = setInterval(function(){ moreValFn(input); }, 50);
			}, 200);

		});

		$('body').on('mousedown', lessVal, function () {
			var el = $(this);
			var input = el.siblings(newInput);
			lessValFn(input);
			timeout = setTimeout(function(){
				interval = setInterval(function(){ lessValFn(input); }, 50);
			}, 200);
		});

		$(moreVal +', '+ lessVal).on("mouseup mouseout", function() {
			clearTimeout(timeout);
			clearInterval(interval);
		});

		function moreValFn(input){
			var max = input.attr('max');
			checkInputAttr(input);
			var newValue = value + step;
			if (newValue > max) {
				newValue = max;
			}
			changeInputsVal(input, newValue);
		}

		function lessValFn(input){
			var min = input.attr('min');
			checkInputAttr(input);
			var newValue = value - step;
			if (newValue < min) {
				newValue = min;
			}
			changeInputsVal(input, newValue);
		}

		function changeInputsVal(input, newValue){
			input.val(newValue);
			var inputNumber = input.siblings(this);
			inputNumber.val(newValue);
		}

		function checkInputAttr(input) {
			if (input.val()) {
				value = parseFloat(input.val());
			} else if (input.attr('placeholder')) {
				value = parseFloat(input.attr('placeholder'));
			}
			if (!( $.isNumeric(value) )) {
				value = 0;
			}
			if (input.attr('step')) {
				step = parseFloat(input.attr('step'));
			} else {
				step = 1;
			}
		}

		$(newInput).change(function () {
			var input = $(this);
			var value = parseFloat(input.val());
			var min = input.attr('min');
			var max = input.attr('max');
			if (value < min) {
				value = min;
			} else if (value > max) {
				value = max;
			}
			if (!( $.isNumeric(value) )) {
				value = '';
			}
			input.val(value);
			input.siblings(this).val(value);
		});

		$(newInput).keydown(function(e){
			var input = $(this);
			var k = e.keyCode;
			if( k == 38 ){
				moreValFn(input);
			}else if( k == 40){
				lessValFn(input);
			}
		});
	};
})(jQuery);