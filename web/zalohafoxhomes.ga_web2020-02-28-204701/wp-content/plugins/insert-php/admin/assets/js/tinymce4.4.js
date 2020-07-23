(function($) {
	$(document).on('tinymce-editor-setup', function(event, editor) {

		if( void 0 === wbcr_inp_shortcode_snippets ) {
			console.log('Unknown error.');
			return;
		}

		if( $.isEmptyObject(wbcr_inp_shortcode_snippets) ) {
			return;
		}

		editor.settings.toolbar1 += ',wbcr_insert_php_button';

		var menu = [];

		$.each(wbcr_inp_shortcode_snippets, function(index, item) {
			menu.push({
				text: item.title,
				value: item.id,
				onclick: function() {
					editor.selection.setContent('[wbcr_php_snippet id="' + item.id + '" title="' + item.title + '"]');
				}
			});
		});

		editor.addButton('wbcr_insert_php_button', {
			title: wbcr_inp_tinymce_snippets_button_title,
			type: 'menubutton',
			icon: 'icon wbcr-inp-shortcode-icon',
			menu: menu
		});

	});
})(jQuery);