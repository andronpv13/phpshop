
$(document).ready(function() {

    //Запуск генерации кодов
    $("input[name='qty_gen']").on("click", function() {

        $(this).addClass("disabled").parents('.form-group').next().addClass("hide").next().next('.progress').removeClass('hide');

        var qty = $("input[name='qty_new']").val();
        var promo_id = $("input[name='rowID']").val();

        var button = $(this);

        $.ajax({
            url: '/phpshop/modules/promotions/admpanel/ajax/generator.php',
            type: 'post',
            data: 'qty=' + qty + '&promo_id=' + promo_id + '&operation=create',
            dataType: 'json',
            success: function(json) {
                if (json['success']) {
                    button.removeClass("disabled").parents('.form-group').next().removeClass("hide").next().next('.progress').addClass('hide');
                    $("#qty-all").html(json.count_all);
                    $("#qty-active").html(json.count_active);

                }
            }
        });
    });

    //Удаление промо-кодов
    $("button[name='qty_del']").on("click", function(e) {
        e.preventDefault();

        var promo_id = $("input[name='rowID']").val();
        var button = $(this);
        var qty_off_count = $("#qty_off_count").attr("data-count");

        $.ajax({
            url: '/phpshop/modules/promotions/admpanel/ajax/generator.php',
            type: 'post',
            data: 'operation=delete&promo_id=' + promo_id + '&qty_off_count=' + qty_off_count,
            dataType: 'json',
            success: function(json) {

                if (json['success']) {

                    button.addClass("disabled").hide().parents('.form-group').next().next().next().next().removeClass("hide");
                    $("#qty-all").html(json.count_all);
                    $("#qty-active").html(json.count_active);

                }
            }
        });
    });


    //Выгрузка промо-кодов
    $("button[name='download_codes']").on("click", function(e) {
        e.preventDefault();

        var promo_id = $("input[name='rowID']").val();
        $.ajax({
            url: '/phpshop/modules/promotions/admpanel/ajax/generator.php',
            type: 'post',
            data: 'operation=download&promo_id=' + promo_id,
            dataType: 'json',
            success: function(json) {
                if (json['success']) {
                    window.location.href = './csv/' + json.file;
                }
            }
        });
    });
});

$(document).ready(function() {

    code_check = $('#code_check_new').prop('checked');
    if (code_check == false) {
        $("#delivery_method_check_new").addClass("readonly");
        $("#delivery_method_new").addClass("readonly");
        $("#code_tip_new").addClass("readonly");
        //$("#active_check_new").addClass("readonly");
        //$("#active_date_ot_new").addClass("readonly");
        //$("#active_date_do_new").addClass("readonly");
        $("#sum_order_check_new").addClass("readonly");
        $("#sum_order_new").addClass("readonly");

        //Сообщение при заблокрированном чекбоксе
        //$(':checkbox[readonly=readonly]').click(function(){
        //      alert('Данную галочку невозможно установить пока не введен «Код купона» на вкладке - «Условия»');
        //      return false;
        //  });
    }

    $("#code_check_new").on("click", function() {
        code_check = $('#code_check_new').prop('checked');
        if (code_check == false) {
            $("#delivery_method_check_new").addClass("readonly");
            $("#delivery_method_new").addClass("readonly");
            $("#code_tip_new").addClass("readonly");
            //$("#active_check_new").addClass("readonly");
            //$("#active_date_ot_new").addClass("readonly");
            //$("#active_date_do_new").addClass("readonly");
            $("#sum_order_check_new").addClass("readonly");
            $("#sum_order_new").addClass("readonly");
        }
        else {
            $("#delivery_method_check_new").removeClass("readonly");
            $("#delivery_method_new").removeClass("readonly");
            $("#code_tip_new").removeClass("readonly");
            //$("#active_check_new").removeClass("readonly");
            //$("#active_date_ot_new").removeClass("readonly");
            //$("#active_date_do_new").removeClass("readonly");
            $("#sum_order_check_new").removeClass("readonly");
            $("#sum_order_new").removeClass("readonly");

            //Сообщение при заблокрированном чекбоксе
            //$(':checkbox[readonly=readonly]').click(function(){
            //alert('Данную галочку невозможно установить пока не введен «Код купона» на вкладке - «Условия»');
            //return false;
            //});
        }
    });

    //Сообщение при заблокрированном чекбоксе
    $(':checkbox').click(function() {
        if ($(this).attr("class") == 'readonly') {
            alert('Данную галочку невозможно установить пока не введен «Код купона» на вкладке - «Условия»');
            return false;
        }
    });

    $('#selectalloption').click(function() {
        option_check = $('#selectalloption').prop('checked');
        if (option_check == true) {
            $('#categories option').prop('selected', true);
        }
        else {
            $('#categories option').prop('selected', false);
        }
    });
});

// random big/small letters
function randAa(n) {  
    var s = '';
    while (s.length < n)
        s += String.fromCharCode(Math.random() * 127).replace(/\W|\d|_/g, '');
    $('input[name=code_new]').val(s);
}