jQuery(function($) {
    $.fn.dataTableExt.oSort['num-html-asc'] = function(a, b) {
        var x = a.replace(/<.*?>/g, "");
        var y = b.replace(/<.*?>/g, "");
        x = parseFloat(x);
        y = parseFloat(y);
        return ((x < y || isNaN(y)) ? -1 : ((x > y || isNaN(x)) ? 1 : 0));
    };

    $.fn.dataTableExt.oSort['num-html-desc'] = function(a, b) {
        var x = a.replace(/<.*?>/g, "");
        var y = b.replace(/<.*?>/g, "");
        x = parseFloat(x);
        y = parseFloat(y);
        return ((x < y || isNaN(x)) ? 1 : ((x > y || isNaN(y)) ? -1 : 0));
    };

    $.fn.dataTableExt.oApi.fnSetFilteringDelay = function(oSettings, iDelay) {
        var _that = this;

        if (iDelay === undefined) {
            iDelay = 250;
        }

        this.each(function(i) {
            $.fn.dataTableExt.iApiIndex = i;
            var
                    $this = this,
                    oTimerId = null,
                    sPreviousSearch = null,
                    anControl = $('input', _that.fnSettings().aanFeatures.f);

//            anControl.unbind('keyup search input').bind('keyup', function() {
            anControl.off('keyup search input').on('keyup', function() {
                var $$this = $this;

                if (sPreviousSearch === null || sPreviousSearch != anControl.val()) {
                    window.clearTimeout(oTimerId);
                    sPreviousSearch = anControl.val();
                    oTimerId = window.setTimeout(function() {
                        $.fn.dataTableExt.iApiIndex = i;
                        _that.fnFilter(anControl.val());
                    }, iDelay);
                }
            });

            return this;
        });
        return this;
    };

    var oTable;
    var gaiSelected = [];

    $dataTable = $("#datatable");
    var route = $dataTable.data('entity-list-route');
    var tasksProjectId = $dataTable.data('project-id');
    var accountProfileId = $dataTable.data('profile-id');
    var classname = $dataTable.data('classname');

    $("#search_filter").keyup(function() {
        $dataTable.fnFilter(this.value);
    });

    if ($dataTable.length) {
        oTable = $dataTable.dataTable({
            "aoColumns": autodetectColumns($dataTable),
            "sDom": '<"row"<"col-md-12"t>>T<"row"<"col-md-12"<"row"<"col-md-3"i><"col-md-4"l>rp>>>',
            "sPaginationType": "bootstrap",
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": route,
            "aaSorting": [[0, "desc"]],
            "iDisplayLength": 10,
            "oTableTools": {
                "sSwfPath": "/tabletools/swf/copy_csv_xls_pdf.swf"
            },
            "fnInitComplete": function(oSettings) {
                $('#copy_buttons').append($('div.DTTT_container')[0]);
                this.fnSetFilteringDelay(800);
            },
            "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                if (jQuery.inArray(aData[0], gaiSelected) != -1)
                {
                    $(nRow).addClass('row_selected');
                }
                return nRow;
            },
            "fnServerParams": function(aoData) {
                if (tasksProjectId) {
                    aoData.push({"name": "projectId", "value": tasksProjectId});
                }
                if (accountProfileId) {
                    aoData.push({"name": "profileId", "value": accountProfileId});
                }
                if (classname) {
                    aoData.push({"name": "classname", "value": classname});
                }
            },
            "fnDrawCallback"
                    : function(oSettings) {
                        $('table#datatable tbody tr').click(function(e) {
                            var $row = $(this),
                                    rowId = $row.attr('id');


                            if ($row.hasClass('row_selected')) {
                                $row.removeClass('row_selected');
                                $('li[class*="crud"]').each(function() {
                                    $(this).addClass('disabled');
                                    $(this).children('a').removeAttr('href');
                                })

                            }
                            else {
                                oTable.$('tr.row_selected').removeClass('row_selected');
                                $(this).addClass('row_selected');

                                jQuery('li[class*="crud"]').each(function() {
                                    var $action = $(this),
                                            action = $action.attr('id'),
                                            href = null;

                                    if (typeof ($row.data(action)) !== 'undefined') {
                                        href = $row.data(action);
                                    } else {
                                        href = document.URL + action + '/' + rowId;
                                    }

                                    $(this).removeClass('disabled');
                                    $(this).children('a').attr('href', href);
                                })
                            }
                        });
                    }
        });

        $('#search_filter').keypress(function() {
            if ($(this).val().length < 3)
                return;
            oTable.fnFilter($(this).val());
        });

        $('#delete.crud a').click(function(event) {
            event.preventDefault();

            var buttons = {};
            buttons[Translator.trans('confirmation.buttons.confirm', {}, 'Backend')] = function() {
                var url = $trigger.attr('href');
                var $dialog = $(this);

                if ($trigger.hasClass('ajax')) {
                    $.ajax({
                        url: url,
                        success: function(response) {
                            $dialog.dialog('close');

                            $trigger.trigger('success', [response]);
                        }
                    });
                }
                else {
                    document.location.href = url;
                }
            };
            buttons[Translator.trans('confirmation.buttons.cancel', {}, 'Backend')] = function() {
                $(this).dialog('close');
            };

            var $trigger = $(this),
                    $row = $("#datatable .row_selected");
            $('<div>' + Translator.trans('confirmation.delete.body', {'name': $row.find("td:eq(1)").text() + ' (Id ' + $row.find("td:eq(0)").text() + ')'}, 'Backend') + '</div>').dialog({
                title: Translator.trans('confirmation.delete.title', {}, 'Backend'),
                modal: true,
                resize: false,
                buttons: buttons
            });
        });
    }

    function autodetectColumns($dataTable) {
        var aoColumns = [{"sType": "num-html"}];
        var columnsCount = $('thead th', $dataTable).length;
        for (var i = 1; i < columnsCount; i++) {
            aoColumns.push(null);
        }
        return aoColumns;
    }
});

function fnGetSelected(oTableLocal) {
    return oTableLocal.$('tr.row_selected');
}