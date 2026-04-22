<?php

$TitlePage = __("Покупатели");
PHPShopObj::loadClass('user');

function actionStart() {
    global $PHPShopInterface, $TitlePage;

    $PHPShopInterface->action_button['Добавить Пользователя'] = array(
        'name' => '',
        'action' => 'addNew',
        'class' => 'btn btn-default btn-sm navbar-btn',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-plus',
        'tooltip' => 'data-toggle="tooltip" data-placement="left" title="' . __('Добавить Пользователя') . '"'
    );

    $PHPShopInterface->action_select['Скопировать e-mail выбранных'] = array(
        'name' => 'Скопировать e-mail выбранных',
        'action' => 'copy-mail-select',
        'class' => 'disabled'
    );
    
    
    $PHPShopInterface->action_title['order'] = 'Новый заказ';

    // Мобильная версия
    if (PHPShopString::is_mobile()) {
        $memory['shopusers.option']['name'] = 1;
        $memory['shopusers.option']['mail'] = 0;
        $memory['shopusers.option']['menu'] = 0;
        $memory['shopusers.option']['status'] =1;
        $memory['shopusers.option']['action'] =0;
        $memory['shopusers.option']['discount'] = 1;
        $memory['shopusers.option']['date'] = 0;
        $memory['shopusers.option']['tel'] =0;
        $PHPShopInterface->mobile=true;
    }
    else {
        $memory['shopusers.option']['name'] = 1;
        $memory['shopusers.option']['mail'] = 1;
        $memory['shopusers.option']['menu'] = 1;
        $memory['shopusers.option']['status'] = 1;
        $memory['shopusers.option']['discount'] = 1;
        $memory['shopusers.option']['date'] = 1;
        $memory['shopusers.option']['action'] =1;
        $memory['shopusers.option']['tel'] =1;
        
    }

    $PHPShopInterface->addJSFiles('./shopusers/gui/shopusers.gui.js', './shopusers/gui/shopusers.ajax.js');
    $PHPShopInterface->setActionPanel($TitlePage, array('Скопировать e-mail выбранных','CSV', '|', 'Удалить выбранные'), array('Добавить Пользователя'));
    $PHPShopInterface->setCaption(array(null, "2%"), array("Имя", "25%",array('view' => intval($memory['shopusers.option']['name']))), array("E-mail", "15%",array('view' => intval($memory['shopusers.option']['mail']))), array("Телефон", "15%",array('view' => intval($memory['shopusers.option']['tel']))),array("Статус", "20%",array('view' => intval($memory['shopusers.option']['status']))), array("%", "5%",array('view' => intval($memory['shopusers.option']['discount']))), array("Вход", "10%",array('view' => intval($memory['shopusers.option']['date']))), array("", "7%",array('view' => intval($memory['shopusers.option']['menu']))), array("Статус", "7%", array('align' => 'right','view' => intval($memory['shopusers.option']['action']))));
    $PHPShopInterface->Compile();
}

/**
 * Подбор пользователей
 */
function actionOrderSearch() {

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
    $PHPShopOrm->Option['where'] = ' or ';
    $PHPShopOrm->debug = false;

    $data = $PHPShopOrm->select(array('*'), array('login' => " LIKE '%" . $_POST['words'] . "%'", 'name' => " LIKE '" . ucfirst($_POST['words']) . "%'"), array('order' => 'id DESC'), array('limit' => 20));
    if (is_array($data)) {
        foreach ($data as $row) {

            $result .= '<a href=\'#\' class=\'select-search\' data-id=\'' . $row['id'] . '\' data-name=\'' . $row['name'] . '\' data-tel=\'' . $row['tel'] . '\' data-mail=\'' . $row['login'] . '\'>' . $row['name'] . ',  ' . $row['login'] . ', ' . $row['tel'] . '</a><br>';
        }
        $result .= '<button type="button" class="close pull-right" aria-label="Close"><span aria-hidden="true">&times;</span></button>';

        exit($result);
    } else
        exit();
}

// Обработка событий
$PHPShopInterface->getAction();
?>