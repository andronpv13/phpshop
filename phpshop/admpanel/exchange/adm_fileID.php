<?php

// Функция удаления битой картинки
function actionDelete() {
    global $PHPShopOrm, $PHPShopModules;
    

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
    $data_img = $PHPShopOrm->getOne(['parent','name'],['id'=>'='.$_POST['rowID']]);
    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    
    // Проверка товара на главное изображение
    $PHPShopOrmProduct = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $data_product = $PHPShopOrmProduct->getOne(['id','pic_big'],['id'=>'='.$data_img['parent']]);
    
    // Удалена главная картинка
    if($data_product['pic_big'] == $data_img['name']){
        
        $PHPShopOrm->clean();
        $data = $PHPShopOrm->getOne(['name'],['parent'=>'='.$data_product['id']]);
        
        $pic_small = str_replace(array('.jpg', '.png','.JPG', '.PNG'), array('s.jpg', 's.png','s.jpg', 's.png'), $data['name']);
        $PHPShopOrmProduct->clean();
        
        $PHPShopOrmProduct->update(['pic_small_new'=>(string)$pic_small,'pic_big_new'=>(string)$data['name']],['id'=>'='.$data_product['id']]);
    }
    
    return array('success' => $action);
}


// Обработка событий
$PHPShopGUI->getAction();

?>