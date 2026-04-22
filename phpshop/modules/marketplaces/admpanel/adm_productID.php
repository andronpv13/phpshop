<?php

include_once dirname(__DIR__) . '/class/Marketplaces.php';

function addMarketplacesTab($data) {
    global $PHPShopGUI;

    $Tab = '';

    // Валюты
    $PHPShopValutaArray = new PHPShopValutaArray();
    $valuta_array = $PHPShopValutaArray->getArray();
    if (is_array($valuta_array))
        foreach ($valuta_array as $val) {
            if ($data['baseinputvaluta'] == $val['id']) {
                $valuta_def_name = $val['code'];
            }
        }

    $Tab .= $PHPShopGUI->setField('<a href="/rss/google.php" target="_blank" title="Открыть файл">Google Merchant</a>', $PHPShopGUI->setCheckbox('google_merchant_new', 1, 'Вывод в Google Merchant', $data['google_merchant']));
    $Tab .= $PHPShopGUI->setField('<a href="/yml/?marketplace=' . Marketplaces::CDEK . '" target="_blank" title="Открыть файл">Яндекс.Маркет</a>', $PHPShopGUI->setCheckbox('cdek_new', 1, 'Вывод в Яндекс.Маркет', $data['cdek']));
    $Tab .= $PHPShopGUI->setField('<a href="/yml/?marketplace=' . Marketplaces::ALIEXPRESS . '" target="_blank" title="Открыть файл">AliExpress</a>', $PHPShopGUI->setCheckbox('aliexpress_new', 1, 'Вывод в AliExpress', $data['aliexpress']));
    $Tab .= $PHPShopGUI->setField('<a href="/yml/?marketplace=' . Marketplaces::SBERMARKET . '" target="_blank" title="Открыть файл">Мегамаркет</a>', $PHPShopGUI->setCheckbox('sbermarket_new', 1, 'Вывод в Мегамаркет', $data['sbermarket']));

    $Tab .= $PHPShopGUI->setField('Цена Google Merchant', $PHPShopGUI->setInputText(null, 'price_google_new', $data['price_google'], 150, $valuta_def_name), 2);
    $Tab .= $PHPShopGUI->setField('Цена Яндекс.Маркет', $PHPShopGUI->setInputText(null, 'price_cdek_new', $data['price_cdek'], 150, $valuta_def_name), 2);
    $Tab .= $PHPShopGUI->setField('Цена AliExpress', $PHPShopGUI->setInputText(null, 'price_aliexpress_new', $data['price_aliexpress'], 150, $valuta_def_name), 2);
    $Tab .= $PHPShopGUI->setField('Цена Мегамаркет', $PHPShopGUI->setInputText(null, 'price_sbermarket_new', $data['price_sbermarket'], 150, $valuta_def_name), 2);

    $PHPShopGUI->addTab(["Маркетплейсы", $Tab, true]);
}

function updateMarketplaces($product) {
    if (empty($_POST['ajax'])) {
        if (empty($_POST['google_merchant_new'])) {
            $_POST['google_merchant_new'] = 0;
        }
        if (empty($_POST['cdek_new'])) {
            $_POST['cdek_new'] = 0;
        }
        if (empty($_POST['aliexpress_new'])) {
            $_POST['aliexpress_new'] = 0;
        }
        if (empty($_POST['sbermarket_new'])) {
            $_POST['sbermarket_new'] = 0;
        }
    }
}

$addHandler = array(
    'actionStart' => 'addMarketplacesTab',
    'actionDelete' => false,
    'actionUpdate' => 'updateMarketplaces'
);
?>