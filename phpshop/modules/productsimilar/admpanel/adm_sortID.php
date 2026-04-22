<?php

function addProductSimilarSort($data) {
    global $PHPShopGUI;

    $Tab3 = $PHPShopGUI->setField(null, $PHPShopGUI->setCheckbox('productsimilar_enabled_new', 1, 'Включить в подбор товаров', $data['productsimilar_enabled']));

    $PHPShopGUI->addTab(array("Похожие товары", $Tab3, true));
}

function updateProductSimilarSort() {
    if(!isset($_POST['productsimilar_enabled_new']))
        $_POST['productsimilar_enabled_new']=0;
}


$addHandler = array(
    'actionStart' => 'addProductSimilarSort',
    'actionDelete' => false,
    'actionUpdate' => 'updateProductSimilarSort'
);
?>