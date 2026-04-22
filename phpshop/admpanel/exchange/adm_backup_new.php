<?php

$TitlePage = __('Создание резервной копии базы');
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers_status']);
PHPShopObj::loadClass('user');

// Стартовый вид
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $TitlePage;

    $PHPShopGUI->action_button['Создать'] = array(
        'name' => __('Выполнить'),
        'action' => 'saveID',
        'class' => 'btn  btn-default btn-sm navbar-btn',
        'type' => 'submit',
        'icon' => 'glyphicon glyphicon-download-alt'
    );


    // Размер названия поля
    $PHPShopGUI->field_col = 2;
    $PHPShopGUI->addJSFiles('./exchange/gui/exchange.gui.js');
    $PHPShopGUI->setActionPanel($TitlePage, false, array('Создать'));

    $structure_value[] = array('Структура и данные', '0', 'selected');
    $structure_value[] = array('Только структура', '1', '');

    // Список таблиц
    foreach ($GLOBALS['SysValue']['base'] as $val) {
        if (is_array($val)) {
            foreach ($val as $mod_base)
                $baseArray[$mod_base] = $mod_base;
        }
        else
            $baseArray[$val] = $val;
    }
    

    $table=null;
    if(is_array($baseArray)){
        ksort($baseArray);
        foreach ($baseArray as $val) {
        $table.='<option value="' . $val . '" selected class="">' . $val . '</option>';
    }
    }

    // Содержание закладки 1
    $PHPShopGUI->_CODE.= $PHPShopGUI->setCollapse('Настройки', $PHPShopGUI->setField('Таблицы', '
        <table >
        <tr>
        <td>
        <select id="pattern_table" style="height:400px;width:500px" name="pattern_table[]" multiple class="form-control" required>' . $table . '</select>
        </td>
        <td>&nbsp;</td>
        <td class="text-center"><a class="btn btn-default btn-sm" href="#" id="select-all" data-toggle="tooltip" data-placement="top" title="' . __('Выбрать все') . '"><span class="glyphicon glyphicon-chevron-left"></span></a><br><br>
        <a class="btn btn-default btn-sm" id="select-none" href="#" data-toggle="tooltip" data-placement="top" title="' . __('Убрать выделение со всех') . '"><span class="glyphicon glyphicon-chevron-right"></span></a></td>
        </tr>
   </table>
            
' . $PHPShopGUI->setHelp('Для выбора более одной записи нажмите левой кнопкой мыши на запись, удерживая клавишу CTRL')) .
            $PHPShopGUI->setField('GZIP сжатие', $PHPShopGUI->setCheckbox('export_gzip', 1, null, 1), 1, 'Сокращает размер создаваемого файла') .
            $PHPShopGUI->setField('Комментарий', $PHPShopGUI->setInputText(false, 'export_comment', '', 300)) .
            $PHPShopGUI->setField('Варианты копирования', $PHPShopGUI->setSelect('export_structure', $structure_value, 300, true)), 'in', false);

    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, false);

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "ОК", "right", 70, "", "but", "actionCreate.exchange.edit");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Функция записи
function actionCreate() {
    global $PHPShopModules, $PHPShopGUI;

    

    // Обновление
    if (!empty($_REQUEST['update']))
        $file = 'upload_dump.sql';
    else if (!empty($_POST['export_comment']))
        $file = substr(PHPShopString::toLatin($_POST['export_comment']), 0, 25) . '.sql';
    else
        $file = 'base_' . date("Y_m_d_His") . '.sql';


    $file = "./dumper/backup/" . $file;
    include_once('./dumper/dumper.php');
    $result = mysqlbackup($GLOBALS['SysValue']['connect']['dbase'], $file, $_POST['export_structure'], $_POST['pattern_table']);

    // Gzip
    if (!empty($_REQUEST['export_gzip'])) {
        $result = PHPShopFile::gzcompressfile($file);
    }

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    if (empty($_REQUEST['update'])) {

        if ($result)
            header('Location: ?path=' . $_GET['path']);
        else
            echo $PHPShopGUI->setAlert(__('Нет прав на запись файла') . ' ' . $file, 'danger');
    }
    else
        return array('success' => true);
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>