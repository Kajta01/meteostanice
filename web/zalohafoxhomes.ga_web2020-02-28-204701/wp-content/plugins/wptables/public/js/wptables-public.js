(function($) {
  'use strict';

  window.wpt = window.wpt || {};
  window.wpt.createTable = function(config) {
    if (!config.width) {
      delete config.width;
    }
    if (!config.height) {
      delete config.height;
    }
    config.controller = {
      loadData: function(filter) {
        filter = filter || {};
        var url = config._ctrl_url.replace(/&amp;/gi, '&')
        return $.ajax({
          type: "GET",
          url: url,
          data: filter
        });
      }
    };
    config.onRefreshed = function() {
      if (typeof $.fancybox == "function") {
        $("#" + config._id_div + " [data-fancybox]").fancybox();
      }
    }
    $("#" + config._id_div).jsGrid(config);
  }
})(jQuery);
