<?php

function addOptionHit($data) {
    global $PHPShopGUI;

    // Опции вывода
    $Tab10 = $PHPShopGUI->setCheckbox('hit_new', 1, 'Вывод товара в хитах', @$data['hit']);

    $PHPShopGUI->addTab(array("Хиты", $Tab10, true));
}

function updateOptionHit($data) {
    global $PHPShopModules;
    if (empty($_POST['ajax'])) {

        $hit_cat = (new PHPShopOrm($PHPShopModules->getParam("base.hit.hit_system")))->select()['hit_cat'];

        if (!empty($hit_cat)) {

            if (empty($_POST['hit_new'])) {
                $_POST['hit_new'] = 0;
                $_POST['dop_cat_new'] = str_replace($hit_cat . '#', '', $_POST['dop_cat_new']);
            } else {
                if (!strstr($_POST['dop_cat_new'], $hit_cat . '#')) {
                    $_POST['dop_cat_new'] .= $hit_cat . '#';
                }
            }
        }
        else if (empty($_POST['hit_new']))
            $_POST['hit_new'] = 0;
    }
}

$addHandler = array(
    'actionStart' => 'addOptionHit',
    'actionDelete' => false,
    'actionUpdate' => 'updateOptionHit'
);
?>