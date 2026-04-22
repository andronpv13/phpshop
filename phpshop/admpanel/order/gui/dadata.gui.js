$().ready(function () {

    // Подсказки DaData.ru
    var DADATA_TOKEN = $('#body').attr('data-token');

    if (DADATA_TOKEN != "") {

        $("[name='fio_new'],[name='mass[0][fio_new]']").suggestions({
            token: DADATA_TOKEN,
            partner: "PHPSHOP",
            type: "NAME",
            params: {
                parts: ["NAME"]
            },
            count: 5
        });

        // Подсказки DaData.ru
        var
                token = DADATA_TOKEN,
                type = "ADDRESS",
                $city = $("[name='city_new'],[name='mass[0][city_new]']"),
                $street = $("[name='street_new'],[name='mass[0][street_new]']"),
                $house = $("[name='house_new'],[name='mass[0][house_new]']");

        $city.suggestions({
            token: token,
            partner: "PHPSHOP",
            type: type,
            hint: false,
            bounds: "city-settlement",
            onSelect: showPostalCode,
            onSelectNothing: clearPostalCode
        });

        $street.suggestions({
            token: token,
            partner: "PHPSHOP",
            type: type,
            hint: false,
            bounds: "street",
            constraints: $city,
            onSelect: showPostalCode,
            onSelectNothing: clearPostalCode
        });

        $house.suggestions({
            token: token,
            partner: "PHPSHOP",
            type: type,
            hint: false,
            bounds: "house",
            constraints: $street,
            onSelect: showPostalCode,
            onSelectNothing: clearPostalCode
        });
        function showPostalCode(suggestion) {
            $("[name='index_new'],[name='mass[0][index_new]']").val(suggestion.data.postal_code);
        }
        function clearPostalCode() {
            $("[name='index_new'],[name='mass[0][index_new]']").val("");
        }
        $("[name='fio_new'],[name='mass[0][fio_new]']").suggestions({
            token: DADATA_TOKEN,
            partner: "PHPSHOP",
            type: "NAME",
            count: 5
        });
        $("[name='org_name_new'],[name='mass[0][org_name_new]']").suggestions({
            token: DADATA_TOKEN,
            partner: "PHPSHOP",
            type: "PARTY",
            count: 5
        });

        $("[name='org_name_new'],[name='mass[0][org_name_new]']").suggestions({
            token: DADATA_TOKEN,
            partner: "PHPSHOP",
            type: "PARTY",
            count: 5,
            onSelect: showSuggestion
        });
        $("[name='org_bank_new'],[name='mass[0][org_bank_new]']").suggestions({
            token: DADATA_TOKEN,
            partner: "PHPSHOP",
            type: "BANK",
            count: 5,
            onSelect: showSuggestionBank
        });
    }

    // Вывод подсказок DaData.ru в форме юридических данных
    function showSuggestion(suggestion) {
        var data = suggestion.data;
        if (!data)
            return;
        $("input[name='org_inn_new'],[name='mass[0][org_inn_new]']").val(data.inn);
        $("input[name='org_kpp_new'],[name='mass[0][org_kpp_new]']").val(data.kpp);
        $("input[name='org_yur_adres_new'],[name='mass[0][org_yur_adres_new]']").val(data.address.value);
        $("input[name='org_fakt_adres_new'],[name='mass[0][org_fakt_adres_new]']").val(data.address.value);
    }
    function showSuggestionBank(suggestion) {
        var data = suggestion.data;
        if (!data)
            return;
        $("input[name='org_bik_new'],[name='mass[0][org_bik_new]']").val(data.bic);
        $("input[name='org_city_new'],[name='mass[0][org_city_new]']").val(data.address.data.city);
        $("input[name='org_kor_new'],[name='mass[0][org_kor_new]']").val(data.correspondent_account);
    }
});