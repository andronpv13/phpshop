<?php

function addOptionAcreditStatus($data) {
    global $PHPShopGUI;

    // Опции вывода
    $reference = date('dmy', $data['datas']) . str_replace('-', '', $data['uid']);
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['alfacredit']['alfacredit_log']);
    $PHPShopOrm->debug = false;
    $row = $PHPShopOrm->select(array('*'), array('reference' => "='" . $reference . "'"), false, array('limit' => 1));
    if (isset($row['id'])) {
        // Настройки модуля
        include_once dirname(__FILE__) . '/../hook/mod_option.hook.php'; 
        $PHPShopAlfaCredit = new PHPShopAlfaCreditArray(false);
        
        $status = unserialize($row['status']);
        
        $fields = array (
            'lastName' => __('Фамилия'),
            'firstName' => __('Имя'),
            'credTitle' => __('Наименование кредитного продукта'),
            'credTerm' => __('Срок кредита')
        );
        
        $dis = '';
        foreach ($fields as $key => $field) {
            if (isset($status[$key]))
                $dis .= $field . ': ' . $status[$key] . '<br>';
        }
        $dis = substr($dis, 0, -4);
        $dis .= __('Статус заявки').': ' . $PHPShopAlfaCredit->statuses[$status['currentStatus']];
        
        $Tab3 = $PHPShopGUI->setPanel('Информация о заявке', $dis);
        $PHPShopGUI->addTab(array("Кредит", $Tab3, true));
    }
}

$addHandler = array(
    'actionStart' => 'addOptionAcreditStatus',
    'actionDelete' => false,
    'actionUpdate' => false
);
?>