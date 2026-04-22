<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.pbrf.pbrf_system"));


// Функция обновления
function actionUpdate() {
    global $PHPShopOrm;

    $_POST['data_new'] = serialize($_POST['data']);

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=pbrf');
    return $action;
}

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI,$PHPShopOrm;

    //Системные настройки
    $data = $PHPShopOrm->select();

    $data_person = unserialize($data['data']);

    $Tab1 .= $PHPShopGUI->setField('Ключ API:',$PHPShopGUI->setInputText(false, 'key_new', $data['key'], '60%'),1,'Выдается в pbrf.ru');

    $Tab1 .= $PHPShopGUI->setLine() . $PHPShopGUI->setField('Ваши данные для печати:', 
        $PHPShopGUI->setInputText('Фамилия&nbsp;&nbsp; ', 'data[surname]', $data_person['surname'], '60%', false , 'left') . 
        $PHPShopGUI->setLine(false, 10) .
        $PHPShopGUI->setInputText('Имя&nbsp;&nbsp; ', 'data[name]', $data_person['name'], '60%', false , 'left') . 
        $PHPShopGUI->setLine(false, 10) .
        $PHPShopGUI->setInputText('Отчество&nbsp;&nbsp; ', 'data[name2]', $data_person['name2'], '60%') . 
        $PHPShopGUI->setLine(false, 10) .
        $PHPShopGUI->setInputText('Страна&nbsp;&nbsp; ', 'data[country]', $data_person['country'], '20%' , false , 'left') .
        $PHPShopGUI->setInputText('Область, Район', 'data[region]', $data_person['region'], '20%', false , 'left') . 
        $PHPShopGUI->setInputText('Город&nbsp;&nbsp; ', 'data[city]', $data_person['city'], '20%') . 
        $PHPShopGUI->setLine(false, 10) .
        $PHPShopGUI->setInputText('Улица&nbsp;&nbsp; ', 'data[street]', $data_person['street'], '20%' , false , 'left') . 
        $PHPShopGUI->setInputText('Дом&nbsp;&nbsp; ', 'data[build]', $data_person['build'], '20%' , false , 'left') . 
        $PHPShopGUI->setInputText('Квартира&nbsp;&nbsp; ', 'data[appartment]', $data_person['appartment'], '20%') . 
        $PHPShopGUI->setLine(false, 10) .
        $PHPShopGUI->setInputText('Почтовый индекс&nbsp;&nbsp; ', 'data[zip]', $data_person['zip'], '40%') .
        $PHPShopGUI->setLine(false, 10) .
        $PHPShopGUI->setInputText('Телефон для sms&nbsp;&nbsp; +7', 'data[tel]', $data_person['tel'], '40%')
    , 'left', false, false, array('width' => '98%'));

    $Tab1 .= $PHPShopGUI->setLine() . $PHPShopGUI->setField('Предъявленный документ:', 
        $PHPShopGUI->setInputText('Наименование документа&nbsp;&nbsp; ', 'data[document]', $data_person['document'], '60%') . 
        $PHPShopGUI->setLine(false, 10) .
        $PHPShopGUI->setInputText('Серия&nbsp;&nbsp; ', 'data[document_serial]', $data_person['document_serial'], '30%', false , 'left') . 
        $PHPShopGUI->setInputText('№&nbsp;&nbsp; ', 'data[document_number]', $data_person['document_number'], '30%') . 
        $PHPShopGUI->setLine(false, 10) .
        $PHPShopGUI->setInputText('Выдан&nbsp;&nbsp; ', 'data[document_day]', $data_person['document_day'], '40%' , false , 'left') . 
        $PHPShopGUI->setInputText('20&nbsp;&nbsp; ', 'data[document_year]', $data_person['document_year'], '20%',__('г.')) . 
        $PHPShopGUI->setLine(false, 10) .
        $PHPShopGUI->setInputText('Наименование учреждения выдающего документ&nbsp;&nbsp; ', 'data[document_issued_by]', $data_person['document_issued_by'], '60%' , false , 'left')
    , 'left', false, false, array('width' => '98%'));


    // Содержание закладки 3
    $Info = '<h4>Инструкция сервиса pbrf.ru</h4>
    <p><b>Для получения ключа необходимо:</b>
    <ul>
        <li>Зарегистироваться на <a target="_blank" href="http://pbrf.ru/пользователь/войти">Pbrf.ru</a>.</li>
        <li>Получить ключ доступа в личном кабинете <i>(вкладка API)</i>.</li>
        <li>Ввести этот ключ в поле <kbd>Ключ API</kbd> в настройки модуля.</li>
    </ul>
    </p>
    <p class="alert alert-info">Название домена при создание ключа необходимо указывать первого уровня, даже если магазин работает у вас на субдомене.<br>
    Работать с API сервиса pbrf.ru доступно только на некоторых платных тарифах. Подробнее <a target="_blank" href="http://pbrf.ru/тарифы/выбрать-тариф">смотреть</a> на сайте компании.</p>';
    $Tab3=$PHPShopGUI->setInfo($Info, 250, '95%');

    // Содержание закладки 4
    $Tab4=$PHPShopGUI->setPay(false,true);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Настройка",$Tab1,true), array("Инструкция",$Tab3), array("О Модуле",$Tab4));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter=
            $PHPShopGUI->setInput("hidden","newsID",$id,"right",70,"","but").
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['newsID'], 'actionStart');

?>


