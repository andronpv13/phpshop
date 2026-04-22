<?php


function addCount($data) {
    global $PHPShopGUI;

    // Добавляем значения в функцию actionStart
    $Tab3=$PHPShopGUI->setField('Товаров в каталоге',$PHPShopGUI->setInputText(false, 'count_new', $data['count'],50));
    $PHPShopGUI->addTab(array("Количество",$Tab3,450));
}

function setCount(){
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $PHPShopOrm->debug=false;
    $action = $PHPShopOrm->select(array('COUNT(id) as count'),array('category' => '=' . intval($_POST['catalogID']),'enabled'=>"='1'"),false,array('limit'=>1));
    
    if(!empty($action['count']))
    $_POST['count_new']=$action['count'];
}

$addHandler=array(
        'actionStart'=>'addCount',
        'actionDelete'=>false,
        'actionUpdate'=>'setCount'
);

?>