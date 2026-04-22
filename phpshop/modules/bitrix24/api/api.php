<?php

$_classPath = "../../../";
include($_classPath . "class/obj.class.php");
include_once '../class/Bitrix24.php';

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");

class Bitrix24API {

    public function __construct()
    {
        $this->Bitrix24 = new Bitrix24();
        $this->PHPShopOrm = new PHPShopOrm();
    }

    /*
     * Обновление статуса заказа
     */
    public function updateOrderStatus()
    {
        if($this->Bitrix24->option['update_delivery_token'] === $_REQUEST['auth']['application_token']) {
            if(!empty($this->Bitrix24->option['statuses'])) {
                $deal = $this->Bitrix24->request('crm.deal.get', array('id' => (int) $_REQUEST['data']['FIELDS']['ID']));
                $statusSettings = unserialize($this->Bitrix24->option['statuses']);

                $order = $this->getOrderByDealId((int) $_REQUEST['data']['FIELDS']['ID']);
                if($order) {
                    if(isset($statusSettings[$deal['result']['STAGE_ID']])) {
                        $this->PHPShopOrm->query("UPDATE " . $GLOBALS['SysValue']['base']['orders'] . " SET `statusi`='" . $statusSettings[$deal['result']['STAGE_ID']] . "' WHERE `id`=" . $order['id']);
                        $this->Bitrix24->log(array('request' => $_REQUEST, 'deal' => $deal, 'statusSettings' => $statusSettings), $order['id'], 'Статус заказа успешно изменен', 'updateDeliveryStatus');
                    } else {
                        $this->Bitrix24->log(array('request' => $_REQUEST, 'deal' => $deal, 'statusSettings' => $statusSettings), $order['id'], 'Ошибка обновления статуса заказа. Не настроено соответсвие статусов сделки', 'updateDeliveryStatus', 'error');
                    }
                }
            }
        } else {
            $this->Bitrix24->log($_REQUEST, 0, 'Ошибка авторизации', 'updateDeliveryStatus', 'error');
        }
    }

    /*
     * Удаление id товара в Битрикс24
     */
    public function deleteProduct()
    {
        if($this->Bitrix24->option['delete_product_token'] === $_REQUEST['auth']['application_token'] and $_REQUEST['data']['FIELDS']['ID'] > 0) {
            $this->PHPShopOrm->query("UPDATE " . $GLOBALS['SysValue']['base']['products'] . " SET `bitrix24_product_id`='0' WHERE `bitrix24_product_id`=" . (int) $_REQUEST['data']['FIELDS']['ID']);
            $this->PHPShopOrm->query("UPDATE " . $GLOBALS['SysValue']['base']['delivery'] . " SET `bitrix24_delivery_id`='0' WHERE `bitrix24_delivery_id`=" . (int) $_REQUEST['data']['FIELDS']['ID']);
        }
    }

    /*
     * Удаление id пользователя\компании в Битрикс24
     */
    public function deleteUser()
    {
        if($this->Bitrix24->option['delete_contact_token'] === $_REQUEST['auth']['application_token'] or $this->Bitrix24->option['delete_company_token'] === $_REQUEST['auth']['application_token']) {
            $this->PHPShopOrm->query("UPDATE " . $GLOBALS['SysValue']['base']['shopusers'] . " SET `bitrix24_client_id`='0' WHERE `bitrix24_client_id`=" . (int) $_REQUEST['data']['FIELDS']['ID']);
        }
    }

    private function getOrderByDealId($dealId) {
        $orm = new PHPShopOrm('phpshop_orders');

        return $orm->getOne(array('*'), array('bitrix24_deal_id' => "='" . $dealId . "'"));
    }
}

$Bitrix24API = new Bitrix24API();

switch ($_REQUEST['event']) {
    case 'ONCRMDEALUPDATE':
        $Bitrix24API->updateOrderStatus();
        break;
    case 'ONCRMPRODUCTDELETE':
        $Bitrix24API->deleteProduct();
        break;
    case 'ONCRMCONTACTDELETE':
        $Bitrix24API->deleteUser();
        break;
    case 'ONCRMCOMPANYDELETE':
        $Bitrix24API->deleteUser();
        break;
}
