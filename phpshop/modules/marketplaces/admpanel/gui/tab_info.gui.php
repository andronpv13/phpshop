<?php

include_once dirname(dirname(__DIR__)) . '/class/Marketplaces.php';

function tab_info($data) {
    global $PHPShopGUI;

    $Info = '<p>
            <h4>ƒоступные прайс-листы</h4>
        <ol>
        <li>Google Merchant: <code>' . Marketplaces::getProtocol() . $_SERVER['SERVER_NAME'] .$GLOBALS['SysValue']['dir']['dir']. '/rss/google.xml</code>
        <li>AliExpress: <code>' . Marketplaces::getProtocol() . $_SERVER['SERVER_NAME'] .$GLOBALS['SysValue']['dir']['dir']. '/yml/?marketplace=' . Marketplaces::ALIEXPRESS . '</code>
        <li>ћегамаркет: <code>' . Marketplaces::getProtocol() . $_SERVER['SERVER_NAME'] .$GLOBALS['SysValue']['dir']['dir']. '/yml/?marketplace=' . Marketplaces::SBERMARKET . '</code>
        <li>яндекс.ћаркет: <code>' . Marketplaces::getProtocol() . $_SERVER['SERVER_NAME'] .$GLOBALS['SysValue']['dir']['dir']. '/yml/?marketplace=' . Marketplaces::CDEK . '</code>
        </ol>                      
      <h4>SQL запросы дл€ пакетной обработки</h4>
      <p>
      ƒл€ использовани€ SQL команд в большинстве случаев помогает штатна€ возможность панели управлени€ магазином <kbd>Ѕаза</kbd> -  <kbd>SQL запрос к базе</kbd>.
      </p>
       <table class="table table-bordered table-striped">
   <thead>
        <tr>
          <th>#</th>
          <th>SQL</th>
          <th>ќписание</th>
        </tr>
      </thead>
    <tbody>
         <tr>
          <th scope="row">1</th>
          <td>update phpshop_products set google_merchant=\'0\' where price<1 or items<1;</td>
          <td>«амена статуса участие в Google Merchant (убрать из выгрузки) при пустом складе или нулевой цене</td>
        </tr>
         <tr>
          <th scope="row">2</th>
          <td>update phpshop_products set google_merchant=\'1\' where price>0 and items>0;</td>
          <td>«амена статуса участие в Google Merchant (добавить в выгрузку) при положительном складе и не нулевой цене</td>
        </tr>
        <tr>
          <th scope="row">5</th>
          <td>update phpshop_products set aliexpress=\'0\' where price<1 or items<1;</td>
          <td>«амена статуса участие в AliExpress (убрать из выгрузки) при пустом складе или нулевой цене</td>
        </tr>
         <tr>
          <th scope="row">6</th>
          <td>update phpshop_products set aliexpress=\'1\' where price>0 and items>0;</td>
          <td>«амена статуса участие в AliExpress (добавить в выгрузку) при положительном складе и не нулевой цене</td>
        </tr>
        <tr>
          <th scope="row">7</th>
          <td>update phpshop_products set sbermarket=\'0\' where price<1 or items<1;</td>
          <td>«амена статуса участие в ћегамаркет (убрать из выгрузки) при пустом складе или нулевой цене</td>
        </tr>
         <tr>
          <th scope="row">8</th>
          <td>update phpshop_products set sbermarket=\'1\' where price>0 and items>0;</td>
          <td>«амена статуса участие в ћегамаркет (добавить в выгрузку) при положительном складе и не нулевой цене</td>
        </tr>
   </tbody>
</table>
</p>';
    
    return $PHPShopGUI->setInfo($Info, 280, '98%');
}
?>
