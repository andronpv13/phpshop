<?php

function actionStart() {
    global $PHPShopInterface, $PHPShopModules,$select_name;

    $PHPShopInterface->setActionPanel('Пункты выдачи', $select_name, ['Добавить +']);
    $PHPShopInterface->setCaption(
        ['', '1%'],
        ['Название', '39%'],
        ['Город', '30%'],
        ['Адрес', '30%']
    );

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam('base.branches.branches_branches'));
    $PHPShopOrm->debug = false;
    $citiesOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['citylist_city']);
    $citiesOrm->debug = false;

    $branches = $PHPShopOrm->getList(['*'], false, ['order' => 'id']);

    $cities = array_column($citiesOrm->getList(['*'], ['country_id' => '="3159"']), 'name', 'city_id');

    foreach ($branches as $branch) {
        $PHPShopInterface->setRow(
            $branch['id'],
            ['name' => $branch['name'], 'link' => sprintf('?path=modules.dir.branches&id=%s', $branch['id']), 'align' => 'left'],
            $cities[$branch['city_id']],
            $branch['address']
        );
    }
    $PHPShopInterface->Compile();
}