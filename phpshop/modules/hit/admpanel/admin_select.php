<?php

function hitAddOption() {
    global $PHPShopInterface;

    $memory = $PHPShopInterface->getProductTableFields();

    $PHPShopInterface->_CODE .= '<p class="clearfix"> </p>';
    $PHPShopInterface->_CODE .= __('Хит') . '<br>';
    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('hit', 1, 'Лейбл Хит', $memory['catalog.option']['Хит']);
}

function hitActionSave() {
    global $PHPShopModules,$PHPShopOrm;

    if (isset($_POST['hit_new'])) {

        $hit_cat = (new PHPShopOrm($PHPShopModules->getParam("base.hit.hit_system")))->select()['hit_cat'];

        $val = array_values($_SESSION['select']['product']);
        if (is_array($val)) {
            foreach ($val as $id) {
                $dop_cat = $PHPShopOrm->select(['dop_cat'], ['id' => '=' . $id])['dop_cat'];
                
                if (empty($_POST['hit_new'])) {
                    $dop_cat = str_replace('#' . $hit_cat . '#', '', $dop_cat);
                } else {
                    if (!strstr($dop_cat, '#' . $hit_cat . '#')) {
                        $dop_cat .= '#' . $hit_cat . '#';
                    }
                }

                $PHPShopOrm->update(['dop_cat_new' => $dop_cat . $_POST['dop_cat_new']],['id'=>'='.$id]);
            }
        }

    }
}

$addHandler = [
    'actionOption' => 'hitAddOption',
    'actionSave' => 'hitActionSave'
];