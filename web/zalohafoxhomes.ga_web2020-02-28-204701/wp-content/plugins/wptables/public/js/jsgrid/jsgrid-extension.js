(function() {
    'use strict';

    function FloatNumberField(config) {
        jsGrid.NumberField.call(this, config);
    }

    FloatNumberField.prototype = new jsGrid.NumberField({
        itemTemplate: function(value) {
            var output = numeral(value).format(this.format || '0,0.00');
            if (this.currency_symbol && this.currency_symbol.length > 0) {
                output = output.replace(/\$/g, this.currency_symbol);
            }
            return output;
        }
    });

    function LinkField(config) {
        jsGrid.TextField.call(this, config);
    }

    LinkField.prototype = new jsGrid.TextField({
        itemTemplate: function(value) {
            var text, href;
            var reg = new RegExp(/(^\[.*\])(\(.*\)$)/);
            if (reg.test(value)) {
                var match = reg.exec(value);
                text = match[1];
                text = text.substring(1, text.length - 1);
                href = match[2];
                href = href.substring(1, href.length - 1);
            } else {
                text = href = value;
            }
            return "<a href='" + href + "'>" + text + "</a>";
        }
    });

    jsGrid.fields.number = FloatNumberField;
    jsGrid.fields.link = LinkField;
})();
