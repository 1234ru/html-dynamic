$(document).ready(function() {

	var body = $(document.body);

	body.on('change', 'input', function() {
			var input = $(this);
			var a = input.closest('.page-item').find('a');
			var url = a.attr('href');
			var name = encodeURIComponent( input.attr('name') );

			url = url.replace( new RegExp('&' + name + '=[^&]+'), '' );

			if (input.is(':checked') && input.val() != '' ) { // не снятый чекбокс и не пустая радиокнопка
				var append = input.serialize() ; // тут уже закодированное выражение вида name=value
				url += '&' + append;
			}

			a.attr('href', url);
	});

	body.on('click', 'label + button', function () {
		var checkedRadio = $(this).closest('form').find('input[type=radio]:checked');
		checkedRadio.attr('checked', false);
		checkedRadio.change();
		return false;
	});
});