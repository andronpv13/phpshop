
function UpdatePromotion(promo) {

    var sum = $("#OrderSumma").val();
    var ssum = $("#SkiSumma").attr('data-discount');
    var promocode = $("#promocode").val();
    var tipoplcheck = $("#order_metod:checked").val();
    var dostavka = $("#DosSumma").html();
    var wsum = $("#WeightSumma").html();

    if (typeof promocode == "undefined" || promocode.length === 0) {
        promocodei = promo;
    } else {
        promocodei = promocode;
    }

    $("#promotion_load").show();

    $.ajax({
        url: '/phpshop/modules/promotions/ajax/promotions.php',
        type: 'post',
        data: 'promocode=' + promocodei + '&sum=' + sum + '&type=json&ssum=' + ssum + '&tipoplcheck=' + tipoplcheck + '&dostavka=' + dostavka + '&wsum=' + wsum,
        dataType: 'json',
        success: function (json) {

            if (json['success']) {
                var messageBox = '.success-notification';

                // если нет элемента для всплывающих сообщий, выводим обычным alert
                if ($(messageBox).length < 1) {
                    json['mes'] = json['mesclean'];
                }

                // Если положительный ответ
                if (json['status'] == 1) {

                    //Сравним итоговые суммы
                    var totalsum = parseFloat($("#TotalSumma").html().replace(/ /g, ''));
                    var totalajax = parseFloat(json['total']);

                    if (parseInt(totalsum) >= parseInt(totalajax)) {

                        $("#TotalSumma").html(json['total']);
                        $("#SkiSumma").html(json['discount']);
                        $("#OrderSumma").val(json['totalsummainput']);
                        //$("#SkiSummaAll").html(json['discountall']);

                        //Бесплатная доставка
                        if (json['freedelivery'] == 0) {
                            $("#DosSumma").html(json['delivery']);
                        }

                        $("#promocode").parent('.form-group, .input-group').addClass("has-success");
                        $("#promocode").parent('.form-group, .input-group').removeClass("has-error");

                        $(".paymOneEl").addClass("paymOneElr");
                        // $(".paymOneEl").removeClass("paymOneEl");

                        if (json['deliverymethodcheck'] != 0) {

                            $("input[name=order_metod]").change(function () {
                                if (this.value != json['deliverymethodcheck']) {
                                    $(this).closest('.paymOneEl').removeClass('active').attr("disabled", true);
                                    $('#order_metod[value="' + json['deliverymethodcheck'] + '"]').closest('.paymOneElr').addClass('active');
                                    $('#order_metod[value="' + json['deliverymethodcheck'] + '"]').prop('checked', true);
                                    showAlertMessage('Для данного промо-кода невозможно выбрать другой тип оплаты!');
                                }

                            });
                        }

                        $("#promotion_load").hide();

                        //выводим сообщение
                        if (json['mes'] != '') {
                            showAlertMessage(json['mes']);
                        }

                    } else {
                        showAlertMessage('Для данного промо-кода скидка является меньшей чем изначальная скидка');
                        $("#promotion_load").hide();
                    }

                } else if (json['status'] == 9) {

                    $("#TotalSumma").html(json['total']);
                    $("#SkiSumma").html(json['discount']);
                    $("#OrderSumma").val(json['totalsummainput']);
                    $("#SkiSummaAll").html(json['discountall']);

                    //Бесплатная доставка
                    if (json['freedelivery'] == 0) {
                        $("#DosSumma").html(json['freedelivery']);
                    }

                    // Убираем выделение промокода
                    $("#promocode").parent('.form-group').removeClass("has-success");
                    $("#promocode").parent('.form-group').removeClass("has-error");

                    if (json['deliverymethodcheck'] != 0) {
                        $('input[name=order_metod]').attr("disabled", true);
                        $('input[name=order_metod]:checked').attr("disabled", false);
                        $(".paymOneElr").click(function () {
                            showAlertMessage('Для данного промо-кода невозможно выбрать другой тип оплаты!');
                        });
                    }

                    $("#promotion_load").hide();

                }
                //Если отрицательный ответ
                else {

                    // Выделяем ошибку промокода
                    $("#promocode").parent('.form-group').removeClass("has-success");
                    $("#promocode").parent('.form-group').addClass("has-error");

                    $("#promotion_load").hide();

                    //перенаправляем на список оплат
                    if (json['action'] == 1) {
                        $("html, body").delay(2000).animate({scrollTop: $('#order_metod').offset().top}, 2000);

                    }
                    //выводим сообщение
                    if (json['mes'] != '')
                        showAlertMessage(json['mes']);
                }


            }
        }
    });
}
