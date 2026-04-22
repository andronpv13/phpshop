<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.sticker.sticker_forms"));

// Функция удаления
function actionDelete() {
    global $PHPShopOrm;
    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    return array('success' => $action);
}

// Выбор шаблона дизайна
function GetSkinList($skin) {
    global $PHPShopGUI;
    $dir = "../templates/";

    $value[] = array(__('Не выбрано'), '', '');

    if (is_dir($dir)) {
        if (@$dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if (file_exists($dir . '/' . $file . "/main/index.tpl")) {

                    if ($skin == $file)
                        $sel = "selected";
                    else
                        $sel = "";

                    if ($file != "." and $file != ".." and ! strpos($file, '.'))
                        $value[] = array($file, $file, $sel);
                }
            }
            closedir($dh);
        }
    }

    return $PHPShopGUI->setSelect('skin_new', $value);
}

/**
 * Экшен сохранения
 */
function actionSave() {


    // Сохранение данных
    actionUpdate();

    header('Location: ?path=' . $_GET['path']);
}

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm;

    if (empty($_POST['enabled_new']))
        $_POST['enabled_new'] = 0;

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    return array('success' => $action);
}

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $PHPShopOrm,$PHPShopModules;

    $PHPShopOrmOption = new PHPShopOrm($PHPShopModules->getParam("base.sticker.sticker_system"));
    $option = $PHPShopOrmOption->select();
    
    // Выборка
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));
    $PHPShopGUI->field_col = 3;

    // Редактор 
    if (empty($option['editor']))
        $editor = 'ace';
    else
        $editor = $PHPShopSystem->getSerilizeParam("admoption.editor");

    $PHPShopGUI->setEditor($editor, true);
    $oFCKeditor = new Editor('content_new', true);
    $oFCKeditor->Height = '320';
    $oFCKeditor->ToolbarSet = 'Normal';
    $oFCKeditor->Value = $data['content'];

    $Tab1 = $PHPShopGUI->setCollapse('Содержание', $oFCKeditor->AddGUI());

    $Tab_info = $PHPShopGUI->setField('Название:', $PHPShopGUI->setInputText(false, 'name_new', $data['name'], '100%'));
    $Tab_info .= $PHPShopGUI->setField('Маркер:', $PHPShopGUI->setInputText('@sticker_', 'path_new', $data['path'], '100%', '@'));
    $Tab_info .= $PHPShopGUI->setField('Опции:', $PHPShopGUI->setCheckbox('enabled_new', 1, 'Вывод на сайте', $data['enabled']));
    $Tab_info .= $PHPShopGUI->setField('Привязка к страницам:', $PHPShopGUI->setInputText(false, 'dir_new', $data['dir']) . $PHPShopGUI->setHelp('Пример: /page/about.html,/page/company.html'));
    $Tab_info .= $PHPShopGUI->setField('Дизайн', GetSkinList($data['skin']));

    $Tab1 .= $PHPShopGUI->setCollapse('Информация', $Tab_info);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true, false, true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "Удалить", "right", 70, "", "but", "actionDelete.modules.edit") .
            $PHPShopGUI->setInput("submit", "editID", "Сохранить", "right", 70, "", "but", "actionUpdate.modules.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>