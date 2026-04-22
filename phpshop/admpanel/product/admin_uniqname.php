<?php

$TitlePage = __("Проверка уникальности названий товаров");

// Стартовый вид
function actionStart() {
    global $PHPShopInterface, $PHPShopGUI, $TitlePage, $PHPShopModules, $PHPShopSystem;

    $PHPShopInterface->setActionPanel($TitlePage, false, false);
    $PHPShopInterface->checkbox_action = false;

    $PHPShopInterface->setCaption(array("Иконка", "5%", array('sort' => 'none')), array("Название товара", "35%"),array("ID", "10%"),array("Повторы названий", "35%", array('align' => 'right', array('sort' => 'none'))));


    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $PHPShopOrm->debug = false;
    $PHPShopOrm->mysql_error = false;

    $PHPShopOrm->sql = "SELECT id, uid, name, enabled, pic_small, count(name) from " . $GLOBALS['SysValue']['base']['products'] . " where parent_enabled='0' GROUP BY name HAVING count(name)>1";

    $data = $PHPShopOrm->select();
    if (is_array($data))
        foreach ($data as $row) {

            if (empty($row['name']))
                continue;
            
            // Дубли
            $list=$ids=null;
            $PHPShopOrmProduct = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
            $product = $PHPShopOrmProduct->getList(['*'],['name'=>'=\''.$row['name'].'\'','parent_enabled'=>"='0'"]);
            if(is_array($product)){
                foreach ($product as $val){
                   $ids.=$val['id'].'<br>';
                   $list.= $PHPShopGUI->setLink('?path=product&return=' . $_GET['path'] . '&id=' . $val['id'], $val['name']).'<br>';
                }
            }

            if (!empty($row['pic_small']))
                $icon = '<img src="' . $row['pic_small'] . '" onerror="this.onerror = null;this.src = \'./images/no_photo.gif\'" class="media-object">';
            else
                $icon = '<img class="media-object" src="./images/no_photo.gif">';

            // Артикул
            if (!empty($row['uid']))
                $uid = '<div class="text-muted">' . __('Арт') . ' ' . $row['uid'] . '</div>';
            else
                $uid = '<div class="text-muted"></div>';

            // Вывод
            if (empty($row['enabled'])) {
                $enabled_css = 'text-muted';
            } else {
                $enabled_css = null;
            }


            $PHPShopInterface->setRow(array('name' => $icon, 'link' => '?path=product&return=' . $_GET['path'] . '&id=' . $row['id']), array('name' => $row['name'], 'link' => '?path=product&return=' . $_GET['path'] . '&id=' . $row['id'], 'addon' => $uid, 'class' => $enabled_css), $ids,  array('name' => $list, 'align' => 'right'));

        }

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, false);

    $sidebarleft[] = array('title' => 'Категории', 'content' => $PHPShopInterface->loadLib('tab_menu_service', false, './exchange/'));
    $PHPShopInterface->setSidebarLeft($sidebarleft, 2);

    // Футер
    $PHPShopInterface->Compile(2);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>