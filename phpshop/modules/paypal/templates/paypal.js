// Paypal add form
$(document).ready(function() {
    var add_paypal_form = '<div class="paypal_add_form"><br><div id="citylist"><span class="required">*</span> Страна<br><select name="country_new" class="citylist req"><option value="RU" >Россия</option><option value="BY">Белоруссия</option><option value="UA">Украина</option><option value="KZ">Казахстан</option></select></div><br><span class="required">*</span> Город<br><input type="text" value="" name="city_new" class="req"><br><br><span class="required">*</span> Индекс<br><input type="text" value="" name="index_new" class="req"><br><br><span class="required">*</span> Улица<br><input type="text" value="" name="street_new" class="req"><br><br><span class="required">*</span> Дом<br><input type="text" value="" name="house_new" class="req"><br><br><span class="required">*</span> Квартира<br><input type="text" value="" name="flat_new" class="req"><br><br></div>';

    // Вывод дополнительных полей для paypal при смене оплаты
    $("input#order_metod").change(function() {
        if($(this).val() == 10003)
        $(add_paypal_form).insertAfter("#dop_info");    
        else  $('.paypal_add_form').html('');

    });
    
    // Если оплата paypal по умолчанию
    if($("input#order_metod:checked").val() == 10003){
        $(add_paypal_form).insertAfter("#dop_info");
    }
});     