<?php

function tab_info($data) {
    global $PHPShopGUI;

    $Info = '<p>
        
   <h4>Настройка модуля</h4>
   <ol>
   <li>Скопировать "Авторизационный токен" из личного кабинета "Мегамаркет Про" <a href="https://partner.megamarket.ru/settings/merchants" target="_blank">Настройки - Интеграция по API</a> в одноименное поле настроек модуля.</li>
   <li>Указать по желанию "Пароль YML-файла".</li>
   <li>Указать параметры обновления данных (цены и склад).</li>
   <li><kbd>Сохранить</kbd> настройки модуля.</li>
   </ol>


      <h4>Настройка Мегамаркет</h4>
          <ol>
        <li>В личном кабинете "Мегамаркет Про" <a href="https://partner.megamarket.ru/settings/merchants" target="_blank">Настройки - Магазины</a>, указать ссылку на товарный фид: <code>https://' . $_SERVER['SERVER_NAME'] . '/yml/?marketplace=megamarket&pas=' . $data['password'] . '</code></li>
        <li>В личном кабинете "Мегамаркет Про" <a href="https://partner.megamarket.ru/settings/merchants" target="_blank">Настройки - Интеграция по API</a>, указать:
        
      <ul>
         <li>URL для метода создания отправления (order/new): <code>https://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/megamarket/api.php/' . md5($data['token']) . '/new</code></li>
          <li>URL для метода отмены лотов (order/cancel): <code>https://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/megamarket/api.php/' . md5($data['token']) . '/cancel</code></li>
       </ul>
      </li>
        <li>Связаться с представителем Мегамаркета через чат/почту и попросить активировать интеграцию по API для выгрузки заказов, обновления цен и остатков.</li>
    </ol>
    


   <h4>Выгрузка товаров в Мегамаркет</h4>
   <ol>
    <li>В карточке редактирования товара в магазине через закладку "Модули - Мегамаркет" включить опцию <kbd>Включить экспорт в Мегамаркет</kbd> и сохранить данные.</li>
    <li>После успешной загрузки товарного фида Мегамаркетом товары появятся в разделе <a href="https://partner.megamarket.ru/main/catalog/matched" target="_blank">Ассортимент - Готовые связки</a> в личном кабинете "Мегамаркет Про".</li>
     <li>Для ручной выгрузки цен и остатков товаров в Мегамаркет используйте кнопку <kbd>Выгрузить цены</kbd> в настройках модуля. Цены и остатки так же передаются в Мегамаркет при редактировании данных товара в админпанеле.</li>
    <li>Для автоматической выгрузки цен и остатков товаров в Мегамаркет по расписанию следует добавить новую задачу в модуль <a href="https://docs.phpshop.ru/moduli/razrabotchikam/cron" target="_blank">Задачи</a> с адресом запускаемого файла <code>phpshop/modules/megamarket/cron/products.php</code>.</li>
  </ol>
  
  <h4>Загрузка заказов с Мегамаркет</h4>
   <ol>
    <li>Новые заказы с Мегамаркет будут автоматически попадать в заказы на сайте со статусом и доставкой, указанными в настройке модуля. 
    <li>Поддерживается автоматическое списание товара по признаку статуса заказа в магазине из настроек модуля.</li>
  </ol>
        </p>';

    return $PHPShopGUI->setInfo($Info, 280, '98%');
}

?>
