<?php

function tab_info($data) {
    global $PHPShopGUI;
    
    $Info = '<p>
     <h4>Настройка модуля</h4>
        <ol>
        <li>Включить и настроить необходимые модули расчета доставок <a href="https://docs.phpshop.ru/moduli/dostavka/pochta-rossiie" target="_blank">Почта России</a>, <a href="https://docs.phpshop.ru/moduli/dostavka/cdek-widget" target="_blank">CDEK Widget</a> и <a href="https://docs.phpshop.ru/moduli/dostavka/yandex-delivery" target="_blank">Яндекс Доставка</a>.</li>
        <li> В поле <code>Хранение кеша</code> можно выбрать вариант хранение данных расчета доставки в <code>Базе данных MySQL</code> или памяти <code>Сервера кеширования Memcached</code>. Самый быстрый результат при использовании сервера кеширования Memcached.</li>
        <li>Указать данные в полях <code>Почтовый индекс города отправителя</code> и <code>Вес по умолчанию</code>, который будет использоваться при отсутствии персонального веса товара в базе.</li>
        <li>В меню <kbd>Настройки</kbd> - <kbd>Интеграции</kbd> активировать <code>Подсказки DaData.ru</code> и ввести персональный <code>Публичный ключ</code>.</li>
        <li>В меню <kbd>Настройки</kbd> - <kbd>Интеграции</kbd> указать адрес и порт сервера кеширования Memcached, по умолчанию <code>127.0.0.1</code> и <code>11211</code>.</li>
        
</ol>
   <h4>Подключение Memcached на хостинге Beget</h4>
    <ol>
        <li><a href="https://beget.com/p566" target="_blank">Зарегистрироваться</a> на хостинге Beget.</li>
        <li>В личном кабинете аккаунта хостинга Beget в разделе <a href="https://cp.beget.com/cloudservices/memcached" target="_blank">Сервисы</a> активируйте сервис <kbd>Memcached</kbd>.</li>
    </ol> 
    
    <h4>Настройка дизайна</h4>
    <ol>
        <li>Для отображения в подробной карточке товара виджета расчета стоимости доставок используется переменная <code>@deliverywidget@</code> в файле шаблона <code>phpshop/templates/имя_шаблона/product/main_product_forma_full.tpl</code></li>
    </ol>  

        </p>';
    
    return $PHPShopGUI->setInfo($Info, 280, '98%');
}
?>