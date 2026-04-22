<?php

function addCloudpaymentsDocs($data) {
    global $PHPShopGUI, $PHPShopSystem;

    $PHPShopGUI->addJSFiles('../modules/cloudkassir/admpanel/gui/cloudpayments.gui.js');


    if (!empty($data['ofd_status'])) {

        $ofd = unserialize($data['ofd']);
        if (is_array($ofd)) {


            $iso = $PHPShopSystem->getDefaultValutaIso();

            // Знак рубля
            if ($iso == 'RUB' or $iso == 'RUR')
                $currency = ' <span class=rubznak>p</span>';
            else
                $currency = $iso;


            $Tab = '<div class="panel panel-default">
            <table class="table table-hover">
            <tr>
            <td>Тип операции</td>
            <td>';

            if ($ofd['operation'] == 'sell' and is_array($ofd['payload'])) {
                $Tab.='<span id="operation-status" class="text-success">Приход</span>';
                $refundButton = '<button class="btn btn-default btn-sm" data-operation="sell_refund" id="cloudpayments" data-id="' . $data['id'] . '"><span class="glyphicon glyphicon-bookmark"></span> Выписать чек возврата</button><span class="text-success" id="refund_alert"></span>';
            } elseif(is_array($ofd['payload'])) {
                $Tab.='<span class="text-danger">Возврат</span>';
                $refundButton = null;
            }
            else {
                $Tab.='<span class="text-muted">Ошибка</span>';
                
                if(empty($data['operation']))
                    $data['operation']='sell';
                
                
                $refundButton = '<button class="btn btn-default btn-sm" data-operation="' . $data['operation'] . '" id="cloudpayments" data-id="' . $data['id'] . '"><span class="glyphicon glyphicon-bookmark"></span> Повторить последний чек</button>';
            }

            $Tab.='</td>
            </tr>
            <tr>
            <td>Заводской номер фискального накопителя</td>
            <td><b>' . $ofd['payload']['DeviceNumber'] . '</b></td>
            </tr>
            <tr>
            <td>Фискальный признак документа</td>
            <td><b>' . $ofd['payload']['FiscalSign'] . '</b></td>
            </tr>
            <tr>
            <td>Регистрационный номер ККТ</td>
            <td>' . $ofd['payload']['RegNumber'] . '</td>
            </tr>
            <tr>
            <td>Номер чека</td>
            <td>' . $ofd['payload']['DocumentNumber'] . '</td>
            </tr>
            <tr>
            <td>Дата, время</td>
            <td>' . $ofd['payload']['DateTime'] . '</td>
            </tr>
            <tr>
            <td>Общая стоимость позиции с учетом скидок и наценок</td>
            <td><b>' . $ofd['payload']['Amount'] . '</b> ' . $currency . '</td>
            </tr>
            <tr>
            <td>Проверка чека на сайте</td>
            <td><a href="'. $ofd['payload']['Url'] . '" target="_blank"><span class="glyphicon glyphicon-qrcode" style="padding-right:5px"></span>Чек на сайте</a></td>
            </tr>
            <tr>
            <td>Журнал работы кассы</td>
            <td><a href="?path=modules.dir.cloudkassir&id=' . $ofd['log_id'] . '" class="text-muted"><span class="glyphicon glyphicon-list-alt" style="padding-right:5px"></span>CloudKassir API Log</a></td>
            </tr>
            </table>
            </div>
            ' . $refundButton;
            $PHPShopGUI->addTab(array("Касса CloudKassir", $Tab, false, '7'));
        }
    }
    // Выписать новый кассовый чек
    else {
        $Tab = '<button class="btn btn-default btn-md" data-operation="sell" id="cloudpayments" data-id="' . $data['id'] . '"><span class="glyphicon glyphicon-bookmark"></span> Выписать кассовый чек прихода</button>';
        $PHPShopGUI->addTab(array("Касса CloudKassir", $Tab, true, '7'));
    }
}

$addHandler = array(
    'actionStart' => 'addCloudpaymentsDocs',
    'actionDelete' => false,
    'actionUpdate' => false
);
?>