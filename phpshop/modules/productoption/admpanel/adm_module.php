<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.productoption.productoption_system"));

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $action = $PHPShopOrm->update(array('version_new' => $new_version));
    header('Location: ?path=modules&id='.$_GET['id']);
    return $action;
}

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm,$PHPShopModules;
    
    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $vendor = array(
        'option_1_name' => $_POST['option_1_name'],
        'option_1_format' => $_POST['option_1_format'],
        'option_2_name' => $_POST['option_2_name'],
        'option_2_format' => $_POST['option_2_format'],
        'option_3_name' => $_POST['option_3_name'],
        'option_3_format' => $_POST['option_3_format'],
        'option_4_name' => $_POST['option_4_name'],
        'option_4_format' => $_POST['option_4_format'],
        'option_5_name' => $_POST['option_5_name'],
        'option_5_format' => $_POST['option_5_format'],
        'option_6_name' => $_POST['option_6_name'],
        'option_6_format' => $_POST['option_6_format'],
        'option_7_name' => $_POST['option_7_name'],
        'option_7_format' => $_POST['option_7_format'],
        'option_8_name' => $_POST['option_8_name'],
        'option_8_format' => $_POST['option_8_format'],
        'option_9_name' => $_POST['option_9_name'],
        'option_9_format' => $_POST['option_9_format'],
        'option_10_name' => $_POST['option_10_name'],
        'option_10_format' => $_POST['option_10_format'],
    );

    $_POST['option_new'] = serialize($vendor);

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id='.$_GET['id']);
    return $action;
}

function checkSelect($val) {
    $value[] = array('text', 'text', $val);
    $value[] = array('textarea', 'textarea', $val);
    //$value[] = array('checkbox', 'checkbox', $val);
    $value[] = array('hidden', 'hidden', $val);
    $value[] = array('editor', 'editor', $val);
    $value[] = array('radio', 'radio', $val);
    return $value;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    $PHPShopGUI->field_col = 1;

    // Выборка
    $data = $PHPShopOrm->select();
    $vendor = unserialize($data['option']);

    $Tab1 = $PHPShopGUI->setField('Опция A', $PHPShopGUI->setInputText('Имя:', 'option_1_name', $vendor['option_1_name'], 180, false, 'left') . '&nbsp;' . $PHPShopGUI->setSelect('option_1_format', checkSelect($vendor['option_1_format']), 100). '&nbsp;<span class="text-muted">@productOption1@</span>');

    $Tab1.= $PHPShopGUI->setField('Опция B', $PHPShopGUI->setInputText('Имя:', 'option_2_name', $vendor['option_2_name'], 180, false, 'left') . '&nbsp;' . $PHPShopGUI->setSelect('option_2_format', checkSelect($vendor['option_2_format']), 100). '&nbsp;<span class="text-muted">@productOption2@</span>');

    $Tab1.= $PHPShopGUI->setField('Опция C', $PHPShopGUI->setInputText('Имя:', 'option_3_name', $vendor['option_3_name'], 180, false, 'left') . '&nbsp;' . $PHPShopGUI->setSelect('option_3_format', checkSelect($vendor['option_3_format']), 100). '&nbsp;<span class="text-muted">@productOption3@</span>');

    $Tab1.= $PHPShopGUI->setField('Опция D', $PHPShopGUI->setInputText('Имя:', 'option_4_name', $vendor['option_4_name'], 180, false, 'left') . '&nbsp;' . $PHPShopGUI->setSelect('option_4_format', checkSelect($vendor['option_4_format']), 100). '&nbsp;<span class="text-muted">@productOption4@</span>');

    $Tab1.= $PHPShopGUI->setField('Опция E', $PHPShopGUI->setInputText('Имя:', 'option_5_name', $vendor['option_5_name'], 180, false, 'left') . '&nbsp;' . $PHPShopGUI->setSelect('option_5_format', checkSelect($vendor['option_5_format']), 100). '&nbsp;<span class="text-muted">@productOption5@</span>');


    $Tab2 = $PHPShopGUI->setField('Опция A', $PHPShopGUI->setInputText('Имя:', 'option_6_name', $vendor['option_6_name'], 180, false, 'left') . '&nbsp;' . $PHPShopGUI->setSelect('option_6_format', checkSelect($vendor['option_6_format']), 100). '&nbsp;<span class="text-muted">@catalogOption1@</span>');

    $Tab2.= $PHPShopGUI->setField('Опция B', $PHPShopGUI->setInputText('Имя:', 'option_7_name', $vendor['option_7_name'], 180, false, 'left') . '&nbsp;' . $PHPShopGUI->setSelect('option_7_format', checkSelect($vendor['option_7_format']), 100). '&nbsp;<span class="text-muted">@catalogOption2@</span>');

    $Tab2.= $PHPShopGUI->setField('Опция C', $PHPShopGUI->setInputText('Имя:', 'option_8_name', $vendor['option_8_name'], 180, false, 'left') . '&nbsp;' . $PHPShopGUI->setSelect('option_8_format', checkSelect($vendor['option_8_format']), 100). '&nbsp;<span class="text-muted">@catalogOption3@</span>');

    $Tab2.= $PHPShopGUI->setField('Опция D', $PHPShopGUI->setInputText('Имя:', 'option_9_name', $vendor['option_9_name'], 180, false, 'left') . '&nbsp;' . $PHPShopGUI->setSelect('option_9_format', checkSelect($vendor['option_9_format']), 100). '&nbsp;<span class="text-muted">@catalogOption4@</span>');

    $Tab2.= $PHPShopGUI->setField('Опция E', $PHPShopGUI->setInputText('Имя:', 'option_10_name', $vendor['option_10_name'], 180, false, 'left') . '&nbsp;' . $PHPShopGUI->setSelect('option_10_format', checkSelect($vendor['option_10_format']), 100). '&nbsp;<span class="text-muted">@catalogOption5@</span>');

    $Tab1 = $PHPShopGUI->setCollapse('Товары', $Tab1, 'in', false);
    $Tab1.= $PHPShopGUI->setCollapse('Каталоги', $Tab2);

    $info = 'Модуль позволяет добавить дополнительные поля для отображения в товарных позициях на сайте и при редактировании в карточке товара через закладку "Дополнительно". 
<p>        
Для вывода данных по товарам на сайте используются переменные <kbd>@productOption1@</kbd>, <kbd>@productOption2@</kbd>, <kbd>@productOption3@</kbd>, <kbd>@productOption4@</kbd>, <kbd>@productOption5@</kbd>, для каталогов используются переменные <kbd>@catalogOption1@</kbd>, <kbd>@catalogOption2@</kbd>, <kbd>@catalogOption3@</kbd>, <kbd>@catalogOption4@</kbd>, <kbd>@catalogOption5@</kbd>.  Сортировка наименования сотвествует сортировке вывода переменных в карточке редактирования сверху вниз. Переменные доступны в любом файле шаблонов продуктов <code>phpshop/templates/имя шаблона/product/</code> и шаблоне каталогов <code>phpshop/templates/имя шаблона/catalog/</code>.</p>  

Для доступа к значениям товаров через php функции используется конструкция:<br><br>
<code>
$PHPShopProduct = new PHPShopProduct(ИД товара);<br>
echo $PHPShopProduct->getParam("option1");<br>
echo $PHPShopProduct->getParam("option2");<br>
echo $PHPShopProduct->getParam("option3");<br>
echo $PHPShopProduct->getParam("option4");<br>
echo $PHPShopProduct->getParam("option5");<br>
</code>

Для доступа к значениям каталогов через php функции используется конструкция:<br><br>
<code>
$PHPShopCategory = new PHPShopCategory(ИД каталога);<br>
echo $PHPShopCategory->getParam("option6");<br>
echo $PHPShopCategory->getParam("option7");<br>
echo $PHPShopCategory->getParam("option8");<br>
echo $PHPShopCategory->getParam("option9");<br>
echo $PHPShopCategory->getParam("option10");<br>
</code>
';

    $Tab2 = $PHPShopGUI->setInfo($info);
    $Tab3 = $PHPShopGUI->setPay($serial = false, $pay = false, $data['version'], $update = true);


    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true), array("Описание", $Tab2), array("О Модуле", $Tab3));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий 
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>