jQuery(function($) {
	$('#export').click(function(e) {
        e.preventDefault();
        jQuery( "#dialog" ).dialog({
            height: 500,
            width: 500,
            modal: true,
            buttons: {
                "Exportar": function() {
                    var numColumns = jQuery('#form-export input:checkbox:checked').length;
                    if (numColumns == 0) {
                        alert(minNumColumns);
                        return;
                    }
                    var filename = jQuery('input[name=filename]').val();
                    if (!jQuery.trim(filename)) {
                        alert(selectFileName);
                        return;
                    }
                    jQuery(this).dialog("close");
                    jQuery('#form-export').submit();
                }
            }
        });
    });
    $('#export-all').click(function(e) {
        e.preventDefault();
        jQuery('#form-export input[type=checkbox]').prop('checked', true);
    });
    $('#export-none').click(function(e) {
        e.preventDefault();
        jQuery('#form-export input[type=checkbox]').prop('checked', false);
    });

    $('#filters_centers').change(function(e) {
        var centroId = $(this).val();
        updateFilters(centroId);
    });

    updateFilters($('#filters_centers').val());
});

function updateFilters(centroId)
{
    if (centroId) {
        $("#filters_grupo_id > option").each(function () {
            if ($(this).attr('value') != '') {
                if (centroId == $(this).attr('data-centro')) {
                    $(this).toggle(true);
                }
                else {
                    $(this).toggle(false);
                }
            }
        });

        $("#filters_profesor_id > option").each(function () {
            if ($(this).attr('value') != '') {
                if (centroId == $(this).attr('data-centro')) {
                    $(this).toggle(true);
                }
                else {
                    $(this).toggle(false);
                }
            }
        });

        $("#filters_week > option").each(function () {
            if ($(this).attr('value') != '') {
                if (centroId == $(this).attr('data-centro')) {
                    $(this).toggle(true);
                }
                else {
                    $(this).toggle(false);
                }
            }
        });
    }
    else {
        $("#filters_grupo_id > option").toggle(true);
        $("#filters_profesor_id > option").toggle(true);
    }
}
