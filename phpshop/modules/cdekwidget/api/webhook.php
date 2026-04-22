<?php

session_start();
$_classPath = "../../../";
include $_classPath . "class/obj.class.php";
include_once($_classPath . "class/mail.class.php");
PHPShopObj::loadClass("payment");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("lang");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("file");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("modules");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("parser");
PHPShopObj::loadClass("string");
PHPShopObj::loadClass("bot");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
$PHPShopModules = new PHPShopModules($_classPath . "modules/");
$PHPShopModules->checkInstall('cdekwidget');
$PHPShopSystem = new PHPShopSystem();
include_once dirname(__FILE__) . '/../class/CDEKWidget.php';

$data=json_decode(file_get_contents("php://input"),true);

if(is_array($data)){
    
    // Создан
    if($data['attributes']['code'] == 'CREATED'){
        
      $order=(new PHPShopOrm($GLOBALS['SysValue']['base']['orders']))->getOne(['tel','cdek_order_data'],['uid'=>'="'.(string) $data['attributes']['number'].'"']);
      $tel=$order['tel'];
      $tracking=$data['attributes']['cdek_number'];
      $delivery_info= unserialize($order['cdek_order_data'])['delivery_info'];
      if(!empty($tracking))
          $delivery_info.=PHP_EOL.PHP_EOL.'Трек для отслеживания: '.$tracking;
      
      if(!empty($tel)){
          
          $PHPShopWappi = new PHPShopWappi();
          $text='Ваш заказ №'.$data['attributes']['number'].' передан в службу доставки СДЕК.'.PHP_EOL.PHP_EOL.$delivery_info;
          $PHPShopWappi->cascade(PHPShopString::win_utf8($text), $tel);
      }
    }
    
    // Можно забирать
    if($data['attributes']['code'] == 'ACCEPTED_AT_PICK_UP_POINT'){
        
      $order=(new PHPShopOrm($GLOBALS['SysValue']['base']['orders']))->getOne(['tel','cdek_order_data'],['uid'=>'="'.(string) $data['attributes']['number'].'"']);
      $tel=$order['tel'];
      $tracking=$data['attributes']['cdek_number'];
      $delivery_info= unserialize($order['cdek_order_data'])['delivery_info'];
      if(!empty($tracking))
          $delivery_info.=PHP_EOL.PHP_EOL.'Трек для отслеживания: '.$tracking;
      
      if(!empty($tel)){
          
          $PHPShopWappi = new PHPShopWappi();
          $text='Ваш заказ №'.$data['attributes']['number'].' готов к получению в пункте выдачи СДЕК.'.PHP_EOL.PHP_EOL.$delivery_info;
          $PHPShopWappi->cascade(PHPShopString::win_utf8($text), $tel);
          
          // Тест
          $PHPShopWappi->cascade(PHPShopString::win_utf8($text), '79267386748');
      }
    }
}
