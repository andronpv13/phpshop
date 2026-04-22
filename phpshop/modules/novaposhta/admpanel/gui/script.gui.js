function novaposhtaValidate(evt) {
    var theEvent = evt || window.event;
    var key = theEvent.keyCode || theEvent.which;
    key = String.fromCharCode( key );
    var regex = /[0-9]|\./;
    if( !regex.test(key) ) {
        theEvent.returnValue = false;
        if(theEvent.preventDefault) theEvent.preventDefault();
    }
}

function novaPoshtaGetPVZ() {
    $("#novaposhtaModal").modal("toggle");

    npGetPvz();
}

function npGetPvz()
{
    $.ajax({
        mimeType: 'text/html; charset='+locale.charset,
        url: '/phpshop/modules/novaposhta/ajax/admin.php',
        type: 'post',
        data: {
            operation: 'getPvz',
            city: $('select[name="np-pvz-city"]').val()
        },
        dataType: "json",
        async: false,
        success: function(json) {
            var $pvz = $('select[name="np-pvz"]');
            $pvz.empty();
            $pvz.append($("<option></option>").attr("value", 0).text('Не выбрано'));
            $.each(json['pvz'], function(key,value) {
                $pvz.append($("<option></option>").attr("value", value['ref']).text(value['title']));
            });
            $pvz.selectpicker('refresh');
        }
    });
}

$(document).ready(function () {
    $('body').on('change', '#np-pvz-city', function () {
        npGetPvz();
    });
    $('body').on('change', '#np-pvz', function () {
        $('input[name="pvz_ref_new"]').val($('select[name="np-pvz"]').val());
    });
});