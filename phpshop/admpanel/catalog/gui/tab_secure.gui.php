<?php

/**
 * Панель безопасности каталога
 * @param array $data массив данных
 * @return string 
 */
function tab_secure($data) {
    global $PHPShopGUI;

    $secure_groups = $data['secure_groups'];
    
    if(empty($secure_groups)) $secure_groups_empty=1;
    $disp= null;

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['users']);
    $data = $PHPShopOrm->select(array('*'), array('enabled' => "='1'"), false, array('limit' => 100));
    if (is_array($data)) {

        $disp.=$PHPShopGUI->setLine($PHPShopGUI->setCheckbox('secure_groups_new[all]', '1', 'Все пользователи', $secure_groups_empty));

        foreach ($data as $row) {
            if (strlen($secure_groups)) {
                $string = 'i' . $row['id'] . 'i';
                if (strpos($secure_groups, $string) !== false) {
                    $che = 1;
                } else {
                    $che = 0;
                }
            } else {
                $che = 0;
            }

            if ($row['id'] == $_SESSION['idPHPSHOP']) {
                $row['name'] = '<kbd>'.$row['name'].'</kbd>';
            } 

            
            $disp.=$PHPShopGUI->setLine($PHPShopGUI->setCheckbox('secure_groups_new['.$row['id'].']', 1, $row['name'].' (login: ' . $row['login'] . ', e-mail: ' . $row['mail'] . ')', $che, null, false));
        }

    }
    return $disp;
}

?>
