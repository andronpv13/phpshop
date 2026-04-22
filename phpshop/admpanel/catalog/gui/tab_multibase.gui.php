<?php
/**
 * Панель мультибазы каталога
 * @param array $data массив данных
 * @return string 
 */
function tab_multibase($val,$size,$multiple=true) {
    global $PHPShopGUI;
    
    $value=array();
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['servers']);
    $data = $PHPShopOrm->select(array('*'), array('enabled'=>"='1'"), array('order' => 'id'), array('limit' => 1000));
    
    if(empty($size))
    $size = '300';
    
    if(empty($multiple))
        $name='servers_new';
    else $name='servers[]';

    $data[1000] = array('host'=>'Главный сайт', 'id'=>1000);
    

    $server = preg_split('/i/', $val['servers'], -1, PREG_SPLIT_NO_EMPTY);
    
    if (is_array($data)) {
        foreach ($data as $row) {
            $sel=false;
            if (is_array($server))
                foreach ($server as $v) {
                    if ($row['id'] == $v)
                        $sel = "selected";
                }
            $value[] = array(PHPShopString::check_idna($row['host'],true), $row['id'], $sel);
        }
        return  $PHPShopGUI->setSelect($name, $value, $size, false, false, false, false, false,$multiple);
    }
    else return $PHPShopGUI->setHelp('Нет дополнительных витрин. <a href="?path=system.servers&action=new">Создать витрину</a>.');
    
}

?>