(function($) {
    'use strict';

    window.wpt_admin = {};
    window.wpt_admin.view = {};

    window.wpt_admin.selectText = function(node) {
        if (document.selection) {
            var range = document.body.createTextRange();
            range.moveToElementText(node);
            range.select();
        } else if (window.getSelection) {
            var range = document.createRange();
            range.selectNodeContents(node);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
        }
    }

    // List tables
    window.wpt_admin.view.list_tables = function() {
        $(".wpt-delete").click(onDeleteClick);
        $(".wpt-shortcode").click(onShortcodeClick);
        $(".wpt-subscribe-btn").click(onSubscribeClick);
        
        function onSubscribeClick() {
            
        }

        function onDeleteClick(e) {
            if (!confirm(wpt_consts.confirm_delete_table)) {
                e.preventDefault();
            }
        }

        function onShortcodeClick() {
            wpt_admin.selectText(this);
        }
    }

    // Add new table
    window.wpt_admin.view.add_new = function(viewData) {
        $(".wpt-ds-item").click(onItemClick);
        $("#wpt-ds-box-csv").wptTabs({ change: onTypeChange });
        $("#wpt-ds-box-csv #wpt-drop-zone").wptFileDrop({ 
            change: onDropFileChange,
            error: onDropFileError
        });

        $("#wpt-ds-box-json").wptTabs({ change: onTypeChange });
        $("#wpt-ds-box-json #wpt-drop-zone").wptFileDrop({ 
            change: onDropFileChange,
            error: onDropFileError
        });

        $("#wpt-ds-box-mysql").wptTabs({ change: onTypeChange });
        $("#db-name").change(onDatabaseChange);
        onDatabaseChange();
        
        function onDatabaseChange() {
            $("#db-table").empty();
            $("#db-table").prop("disabled", false);
            var tables = viewData.dbs[$("#db-name").val()];
            if (tables) {
                tables.forEach(function(table) {
                    $("#db-table").append($("<option>").val(table).text(table));
                });
            } 
        }

        function onItemClick() {
            var format = $(this).attr('data-value');
            $(".wpt-ds-item").toggleClass('wpt-active', false)
            $(this).toggleClass('wpt-active');
            $('#wpt-format').val($(".wpt-ds-item.wpt-active").attr('data-value'));
            $('#wpt-ds-box .wpt-ds-box-content').hide();
            $('#wpt-ds-box #wpt-ds-box-' + format).show();
            $('#wpt-ds-box').show();
        }

        function onTypeChange(activeTab) {
            activeTab.closest('.wpt-ds-box-content')
                .find("#input-type")
                .val(activeTab.attr('data-type'));
        }

        function onDropFileChange(file) {
            $("#wpt-error-file-wrong-type", this).hide();
            $("#wpt-filename", this).text(file ? file.name : "");
        }

        function onDropFileError(msg) {
            $("#wpt-error-file-wrong-type", this).show();
        }
    }

    // Edit table
    window.wpt_admin.view.edit_table = function(viewData) {
        $(".wpt-shortcode").click(onShortcodeClick);
        $("#wpt-fields tbody").sortable({ handle: ".wpt-drag-handle" });
        $("#wpt-fields #wpt-edit-field-btn").click(onEditFieldClick);
        $("#wpt-fields #wpt-title-input").on("input", onFieldTitleChnage);
        $("#wpt-fields .wpt-col-check input").change(onFieldVisibilityChnage);
        $("#wpt-edit-field-dialog #wpt-apply-btn").click(onEditFieldApply);
        $("#wpt-edit-field-dialog #wpt-remove-field-btn").click(onRemoveFieldClick);
        $("#wpt-edit-field-dialog #wpt-format-input").on("input", updatePreview);
        $("#wpt-edit-field-dialog #wpt-currency-input").on("input", updatePreview);
        $('#wpt-edit-field-dialog #wpt-type-select').change(onFieldTypeChange);
        $('#wpt-options input[name="config[paging]"]').change(onPagingChange);
        $('#wpt-options input, #wpt-options select').on("change", onOptionsChange);
        $('#wpt-options input, #wpt-options select').on("input", onOptionsChange);
        $("#wpt-submit-btn").click(submitForm);
        $("#wpt-update-query-btn").click(submitForm);
        $("#wpt-preview-btn").click(onPreviewClick);
        $("#wpt-data #wpt-add-rows-btn").click(onAddRowsClick);
        
        var editFieldDialog = $("#wpt-edit-field-dialog").wptDialog();
        var previewDialog = $("#wpt-preview-dialog").wptDialog();
        var dataSheet = null;
        var isDataChanged = false;
        var removeFieldUrl = $("#wpt-edit-field-dialog #wpt-remove-field-btn").attr('href');
        var isFormSubmit = false;
        var isFieldsChanged = false;
        var isOptionsChnaged = false;

        $(window).bind('beforeunload', function() {
            if (isFormSubmit || !isDirty()) {
                return undefined;
            }
            return 'Are you sure you want to leave?';
        });
        
        function isDirty() {
            return isDataChanged || isFieldsChanged || isOptionsChnaged;
        }

        // do not create for sql
        if ($("#wpt-data-sheet").length > 0) {
            $.ajax({
                type: "GET",
                url: viewData.load_data_url.replace(/&amp;/gi, '&'),
                success: function(data) {
                    dataSheet = createDataSheet($("#wpt-data-sheet").get(0), data);
                }
            });
        }

        function onFieldTitleChnage() {
            isFieldsChanged = true;
            if (dataSheet) {
                var name = $(this).closest("tr").attr("data-field");
                var title = $(this).val();
                viewData.fields.forEach(function(item) {
                    if (item.name == name) {
                        item.title = title;
                    }
                });
                dataSheet.updateSettings({
                    colHeaders: getColHeaders(viewData.fields, dataSheet.getSourceData())
                });
            }
        }

        function onFieldVisibilityChnage() {
            isFieldsChanged = true;
        }

        function onOptionsChange() {
            isOptionsChnaged = true;
        }

        function onAddRowsClick() {
            var amount = $("#wpt-add-rows-inp").val();
            if (!isNaN(amount) && amount > 0) {
                dataSheet.alter('insert_row', dataSheet.countRows(), amount);
                dataSheet.render();
            } 
        }

        function onRemoveFieldClick(e) {
            var isOk = confirm(wpt_consts.confirm_delete_table_field);
            if (!isOk) {
                e.preventDefault();
            }
        }

        function onPreviewClick() {
            var fields = [];
            $("#wpt-fields tbody tr").each(function() {
                var field = $(this).attr('data-field');
                if (getFieldValue(field, 'visible', 'checked')) {
                    fields.push({
                        name: field,
                        title: getFieldValue(field, 'title'),
                        type: getFieldValue(field, 'type'),
                        format: getFieldValue(field, 'format'),
                        align: getFieldValue(field, 'align'),
                        width: getFieldValue(field, 'width'),
                        css: getFieldValue(field, 'css'),
                        currency_symbol: getFieldValue(field, 'currency_symbol')
                    });
                }
            });

            $("#wpt-preview").attr("class", 'jsgrid ' + $("select[name='config[theme]']").val());
            $("#wpt-preview").parent().width(window.innerWidth - 200);
            $("#wpt-preview").parent().height(window.innerHeight - 200);
            var grid = $("#wpt-preview").jsGrid({
                controller: {
                    loadData: function() {
                        if (dataSheet) {
                            return dataSheet.getSourceData();
                        }
                        return $.ajax({
                            type: "GET",
                            url: viewData.load_data_url.replace(/&amp;/gi, '&')
                        });
                    }
                },
                width: "100%",
                autoload: true,
                sorting: $("input[name='config[sorting]']").prop('checked'),
                selecting: $("input[name='config[selecting]']").prop('checked'),
                heading: $("input[name='config[heading]']").prop('checked'),
                paging: $("input[name='config[paging]']").prop('checked'),
                pageSize: $("input[name='config[pageSize]']").val(),
                fields: fields
            });
            previewDialog.open();
        }

        var fieldsIndex = {};
        viewData.fields.forEach(function (item) {
            fieldsIndex[item.name] = item;
        });

        var alignClassIndex = {
            "left": "htLeft",
            'center': 'htCenter',
            'right': 'htRight'
        }

        function createDataSheet(container, data) {
            if (data.length == 0) {
                data.push(createEmptyData(viewData.fields));
            }
            var hot = new Handsontable(container, {
                data: data,
                rowHeaders: true,
                colHeaders: getColHeaders(viewData.fields),
                columns: getColumnsMeta(viewData.fields),
                allowInsertColumn: false,
                allowRemoveColumn: false,
                manualRowMove: true,
                stretchH: 'all',
                cells: function (row, col, prop) {
                    if (!fieldsIndex[prop]) {
                        return null;
                    }
                    return { className: alignClassIndex[fieldsIndex[prop].align] };
                },
                contextMenu: {
                    callback: function (key, options) {
                        if (key == 'alignment:center' || key == 'alignment:left' || key == 'alignment:right') {
                            var meta = hot.getCellMeta(options.start.row, options.start.col);
                            var align = key.replace('alignment:', '');
                            fieldsIndex[meta.prop].align = align;
                            setFieldValue(meta.prop, 'align', align);
                            hot.render();
                        }
                    },
                    items: {
                        "row_above": {},
                        "row_below": {},
                        "hsep1": "---------",
                        "remove_row": {},
                        "hsep2": "---------",
                        "alignment": {
                            "submenu": {
                                "items": [
                                    { key: "alignment:left", name: "Left" },
                                    { key: "alignment:center", name: "Center" },
                                    { key: "alignment:right", name: "Right" }
                                ]
                            }
                        },
                        "hsep3": "---------",
                        "undo": {},
                        "redo": {},
                        "hsep4": "---------",
                        "copy": {},
                        "cut": {}
                    }
                },
                afterChange: function(changes, source) {
                    if (source != "loadData") {
                        isDataChanged = true;
                    }
                },
                afterRemoveRow: function () {
                    isDataChanged = true;
                },
                afterCreateRow: function () {
                    isDataChanged = true;
                },
                afterRender: function(isForced) {
                    if (isForced) {
                        var height = $(".wtHider").height();
                        $("#wpt-data-sheet").height(Math.max(200, Math.min(height, 500)));
                    }
                }
            });
            return hot;
        }

        function createEmptyData(fields) {
            var data = {};
            fields && fields.forEach(function(item) {
                data[item.name] = "";
            }); 
            return data; 
        }

        function getColHeaders(fields) {
            return fields.map(function(item) {
                return item.title;
            });
        }

        function getColumnsMeta(fields) {
            return fields.map(function(item) {
                return { data: item.name };
            });
        }

        function onPagingChange() {
            var checked = $(this).prop('checked');
            $('#wpt-options input[name="config[pageSize]"]').prop('disabled', !checked);
        }

        function submitForm() {
            if (isDataChanged) {
                $("#wpt-form input[name=data]").val(JSON.stringify(dataSheet.getSourceData()));
            }
            isFormSubmit = true;
            $("#wpt-form").submit();
        }

        function onShortcodeClick() {
            wpt_admin.selectText(this);
        }

        function onEditFieldClick() {
            var tr = $(this).closest("tr");
            var field = tr.attr('data-field');
            var dialog = $("#wpt-edit-field-dialog");
            dialog.attr("data-field", field);
            $("#wpt-field-name", dialog).text($("#wpt-title-input", tr).val());
            $("#wpt-format-input", dialog).val(getFieldValue(field, 'format'));
            $("#wpt-currency-input", dialog).val(getFieldValue(field, 'currency_symbol'));
            $("#wpt-type-select", dialog).val(getFieldValue(field, 'type')).change();
            $("#wpt-align-select", dialog).val(getFieldValue(field, 'align'));
            $("#wpt-width-input", dialog).val(getFieldValue(field, 'width'));
            $("#wpt-class-input", dialog).val(getFieldValue(field, 'css'));
            if (removeFieldUrl) {
                $("#wpt-edit-field-dialog #wpt-remove-field-btn").attr('href', removeFieldUrl.replace("__field__", field));
            }
            editFieldDialog.open();
        }

        function getFieldValue(field, prop, jqProp) {
            var item = $("input[name='fields["+field+"]["+prop+"]']");
            return jqProp ? item.prop(jqProp) : item.val();
        }

        function setFieldValue(field, prop, value) {
            return $("input[name='fields["+field+"]["+prop+"]']").val(value);
        }

        function onFieldTypeChange() {
            var type = $(this).val();
            if (type == 'number') {
                var dialog = $("#wpt-edit-field-dialog");
                var field = dialog.attr('data-field');
                if (getFieldValue(field, 'format') == "") {
                    $("#wpt-format-input", dialog).val("0,0.00");
                }
                updatePreview();
                $("#wpt-edit-field-dialog #wpt-format-row").show();
            } else {
                $("#wpt-edit-field-dialog #wpt-format-row").hide();
            }
        }

        function updatePreview() {
            var format = $("#wpt-edit-field-dialog #wpt-format-input").val();
            var output = numeral(1000).format(format);
            var currencySymbol = $("#wpt-edit-field-dialog #wpt-currency-input").val();
            if (currencySymbol && currencySymbol.length > 0) {
                output = output.replace(/\$/g, currencySymbol);
            }
            $("#wpt-format-preview").text(output);
        }

        function onEditFieldApply() {
            var dialog = $("#wpt-edit-field-dialog");
            var field = dialog.attr('data-field');
            setFieldValue(field, 'type', $("#wpt-type-select", dialog).val());
            setFieldValue(field, 'format', $("#wpt-format-input", dialog).val());
            setFieldValue(field, 'currency_symbol', $("#wpt-currency-input", dialog).val());
            setFieldValue(field, 'align', $("#wpt-align-select", dialog).val());
            setFieldValue(field, 'width', $("#wpt-width-input", dialog).val());
            setFieldValue(field, 'css', $("#wpt-class-input", dialog).val());
            editFieldDialog.close();
            submitForm();
        }
    }

    // Dialog
    $.fn.wptDialog = function(config) {
        var _this = this;
        var jq = $(_this);
        jq.hide();
        jq.addClass('wpt-dialog');
        $(document.body).append(jq);
        var overlay = $('<div/>').addClass('wpt-dialog-overlay');

        var public = {};
        public.open = function() {
            jq.show();
            jq.css('top', Math.max(0, (($(window).height() - jq.height()) / 2)) + "px");
            jq.css('left', Math.max(0, (($(window).width() - jq.width()) / 2)) + "px");
            $(document.body).append(overlay);
        }
        public.close = function() {
            jq.hide();
            overlay.detach();
        }

        overlay.click(function() {
            public.close();
        });
        $(".wpt-close-btn", jq).click(function() {
            public.close();
        });
        return public;
    };

    // Tabs
    $.fn.wptTabs = function(config) {
        var _this = this;
        var jq = $(_this);
        config = config || {};

        $(".nav-tab", jq).click(onTabClick);
        onTabChange();

        function onTabClick(e) {
            e.preventDefault();
            $(".nav-tab", jq).removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            onTabChange();
        }

        function onTabChange() {
            var id = $(".nav-tab-active", jq).attr('href');
            $('.wpt-tab-content', jq).hide();
            $(id, jq).show();
            if (config.change != null) {
                config.change.call(this, $(".nav-tab-active", jq));
            }
        }
    }

    // File Drop
    $.fn.wptFileDrop = function(config) {
        config = config || {};
        var _this = this;
        var jq = $(_this);
        jq.css('position', 'relative');
        var inputFile = jq.find("input[type=file]");
        inputFile.css('position', 'absolute')
            .css('left', 0)
            .css('top', 0)
            .css('opacity', 0);
        
        inputFile.change(function(e) {
            if (config.change) {
                var file = inputFile[0].files && inputFile[0].files.length > 0 ? inputFile[0].files[0] : null;
                config.change.call(jq, file);
            }
        });

        jq.on("dragover", function(e) {
            e.preventDefault();
            e.stopPropagation();
            jq.toggleClass('wpt-drop', true);
            var ooleft = jq.offset().left;
            var ooright = jq.outerWidth() + ooleft;
            var ootop = jq.offset().top;
            var oobottom = jq.outerHeight() + ootop;
            var x = e.pageX;
            var y = e.pageY;
            if (!(x < ooleft || x > ooright || y < ootop || y > oobottom)) {
                inputFile.offset({ top: y - 15, left: x - 100 });
            } else {
                inputFile.offset({ top: -400, left: -400 });
            }
        });

        jq.on("dragleave", function() {
            jq.toggleClass('wpt-drop', false);
        });

        jq.on("drop", function(e) {
            jq.toggleClass('wpt-drop', false);
            var ev = e.originalEvent;
            var dt = ev.dataTransfer;
            var file = null;
            if (dt.items && dt.items.length > 0) {
                file = dt.items[0].getAsFile();
            } else if (dt.files && dt.files.length > 0) {
                file = dt.files[0];
            }
            if (file && file.type != inputFile.attr('accept')) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                if (config.error) {
                    config.error.call(jq, 'Only files with type "' + inputFile.attr('accept') + '" is allowed.')
                }
            }
        })

        jq.find('.wpt-filedrop-btn').click(function() {
            inputFile.click();
        });
    }
})(jQuery);
