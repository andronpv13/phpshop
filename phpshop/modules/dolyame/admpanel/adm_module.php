<?php

PHPShopObj::loadClass('order');
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("category");
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.dolyame.dolyame_system"));

/**
 * Обновление версии модуля
 * @return mixed
 */
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;

    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

/**
 * Обновление настроек
 * @return mixed
 */
function actionUpdate() {
    global $PHPShopModules, $PHPShopOrm;

    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    // Категории
    if (is_array($_POST['categories']) and $_POST['categories'][0] != 'null') {

        $cat_array = array();
        foreach ($_POST['categories'] as $v)
            if (!empty($v) and ! strstr($v, ','))
                $cat_array[] = $v;

        if (is_array($cat_array)) {
            $where = array('category' => ' IN ("' . implode('","', $cat_array) . '")');
            $PHPShopOrmProducts = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
            $PHPShopOrmProducts->debug = false;

            $PHPShopOrmProducts->update(array('dolyame_enabled_new' => (int) $_POST['enabled_all']), $where);
        }
    }

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);

    return $action;
}

// Построение дерева категорий
function treegenerator($array, $i, $curent, $dop_cat_array) {
    global $tree_array;
    $del = '&brvbar;&nbsp;&nbsp;&nbsp;&nbsp;';
    $tree_select = $tree_select_dop = $check = false;

    $del = str_repeat($del, $i);
    if (is_array($array['sub'])) {
        foreach ($array['sub'] as $k => $v) {

            $check = treegenerator($tree_array[$k], $i + 1, $k, $dop_cat_array);

            $selected = null;
            $disabled = null;

            if (is_array($dop_cat_array))
                foreach ($dop_cat_array as $vs) {
                    if ($k == $vs)
                        $selected = "selected";
                }

            if (empty($check['select'])) {
                $tree_select .= '<option value="' . $k . '" ' . $selected . $disabled . '>' . $del . $v . '</option>';

                $i = 1;
            } else {
                $tree_select .= '<option value="' . $k . '" ' . $selected . ' disabled>' . $del . $v . '</option>';
            }

            $tree_select .= $check['select'];
        }
    }
    return array('select' => $tree_select);
}

/**
 * Отображение настроек модуля
 * @return bool
 */
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $PHPShopSystem;

    // Выборка
    $data = $PHPShopOrm->select();
    $PHPShopGUI->addJSFiles('../modules/dolyame/admpanel/gui/dolyame.gui.js');

    $PHPShopGUI->field_col = 4;

    $Tab1 = $PHPShopGUI->setField('Логин API магазина', $PHPShopGUI->setInputText(false, 'login_new', $data['login'], 300));
    $Tab1 .= $PHPShopGUI->setField('Пароль API магазина', $PHPShopGUI->setInput("password", 'password_new', $data['password'], false, 300));
    $Tab1 .= $PHPShopGUI->setField('Site ID', $PHPShopGUI->setInputText(false, 'site_id_new', $data['site_id'], 100),1,'Интеграционный сниппет Долями');
    $Tab1 .= $PHPShopGUI->setField('Максимальная сумма', $PHPShopGUI->setInputText(false, 'max_sum_new', $data['max_sum'], 100, $PHPShopSystem->getValutaIcon()));

    // Доступые статусы заказов
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('Новый заказ'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status) {
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);
            $order_status_payment_value[] = array($order_status['name'], $order_status['id'], $data['status_payment']);
        }

    // Статус заказа
    $Tab1 .= $PHPShopGUI->setField('Оплата при статусе', $PHPShopGUI->setSelect('status_new', $order_status_value, 300));
    $Tab1 .= $PHPShopGUI->setField('Статус после оплаты', $PHPShopGUI->setSelect('status_payment_new', $order_status_payment_value, 300));

    $PHPShopCategoryArray = new PHPShopCategoryArray($where);
    $CategoryArray = $PHPShopCategoryArray->getArray();
    $GLOBALS['count'] = count($CategoryArray);

    $tree_array = array();

    foreach ($PHPShopCategoryArray->getKey('parent_to.id', true) as $k => $v) {
        foreach ($v as $cat) {
            $tree_array[$k]['sub'][$cat] = $CategoryArray[$cat]['name'];
        }
        $tree_array[$k]['name'] = $CategoryArray[$k]['name'];
        $tree_array[$k]['id'] = $k;
        if ($k == $data['parent_to'])
            $tree_array[$k]['selected'] = true;
    }

    $GLOBALS['tree_array'] = &$tree_array;

    if (is_array($tree_array[0]['sub']))
        foreach ($tree_array[0]['sub'] as $k => $v) {
            $check = treegenerator($tree_array[$k], 1, $k, $dop_cat_array);

            // Допкаталоги
            $selected = null;
            if (is_array($dop_cat_array))
                foreach ($dop_cat_array as $vs) {
                    if ($k == $vs)
                        $selected = "selected";
                }


            if (empty($tree_array[$k]))
                $disabled = null;
            else
                $disabled = ' disabled';

            $tree_select .= '<option value="' . $k . '"  ' . $selected . $disabled . '>' . $v . '</option>';

            $tree_select .= $check['select'];
        }


    $tree_select = '<select class="selectpicker show-menu-arrow hidden-edit" data-live-search="true" data-container="body" data-style="btn btn-default btn-sm" name="categories[]"  data-width="100%" multiple>' . $tree_select . '</select>';

    $Tab1 = $PHPShopGUI->setCollapse('Настройки', $Tab1);
    $Tab1 .= $PHPShopGUI->setCollapse('Товары', $PHPShopGUI->setField("Размещение", $tree_select . $PHPShopGUI->setCheckbox("categories_all", 1, "Выбрать все категории?", 0), 1, 'Пакетное редактирование. Настройка не сохраняется.') . $PHPShopGUI->setField("Рассрочка доступна", $PHPShopGUI->setRadio("enabled_all", 1, "Вкл.", 1) . $PHPShopGUI->setRadio("enabled_all", 0, "Выкл.", 1)));


    // Инструкция
    $info = '
        <h4>Настройка модуля</h4>
        <ol>
<li>Предоставить необходимые документы и заключить договор с <a href="https://dolyame.ru/business/?utm_medium=ptr.act&utm_campaign=sme.partners&partnerId=5-IV4AJGWE&agentId=5-IVFOVQCN&agentSsoId=14373cd1-2b09-4747-bae6-299a0229aedc&utm_source=partner_rko_a_sme" target="blank">Долями</a>.</li>
<li>На закладке настройки модуля ввести предоставленные Долями <kbd>Логин API магазина</kbd> и <kbd>Пароль API магазина</kbd>.</li>
<li>Выбрать статус заказа для оплаты и статус заказа после оплаты.</li>
<li>Указать максимальную сумму заказа для оплаты Долями (по умолчанию 30 000 руб).</li>
<li>Загрузить в папку <code>phpshop/modules/dolyame/cert/</code> сертификаты, полученные в личном кабинете сервиса Долями. Переименовать файл *.pem в <code>certificate.pem</code>, файл *.key в <code>private.key</code></li>
</ol>

<h4>Настройка дизайна</h4>
 <ol>
   <li>Для подключения карточного сниппета Долями следует на закладке настройки модуля ввести предоставленный Долями <kbd>Site ID</kbd>. Сниппет разрабатывается и встраивается сотрудниками сервиса Долями. Для внедрения сниппета следует обратиться в партнерский отдел Долями <code>partners@dolyame.ru</code>. Время внедрения сниппета составляет несколько недель.</li>
   <li>Для отображения в карточках товара кнопки быстрого заказа Долями или сниппета используется переменная <code>@dolyame_product@</code>. </li>
   <li>При заполнении поля "Site ID" отображается сниппет Долями после встраивания его сотрудниками сервиса Долями. Кнопка быстрого заказа Долями отображается при незаполненном поле "Site ID" и статусе оплаты "Новый заказ". Для работы быстрого заказа Долями необходимо включить режим автоматического подтверждения заказа в личном кабинете сервиса Долями.</li>
 </ol>
';

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Настройки", $Tab1, true, false, true), array("Инструкция", $PHPShopGUI->setInfo($info)), array("О Модуле", $PHPShopGUI->setPay(false, false, $data['version'], true)));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart');
