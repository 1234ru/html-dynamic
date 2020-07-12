$(document).ready(function() {

	var body = $(document.body);

	body.on('change', 'input', function() {
			var input = $(this);
			var form = input.closest('form');
			var a = input.closest('.page-item').find('a');
			var url = a.attr('href');
			var name = encodeURIComponent( input.attr('name') );

			url = url.replace( new RegExp('&' + name + '=[^&]+', 'g'), '' );

			var append = form.serialize();
			if (append) {
				append = '&' + append;
			}
			url += append;

			a.attr('href', url);
	});

	body.on('click', 'label + button', function () {
		var checkedRadio = $(this).closest('form').find('input:checked');
		checkedRadio.attr('checked', false);
		checkedRadio.change();
		return false;
	});
});