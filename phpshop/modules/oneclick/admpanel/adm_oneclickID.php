<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.oneclick.oneclick_jurnal"));

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm;
    $_POST['date_new'] = PHPShopDate::GetUnixTime($_POST['date_new']);
    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    return array('success' => $action);
}

/**
 * Экшен сохранения
 */
function actionSave() {
    global $PHPShopGUI;


    // Сохранение данных
    actionUpdate();

    header('Location: ?path=' . $_GET['path']);
}

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $PHPShopSystem;

    // Выбор даты
    $PHPShopGUI->addJSFiles('./js/bootstrap-datetimepicker.min.js', './news/gui/news.gui.js');
    $PHPShopGUI->addCSSFiles('./css/bootstrap-datetimepicker.min.css');

    // Выборка
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));
    $PHPShopGUI->field_col = 3;

    if (!empty($data['product_image']))
        $icon = '<img src="' . $data['product_image'] . '" onerror="this.onerror = null;this.src = \'./images/no_photo.gif\'" class="media-object">';
    else
        $icon = '<img class="media-object" src="./images/no_photo.gif">';
    
    // Знак рубля
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB')
        $currency = ' <span class="rubznak">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();
    
    $name = '
<div class="media">
  <div class="media-left">
    <a href="?path=product&id=' . $data['product_id'] . '&return=modules.dir.oneclick.' . $data['id'] . '" >
      ' . $icon . '
    </a>
  </div>
   <div class="media-body">
    <div class="media-heading"><a href="?path=product&id=' . $data['product_id'] . '&return=modules.dir.oneclick.' . $data['id'] . '" >' . $data['product_name'] . '</a></div>
    ' . $data['product_price'] . ' '.$currency.'
  </div>
</div>';

    $Tab1.= $PHPShopGUI->setField('Дата', $PHPShopGUI->setInputDate("date_new", PHPShopDate::get($data['date'])));
    $Tab1.= $PHPShopGUI->setField('Имя', $PHPShopGUI->setInputText($data['ip'], 'name_new', $data['name']));
    $Tab1.= $PHPShopGUI->setField('Телефон', $PHPShopGUI->setInputText(false, 'tel_new', $data['tel']));
    $Tab1.= $PHPShopGUI->setField('E-mail', $PHPShopGUI->setInputText(false, 'mail_new', $data['mail']));
    $Tab1.= $PHPShopGUI->setField('Корзина', $name);

    $Tab1.=$PHPShopGUI->setField('Комментарий', $PHPShopGUI->setTextarea('message_new', $data['message']));

    $status_atrray[] = array('Новая заявка', 1, $data['status'],'data-content="<span class=\'glyphicon glyphicon-text-background\' style=\'color:#35A6E8\'></span> Новая заявка"');
    $status_atrray[] = array('Перезвонить', 2, $data['status'],'data-content="<span class=\'glyphicon glyphicon-text-background\' style=\'color:#EC971F\'></span> Перезвонить"');
    $status_atrray[] = array('Недоcтупен', 3, $data['status'],'data-content="<span class=\'glyphicon glyphicon-text-background\' style=\'color:red\'></span> Недоcтупен"');
    $status_atrray[] = array('Выполнен', 4, $data['status'],'data-content="<span class=\'glyphicon glyphicon-text-background\' style=\'color:#70BD1B\'></span> Выполнен"');

    $Tab1.=$PHPShopGUI->setField('Статус', $PHPShopGUI->setSelect('status_new', $status_atrray, 150));


    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true, false, true));

    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "Удалить", "right", 70, "", "but", "actionDelete.modules.edit") .
            $PHPShopGUI->setInput("submit", "editID", "Сохранить", "right", 70, "", "but", "actionUpdate.modules.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Функция удаления
function actionDelete() {
    global $PHPShopOrm;
    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    return array("success" => $action);
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>