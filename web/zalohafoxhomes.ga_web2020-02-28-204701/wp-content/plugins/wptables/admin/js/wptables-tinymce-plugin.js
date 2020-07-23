(function($) {
  'use strict';
  window.wpt_tinymce = {};
  window.wpt_tinymce.showTables = function(editor, url) {
    $.get(ajaxurl, { action: "wpt_tinymce_get_tables" }).success(
      function(tables) {
        showPopup(JSON.parse(tables));
      }
    );

    function showPopup(tables) {
      editor.windowManager.open({
        title: 'Insert Table',
        body: [{
          type: 'listbox',
          name: 'table',
          label: 'Choose',
          values: tables,

        }, {
          type: 'container',
          name: 'title',
          label: 'Or',
          html: "<a href='" + window.wpt_consts.url_add_new_table + "' target='_blank'>" + "Add New Table" + "</a>"
        }],
        width: 500,
        height: 100,
        onsubmit: function(e) {
          editor.insertContent(e.data.table);
        }
      });
    }

  };

  if ('undefined' !== typeof(tinymce)) {
    tinymce.create('tinymce.plugins.WPTablesPlugin', {
      init: function(editor, url) {
        editor.addButton('WPTables_insert_table', {
          title: 'Insert Table',
          icon: 'icon wpt-icon wpt-icon-table2',
          onclick: function() {
            window.wpt_tinymce.showTables(editor, url);
          }
        });
      }
    });
    tinymce.PluginManager.add('wptables_tinymce', tinymce.plugins.WPTablesPlugin);
  }

})(jQuery);
