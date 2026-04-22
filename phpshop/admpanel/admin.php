<?php
// Фрейм
if (isset($_GET['frame'])) {
    $isFrame = ' hidden ';
    $frameWidth = 'width:100%;';
    $isMobile = null;
} else {
    $isFrame = $frameWidtn = null;
    $isMobile = 'visible-xs';
}

session_start();
$_classPath = "../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass(array("base", "system", "admgui", "orm", "date", "xml", "security", "string", "parser", "mail", "lang"));

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopBase->chekAdmin();

// Системные настройки
$PHPShopSystem = new PHPShopSystem();
$_SESSION['lang'] = $PHPShopSystem->getSerilizeParam("admoption.lang_adm");
$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'admin'));
mb_internal_encoding($GLOBALS['PHPShopBase']->codBase);

// Конфигурация
$shop_type = (int) $PHPShopSystem->getParam("shop_type");

// Режим сайта
if ($shop_type == 2) {
    $hideSite = $hideCatalog = 'hide';
    $brand_type = 'Site ';
}

// Режим каталога
elseif ($shop_type == 1) {
    $hideCatalog = 'hide';
    $brand_type = 'Catalog ';
} else {
    $hideSite = $hideCatalog = $brand_type = null;
}

if (empty($_GET['path'])) {

    if (empty($hideSite))
        header('Location: ?path=intro');
    else
        header('Location: ?path=page.catalog');
}

$_SESSION['imageResultPath'] = $PHPShopSystem->getSerilizeParam('admoption.image_result_path');
$_SESSION['imageResultDir'] = $PHPShopBase->getParam('dir.dir');
if ($PHPShopSystem->ifSerilizeParam('admoption.dadata_enabled')) {
    $DADATA_TOKEN = $PHPShopSystem->getSerilizeParam('admoption.dadata_token');
    if (empty($DADATA_TOKEN))
        $DADATA_TOKEN = 'b13e0b4fd092a269e229887e265c62aba36a92e5';
} else
    $DADATA_TOKEN = null;

// Редактор GUI
$PHPShopGUI = new PHPShopGUI();
$PHPShopInterface = new PHPShopInterface();

// Модули
$PHPShopModules = new PHPShopModules($_classPath . "modules/");

/*
 *  Роутер
 */

// Меню модулей
$modulesMenu = modulesMenu();

// Подраздел [cat.sub]
if (strpos($_GET['path'], '.')) {
    $subpath = explode(".", $_GET['path']);

    // Редирект [cat.id]
    if (is_numeric($subpath[1])) {
        if ($subpath[0] == 'catalog')
            header('Location: ?path=' . $subpath[0] . '&cat=' . $subpath[1]);
        else if ($subpath[0] == 'sort')
            header('Location: ?path=' . $subpath[0] . '&cat=' . $subpath[1]);
        else
            header('Location: ?path=' . $subpath[0] . '&id=' . $subpath[1]);
    }
    else if (is_numeric($subpath[2])) {
        header('Location: ?path=' . $subpath[0] . '.' . $subpath[1] . '&cat=' . $subpath[2]);
    } else
        $loader_file = $subpath[0] . '/admin_' . $subpath[1] . '.php';
} else
    $subpath = array($_GET['path'], $_GET['path']);

if (!empty($_GET['path'])) {

    if (empty($_REQUEST['id'])) {

        $loader_file = $subpath[0] . '/admin_' . $subpath[1] . '.php';
    } else {

        $loader_file = $subpath[0] . '/adm_' . $subpath[1] . 'ID.php';
    }
    if (array_key_exists('action', $_REQUEST) and $_REQUEST['action'] == 'new') {
        $loader_file = $subpath[0] . '/adm_' . $subpath[1] . '_new.php';
    }
    $active_path = str_replace(".", "_", $_GET['path']);
    ${'menu_active_' . $active_path} = 'active';
}

$loader_function = 'actionStart';
if (file_exists($loader_file)) {

    if (empty($_REQUEST['id']) and empty($_REQUEST['action']))
        require_once($loader_file);
    else {
        ob_start();
        require_once($loader_file);
        $interface = ob_get_clean();
    }
}

// Подключение меню модулей
function modulesMenu() {
    global $notificationList, $hideSite, $hideCatalog;

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['modules']);
    if (!empty($_SESSION['mod_limit']))
        $mod_limit = intval($_SESSION['mod_limit']);
    else
        $mod_limit = 50;
    $data = $PHPShopOrm->select(array('*'), false, array('order' => 'date desc'), array('limit' => $mod_limit));
    $dis = $db = null;
    if (is_array($data))
        foreach ($data as $row) {
            $path = $row['path'];
            $menu = "../modules/" . $path . "/install/module.xml";
            $menu_array = xml2array($menu, false, true);
            $db_podmenu = $podmenu = null;
            $db = $menu_array["adminmenu"];

            if (!empty($_SESSION['support']) and $_SESSION['support'] < @$menu_array['sign'])
                continue;

            if (!empty($menu_array['pro']) and empty($_SESSION['mod_pro']))
                continue;

            // Скрытие модулей для сайта
            if (!empty($hideSite) and empty($menu_array['site']))
                continue;

            // Скрытие модулей для каталога
            if (!empty($hideCatalog) and empty($hideSite) and ( empty($menu_array['catalog']) and empty($menu_array['site'])))
                continue;

            if ($db['capability']) {

                if (array_key_exists('podmenu', $db) and is_array($db['podmenu'])) {
                    $dis .= '<li class="dropdown-submenu"><a href="?path=modules&id=' . $path . '">' . __($db['title']) . '</a>';
                    $dis .= '<ul class="dropdown-menu">';

                    if (!@is_array($db['podmenu'][0]))
                        $db_podmenu[0] = $db['podmenu'];
                    else
                        $db_podmenu = $db['podmenu'];

                    foreach ($db_podmenu as $podmenu)
                        $dis .= '<li><a href="?path=modules.' . $podmenu['podmenu_action'] . '">' . __($podmenu['podmenu_name']) . '</a></li>';

                    $dis .= '</ul></li>';
                } else
                    $dis .= '<li><a href="?path=modules&id=' . $path . '">' . __($db['title']) . '</a></li>';
            }

            // Notification
            if (!empty($db['notification'])) {
                $notificationList[] = $path;
            }

            // Redirect module.xml redirect.from -> redirect.to
            if (is_array($db))
                if (array_key_exists('redirect', $db) and is_array($db['redirect'])) {
                    if ($_GET['path'] == $db['redirect']['from'] and empty($_GET['id'])) {

                        // Сохранение GET параметров
                        parse_str($_SERVER['QUERY_STRING'], $source_query);
                        $source_query['path'] = 'modules.' . $db['redirect']['to'];

                        return header('Location: ?' . http_build_query($source_query));
                    }
                }
        }

    return $dis;
}

// Автостарт презентации
if (empty($_COOKIE['presentation']) or $_COOKIE['presentation'] == 'true')
    $presentation_checked = 'checked';
else
    $presentation_checked = null;

// Тема оформления
if (empty($_SESSION['admin_theme']))
    $theme = PHPShopSecurity::TotalClean($PHPShopSystem->getSerilizeParam('admoption.theme'));
else
    $theme = $_SESSION['admin_theme'];
if (!file_exists('./css/bootstrap-theme-' . $theme . '.css'))
    $theme = 'default';

$version = null;
$adm_title = $adm_brand = substr($PHPShopSystem->getSerilizeParam('admoption.adm_title'), 0, 70);
foreach (str_split($GLOBALS['SysValue']['upload']['version']) as $w)
    $version .= $w . '.';
$brand = 'PHPShop ' . $brand_type . substr($version, 0, 3);
if (empty($adm_title)) {
    $adm_title = 'PHPShop';
    $adm_brand = $brand;
}

// Fullscreen
if (!empty($_COOKIE['fullscreen'])) {
    $container = 'container-fluid';
    $fullscreen = 'glyphicon-retweet';
} else {
    $container = 'container';
    $fullscreen = 'glyphicon-fullscreen';
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['PHPShopLang']->code; ?>">
    <head>
        <meta charset="<?php echo $GLOBALS['PHPShopLang']->charset; ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $adm_title . ' - ' . PHPShopSecurity::TotalClean($TitlePage); ?></title>
        <meta name="author" content="PHPShop Software">
        <meta name="description" content="<?php echo $brand; ?>">
        <link rel="apple-touch-icon" href="./apple-touch-icon.png">
        <link rel="icon" href="./favicon.ico"> 

        <!-- Bootstrap -->
        <link id="bootstrap_theme" href="./css/bootstrap-theme-<?php echo $theme; ?>.css" rel="stylesheet">

        <!-- Preload -->
        <link rel="preload"  href="./css/bootstrap-toggle.min.css" as="style">
        <link rel="preload"  href="./css/jquery.dataTables.css" as="style">
        <link rel="preload"  href="./css/bootstrap-select.min.css" as="style">
        <link rel="preload"  href="./css/jquery.treegrid.css" as="style">
        <link rel="preload"  href="./css/admin.css" as="style">
        <link rel="preload"  href="./css/bar.css" as="style">
        <link rel="preload"  href="./css/bootstrap-tour.min.css" as="style">
        <link rel="preload"  href="./css/messagebox.min.css" as="style">
        <link rel="preload"  href="//fonts.googleapis.com/css?family=Open+Sans:300,400,700&display=swap&subset=cyrillic,cyrillic-ext" as="style">
    </head>

    <body role="document" id="body" data-token="<?php echo $DADATA_TOKEN; ?>">

        <!-- jQuery plugins -->
        <link href="./css/jquery.dataTables.css" rel="stylesheet">
        <link href="./css/bootstrap-select.min.css" rel="stylesheet">
        <link href="./css/jquery.treegrid.css" rel="stylesheet">
        <link href="./css/admin.css" rel="stylesheet">
        <link href="./css/bar.css" rel="stylesheet">
        <link href="./css/bootstrap-tour.min.css" rel="stylesheet">
        <link href="./css/messagebox.min.css" rel="stylesheet">
        <link href="./css/bootstrap-toggle.min.css" rel="stylesheet">

        <!-- jQuery -->
        <script src="js/jquery-1.11.0.min.js" data-rocketoptimized="false" data-cfasync="false"></script>

        <!-- Localization -->
        <script src="../locale/<?php echo $_SESSION['lang']; ?>/gui.js" data-rocketoptimized="false" data-cfasync="false"></script>

        <div class="<?php echo $container; ?>" style="<?php echo $frameWidth; ?>">

            <nav class="navbar navbar-default <?php echo $isFrame; ?>">
                <div>

                    <!-- Brand  -->
                    <div class="navbar-header">
                        <a class="navbar-brand" href="../../" title="<?php _e('Перейти в магазин'); ?>" target="_blank"><span class="glyphicon glyphicon-cog"></span> <?php echo $adm_brand ?></a>

                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar1" aria-expanded="false" aria-controls="navbar">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>

                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div id="navbar1" class="collapse navbar-collapse">
                        <ul class="nav navbar-nav navbar-right">
                            <li class="dropdown <?php echo $menu_active_modules; ?>">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php _e('Модули'); ?> <span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu" id="modules-menu">
                                    <li><a href="?path=modules"><span class="glyphicon glyphicon-tasks"></span> <?php _e('Управление модулями'); ?></a></li>
                                    <li class="divider"></li>
                                    <li class="dropdown-header"><?php _e('Установленные модули'); ?></li>
                                    <?php echo $modulesMenu; ?>
                                </ul>
                            </li>
                            <li class="dropdown <?php echo @$menu_active_system . @$menu_active_system_company . @$menu_active_system_seo . @$menu_active_system_sync . @$menu_active_tpleditor . @$menu_active_system_image . @$menu_active_system_servers . @$menu_active_system_integration . @$menu_active_system_warehouse . @$menu_active_company ?>">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php _e('Настройки'); ?> <span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="?path=system"><?php _e('Основные'); ?></a></li>
                                    <li class="dropdown-submenu"><a href="?path=system.company"><?php _e('Реквизиты'); ?></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="?path=company"><?php _e('Юридические лица'); ?></a></li>
                                        </ul>
                                    </li>
                                    <li class="<?php echo $hideSite; ?>"><a href="?path=system.sync"><?php _e('Обмен данными'); ?></a></li>
                                    <li><a href="?path=system.seo"><?php _e('SEO заголовки'); ?></a></li>
                                    <li class="<?php echo $hideSite; ?>"><a href="?path=system.currency"><?php _e('Валюты'); ?></a></li>
                                    <li><a href="?path=system.image"><?php _e('Изображения'); ?></a></li>
                                    <li><a href="?path=system.servers"><?php _e('Витрины'); ?></a></li>
                                    <li class="<?php echo $hideCatalog; ?>"><a href="?path=system.warehouse"><?php _e('Склады'); ?></a></li>
                                    <li><a href="?path=system.dialog"><?php _e('Диалоги'); ?></a></li>
                                    <li><a href="?path=system.integration"><?php _e('Интеграции'); ?></a>
                                    <li><a href="?path=system.yandexcloud"><span class="glyphicon glyphicon-star"></span> <?php _e('Yandex Cloud'); ?></a></li>

                                    <li><a href="?path=system.locale"><?php _e('Локализация'); ?></a></li>
                                    <li><a href="?path=system.service"><?php _e('Обслуживание'); ?></a></li>

                                    <li class="divider"></li>
                                    <li><a href="?path=tpleditor"><span class="glyphicon glyphicon-picture"></span> <?php _e('Шаблоны дизайна'); ?></a></li>
                                </ul>
                            </li>
                            <li class="dropdown <?php echo @$menu_active_exchange_export . @$menu_active_exchange_import . @$menu_active_exchange_sql . @$menu_active_exchange_backup . @$menu_active_exchange_service . @$menu_active_exchange_export_order . @$menu_active_exchange_export_user . @$menu_active_exchange_export_catalog . @$menu_active_exchange_import_order . @$menu_active_exchange_import_user . @$menu_active_exchange_import_catalog; ?>">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php _e('База'); ?> <span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li class="<?php echo $hideSite; ?>"><a href="?path=exchange.import"><span class="glyphicon glyphicon-import"></span> <?php _e('Импорт данных'); ?></a></li>
                                    <li class="<?php echo $hideSite; ?>"><a href="?path=exchange.export"><span class="glyphicon glyphicon-export"></span> <?php _e('Экспорт данных'); ?></a></li>
                                    <li class="divider <?php echo $hideSite; ?>"></li>

                                    <li class="dropdown-submenu">
                                        <a href="?path=exchange.service"><?php _e('Обслуживание'); ?></a>
                                        <ul class="dropdown-menu <?php echo $hideSite; ?>">
                                            <li><a href="?path=exchange.service"><?php _e('Очистка базы данных'); ?></a></li>
                                            <li><a href="?path=exchange.file"><?php _e('Проверка изображений'); ?></a></li>
                                            <li><a href="?path=product"><?php _e('Проверка артикулов'); ?></a></li>
                                            <li><a href="?path=product.uniqname"><?php _e('Проверка названий'); ?></a></li>
                                        </ul>
                                    </li>

                                    <li><a href="?path=exchange.sql"><?php _e('SQL запрос к базе'); ?></a></li>
                                    <li class="divider"></li>
                                    <li><a href="?path=exchange.backup"> <span class="glyphicon glyphicon-hdd"></span> <?php _e('Резервное копирование'); ?></a></li>
                                </ul>
                            </li>

                            <?php
                            if ($_SESSION['update'] == 1)
                                $update_style = 'text-success';
                            else if ($_SESSION['update'] == 2)
                                $update_style = 'text-warning';
                            else if ($_SESSION['update'] == 3)
                                $update_style = 'text-danger';
                            else
                                $update_style = null;
                            ?>

                            <li class="dropdown <?php echo @$menu_active_update . @$menu_active_update_restore . @$menu_active_system_about ?>">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php _e('Справка'); ?> <span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="?path=system.about"><?php _e('О программе'); ?></a></li>
                                    <li class="divider"></li>
                                    <li class="dropdown-submenu"><a href="https://wiki.phpshop.ru" target="_blank"><?php _e('Учебник'); ?></a>
                                        <ul class="dropdown-menu dropdown-menu-left">
                                            <li><a href="https://wiki.phpshop.ru" target="_blank"><?php _e('Учебник'); ?></a></li>
                                            <li><a href="https://docs.phpshop.ru" target="_blank"><?php _e('Учебник зеркало'); ?></a></li>
                                        </ul
                                    </li>
                                    
                                    
                                    <li><a href="?path=support"><?php _e('Техподдержка'); ?> <span class="glyphicon glyphicon-info-sign <?php echo $update_style; ?>"></span></a></li>
                                    <li class="<?php echo $hideSite; ?>"><a href="#" id="presentation-select"><?php _e('Обучение'); ?></a></li>
                                    <li class="divider"></li>

                                    <li><a href="?path=update"><span class="glyphicon glyphicon-cloud-download <?php echo $update_style; ?>"></span> <?php _e('Мастер обновления'); ?></a></li>

                                </ul>
                            </li>
                            <li class="divider"></li>
                            <li class="dropdown <?php echo @$menu_active_users . @$menu_active_users_jurnal . @$menu_active_users_stoplist; ?>">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><span class="glyphicon glyphicon-user hidden-xs"></span> <span class="visible-xs"><?php _e('Администратор'); ?> <span class="caret"></span></span><span class="caret  hidden-xs"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li class="dropdown-header"><?php
                            _e('Вошел как');
                            echo ' ' . $_SESSION['logPHPSHOP'];
                            ?></li>
                                    <li class="divider"></li>
                                    <li><a href="?path=users&id=<?php echo $_SESSION['idPHPSHOP']; ?>"><?php _e('Профиль'); ?></a></li>
                                    <li><a href="?path=users"><?php _e('Все администраторы'); ?></a></li>
                                    <li><a href="?path=users.jurnal"><?php _e('Журнал авторизации'); ?></a></li>
                                    <li class="divider"></li>
                                    <li><a href="./?logout"><span class="glyphicon glyphicon-transfer"></span> <?php _e('Выйти'); ?></a></li>
                                </ul>
                            </li>
                            <li><a href="#" title="<?php _e('Изменить размер'); ?>" class="setscreen hidden-xs"><span class="glyphicon <?php echo $fullscreen; ?>"></span></a></li>
                            <li><a href="../../" title="<?php _e('Перейти в магазин'); ?>" class="home go2front hidden-xs" target="_blank"><span class="glyphicon glyphicon-share-alt"></span></a></li>
                        </ul>
                    </div><!-- /.navbar-collapse -->
                </div>
            </nav>
            <nav class="navbar navbar-inverse navbar-statick <?php echo $isFrame; ?>">
                <div>

                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar2" aria-expanded="false" aria-controls="navbar">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>

                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div id="navbar2" class="collapse navbar-collapse">

                        <ul class="nav navbar-nav">
                            <li><a href="../../" title="><?php _e('Магазин'); ?>" target="_blank" class="visible-xs"><?php _e('Магазин'); ?></a></li>
                            <li class="<?php echo @$menu_active_intro; ?>"><a href="./admin.php" title="<?php _e('Домой'); ?>" class="home"><span class="glyphicon glyphicon-home hidden-xs"></span><span class="visible-xs"><?php _e('Домой'); ?></span></a></li>
                            <li class="dropdown <?php echo @$menu_active_order . @$menu_active_payment . @$menu_active_order_paymentlog . @$menu_active_order_status . @$menu_active_report_statorder . @$menu_active_report_statuser . @$menu_active_report_statpayment . @$menu_active_report_statproduct . $hideCatalog; ?>">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php _e('Заказы'); ?> <span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="?path=order"><span><?php _e('Заказы'); ?></span><span class="dropdown-header"><?php _e('Просмотр и оформление заказов, распечатка счетов'); ?></span></a></li>
                                    <li><a href="?path=order.paymentlog"><?php _e('Электронные платежи'); ?><span class="dropdown-header"><?php _e('Просмотр журнала оплаты заказов платежными системами'); ?></span></a></li>
                                    <li><a href="?path=payment"><?php _e('Способы оплаты'); ?><span class="dropdown-header"><?php _e('Просмотр, добавление и редактирование способов оплаты заказов'); ?></span></a></li>
                                    <li><a href="?path=order.status"><?php _e('Статусы заказов'); ?><span class="dropdown-header"><?php _e('Просмотр, добавление и редактирование статусов заказов'); ?></span></a></li>
                                    <li><a href="?path=delivery"><?php _e('Доставка'); ?><span class="dropdown-header"><?php _e('Просмотр и редактирование доставки. Настройка полей для заполнения заказа'); ?></span></a></li>
                                    <li class="divider"></li>
                                    <li><a href="?path=report.statorder"><span class="glyphicon glyphicon-stats"></span> <?php _e('Отчеты по продажам'); ?></a></li>
                                </ul>
                            </li>

                            <li class="dropdown <?php echo @$menu_active_catalog . @$menu_active_catalog_list . @$menu_active_product . @$menu_active_report_searchjurnal . @$menu_active_report_searchreplace . @$menu_active_sort . $hideSite; ?>" id="tour-product">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php _e('Товары'); ?> <span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="?path=catalog"><span><?php _e('Товары'); ?></span><span class="dropdown-header"><?php _e('Просмотр, добавление и редактирование товаров'); ?></span></a></li>
                                    <li><a href="?path=catalog.list"><span><?php _e('Каталоги'); ?></span><span class="dropdown-header"><?php _e('Просмотр, добавление и редактирование категорий товаров'); ?></span></a></li>
                                    <li><a href="?path=sort"><?php _e('Характеристики'); ?><span class="dropdown-header"><?php _e('Просмотр, добавление и редактирование дополнительных полей товаров'); ?></span></a></li>
                                    <li><a href="?path=sort.parent"><?php _e('Варианты подтипов'); ?><span class="dropdown-header"><?php _e('Просмотр, добавление и редактирование вариантов подтипов товаров'); ?></span></a></li>
                                    <li class="divider"></li>
                                    <li><a href="?path=report.searchjurnal"><span class="glyphicon glyphicon-sunglasses"></span> <?php _e('Журнал поиска товаров'); ?></a></li>
                                </ul>
                            </li>

                            <li class="dropdown <?php echo @$menu_active_shopusers . @$menu_active_shopusers_status . @$menu_active_shopusers_notice . @$menu_active_shopusers_comment . @$menu_active_dialog; ?>">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php _e('Пользователи'); ?> <span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="?path=shopusers"><?php _e('Пользователи'); ?><span class="dropdown-header"><?php _e('Список зарегистрированных пользователей'); ?></span></a></li>

                                    <li class="dropdown-submenu <?php echo $hideCatalog; ?>">
                                        <a href="?path=shopusers.status"><?php _e('Статусы и скидки'); ?><span class="dropdown-header"><?php _e('Управление статусами и скидками пользователей'); ?></span></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="?path=shopusers.status"><?php _e('Статусы пользователей'); ?><span class="dropdown-header"><?php _e('Управление накопительными скидками и статусами пользователей'); ?></span></a></li>
                                            <li><a href="?path=shopusers.discount"><?php _e('Скидки от заказа'); ?><span class="dropdown-header"><?php _e('Управление скидками от суммы заказа'); ?></span></a></li>
                                            <li><a href="?path=promotions"><span><?php _e('Промоакции'); ?></span><span class="dropdown-header"><?php _e('Промоакции и скидки'); ?></span></a></li>
                                        </ul>
                                    </li>
                                    <li class="<?php echo $hideCatalog; ?>"><a href="?path=shopusers.notice"><?php _e('Уведомления'); ?><span class="dropdown-header"><?php _e('Запросы от покупателей о поступлении товара на склад'); ?></span></a></li>
                                    <li class="<?php echo $hideSite; ?>"><a href="?path=shopusers.comment"><?php _e('Отзывы о товарах'); ?><span class="dropdown-header"><?php _e('Список отзывов для товаров, оставленные пользователями'); ?></span></a></li>
                                    <li class="divider <?php echo $hideSite; ?>"></li>
                                    <li class="<?php echo $hideSite; ?>"><a href="?path=dialog"><span class="glyphicon glyphicon-comment"></span> <?php _e('Диалоги с пользователями'); ?></a></li>
                                </ul>
                            </li>

                            <li class="dropdown <?php echo @$menu_active_menu . @$menu_active_gbook . @$menu_active_page_catalog . @$menu_active_page . @$menu_active_news . @$menu_active_news_rss . @$menu_active_photo_catalog; ?>">
                                <a href="#" class="dropdown-toggle " data-toggle="dropdown" role="button" aria-expanded="false"><?php _e('Веб-сайт'); ?> <span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="?path=page.catalog"><?php _e('Страницы'); ?><span class="dropdown-header"><?php _e('Создание и публикация страниц'); ?></span></a></li>
                                    <li><a href="?path=photo.catalog"><?php _e('Фотогалерея'); ?><span class="dropdown-header"><?php _e('Фотогалерея изображений и видео'); ?></span></a></li>
                                    <li><a href="?path=menu"><?php _e('Текстовые блоки'); ?><span class="dropdown-header"><?php _e('Вывод текстовых блоков в блок-меню'); ?></span></a></li>
                                    <li><a href="?path=gbook"><?php _e('Отзывы о сайте'); ?><span class="dropdown-header"><?php _e('Отзывы пользователей о сайте'); ?></span></a></li>
                                    <li><a href="?path=news"><?php _e('Новости'); ?><span class="dropdown-header"><?php _e('Новостная лента сайта'); ?></span></a></li>
                                </ul>
                            </li>

                            <li class="dropdown <?php echo @$menu_active_slider . @$menu_active_links . @$menu_active_banner . @$menu_active_opros . @$menu_active_metrica_traffic . @$menu_active_metrica_sources_summary . @$menu_active_metrica_sources_social . @$menu_active_metrica_sources_sites . @$menu_active_metrica_search_phrases . @$menu_active_metrica_search_engines . @$menu_active_metrica . @$menu_active_promotions . @$menu_active_lead_kanban . @$menu_active_report_customers . @$menu_active_report_goods; ?>" >
                                <a href="#" class="dropdown-toggle " data-toggle="dropdown" role="button" aria-expanded="false"><?php _e('Маркетинг'); ?> <span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li class="<?php echo $hideCatalog; ?>"><a href="?path=lead.kanban"><span><?php _e('Канбан доска'); ?></span><span class="dropdown-header"><?php _e('Управление заказами и задачами'); ?></span></a></li>
                                    <li class="<?php echo $hideCatalog; ?>"><a href="?path=promotions"><span><?php _e('Промоакции'); ?></span><span class="dropdown-header"><?php _e('Промоакции и скидки'); ?></span></a></li>
                                    <li><a href="?path=slider"><span><?php _e('Слайдер'); ?></span><span class="dropdown-header"><?php _e('Рекламный слайдер на главной странице'); ?></span></a></li>
                                    <li><a href="?path=news.sendmail"><?php _e('Рассылки'); ?><span class="dropdown-header"><?php _e('Создание рассылок пользователям'); ?></span></a></li>

                                    <li><a href="?path=banner"><?php _e('Баннеры и pop-up'); ?><span class="dropdown-header"><?php _e('Вывод баннеров и уведомлений'); ?></span></a></li>

                                    <li><a href="?path=metrica"><?php _e('Статистика посещений'); ?><span class="dropdown-header"><?php _e('Статистика посещей Яндекс.Метрика'); ?></span></a></li>

                                    <li class="divider"></li>
                                    <li class="dropdown-submenu <?php echo $hideCatalog; ?>">
                                        <a href="?path=report.customers"><span class="glyphicon glyphicon-equalizer"></span> <?php _e('Аналитика'); ?></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="?path=report.customers"><span><?php _e('Аналитика покупателей'); ?></span><span class="dropdown-header"><?php _e('Аналитические отчеты по клиентам'); ?></span></a></li>
                                            <li><a href="?path=report.goods"><?php _e('Аналитика товаров'); ?><span class="dropdown-header"><?php _e('Аналитические отчеты  по товарам'); ?></span></a></li>
                                        </ul
                                    </li>

                                </ul>
                            </li>
                        </ul>
                        <?php
                        if (empty($_GET['where']['name']))
                            $_GET['where']['name'] = null;

                        // Быстрый поиск
                        switch ($PHPShopSystem->getSerilizeParam('admoption.search_enabled')) {
                            case 1:
                                $search_class = 'hidden';
                                $search_id = $search_name = $search_placeholder = $search_action = $search_value = null;
                                break;

                            case 2:
                                $search_class = 'hidden-xs search-product';
                                $search_placeholder = __('Искать в заказах...');
                                $search_target = '_self';
                                $search_name = 'where[uid]';
                                $search_path = 'order';
                                $search_value = PHPShopSecurity::true_search($_GET['where']['name']);
                                break;

                            default:
                                $search_class = 'hidden-xs search-product';
                                $search_placeholder = __('Искать в товарах...');
                                $search_target = '_self';
                                $search_name = 'where[name]';
                                $search_path = 'catalog';
                                $search_value = PHPShopSecurity::true_search($_GET['where']['name']);
                                break;
                        }

                        if (!empty($hideSite))
                            $search_class = 'hidden';
                        ?>
                        <form class="navbar-right <?php echo $search_class; ?>"  action="<?php echo $search_action; ?>" target="<?php echo $search_target; ?>">
                            <div class="input-group">
                                <input name="<?php echo $search_name; ?>" maxlength="256" value="<?php echo $search_value; ?>" id="<?php echo $search_id; ?>" class="form-control input-sm" placeholder="<?php echo $search_placeholder; ?>" required="" type="search"  data-container="body" data-toggle="popover" data-placement="bottom" data-html="true"  data-content="">
                                <input type="hidden" name="path" value="<?php echo $search_path; ?>">
                                <input type="hidden" name="from" value="header">
                                <span class="input-group-btn">
                                    <button class="btn btn-default btn-sm" type="submit"><span class="glyphicon glyphicon-search"></span></button>
                                </span>
                            </div>
                        </form>
                        <div style="overflow:hidden">
                            <?php
// notification
                            $i_notif = 0;
                            if (empty($_SESSION['update_check']))
                                if (is_array($notificationList))
                                    foreach ($notificationList as $notification) {
                                        if ($i_notif < 3) {
                                            include_once($_classPath . 'modules/' . $notification . '/admpanel/notification.php');
                                            $notification_function = 'notification' . ucfirst($notification);
                                            if (function_exists($notification_function)) {
                                                call_user_func($notification_function);
                                            }
                                        }
                                        $i_notif++;
                                    }

// update
                            if (!empty($_SESSION['update_check']))
                                echo '<a class="navbar-btn btn btn-sm btn-info navbar-right hidden-xs" href="?path=update" data-toggle="tooltip" data-placement="bottom" title="' . __('Доступно обновление') . '" >Update <span class="badge">' . intval($_SESSION['update_check']) . '</span></a>';


// dialog
                            $dialog = $PHPShopBase->getNumRows('dialog', "where isview='0'");
                            if ($dialog > 99)
                                $dialog = 99;
                            
                            if($PHPShopSystem->getSerilizeParam('admoption.sound_off') != 1)
                                $play='play';
                            else $play='no-play';
                            ?>
                            <a class="navbar-btn btn btn-sm btn-primary navbar-right hidden-xs hide" href="?path=dialog"><?php _e('Диалоги'); ?> <span class="badge" id="dialog-check"><?php echo intval($dialog); ?></span></a><audio id="<?php echo $play;?>-chat" src="images/chat.mp3"></audio>

                            <?php
                            $order = $PHPShopBase->getNumRows('orders', "where statusi='0'");
                            ?>

                            <a class="navbar-btn btn btn-sm btn-warning navbar-right hidden-xs hidden-sm hide" href="?path=order&where[statusi]=0"><?php _e('Заказы'); ?> <span class="badge" id="<?php echo $hideCatalog; ?>orders-check"><?php if (empty($hideCatalog)) echo $order; ?></span>
                            </a><audio id="<?php echo $play;?>" src="images/message.mp3"></audio>

                            <?php
                            $notes = $PHPShopBase->getNumRows('notes', "where status='0'");
                            if ($notes > 99)
                                $notes = 99;
                            ?>

                            <a class="navbar-btn btn btn-sm btn-info navbar-right hidden-xs hide" href="?path=lead.kanban"><?php _e('Лиды'); ?> <span class="badge" id="notes-check"><?php echo $notes; ?></span>
                            </a><audio id="<?php echo $play;?>" src="images/message.mp3"></audio>
                        </div>

                    </div><!-- /.navbar-collapse -->
                </div>
            </nav>
            <div class="clearfix"></div>
            <a id="temp-color" class="hide"></a><a id="temp-color-selected" class="hide"></a>

            <?php
            if (file_exists($loader_file)) {
                if (empty($_REQUEST['id']) and empty($_REQUEST['action'])) {

                    if ($PHPShopBase->Rule->CheckedRules($subpath[0], 'view')) {
                        if (function_exists($loader_function))
                            call_user_func($loader_function);
                        else
                            _e('Функция ') . $loader_function . __('() не найдена в файле ') . $loader_file;
                    } else
                        $PHPShopBase->Rule->BadUserFormaWindow();
                } else
                    echo $interface;
            } else
                $PHPShopBase->Rule->BadUserFormaWindow();
            ?>
        </div>

        <!-- Notification -->
        <div id="notification" class="success-notification hide">
            <div  class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <span class="notification-alert"> </span>
            </div>
        </div>
        <!--/ Notification -->

        <!-- Presentation -->
        <div id="presentation" class="hide">
            <div id="presentation-content">
                <div class="panel panel-default">
                    <div class="panel-heading "><span class="glyphicon glyphicon-film text-primary"></span> <b class="text-primary"><?php _e('Урок 1: Создание товара'); ?></b>
                        <a class="btn btn-primary btn-xs pull-right" href="?path=product&return=catalog&action=new&video"><span class="glyphicon glyphicon-play"></span> <?php _e('Старт'); ?></a></div>
                    <div class="panel-body ">
                        <?php _e('Обучающий урок по созданию нового товара, заполнения полей и сохранения результата'); ?>.
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading"><span class="glyphicon glyphicon-film text-primary"></span> <b class="text-primary"><?php _e('Урок 2: Создание каталога'); ?></b>
                        <a class="btn btn-primary btn-xs pull-right" href="?path=catalog&action=new&video"><span class="glyphicon glyphicon-play"></span> <?php _e('Старт'); ?></a></div>
                    <div class="panel-body ">
                        <?php _e('Обучающий урок по созданию нового каталога товара, заполнения полей и сохранения результата'); ?>.
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading"><span class="glyphicon glyphicon-film text-primary"></span> <b class="text-primary"><?php _e('Урок 3: Редактор шаблонов'); ?></b>
                        <a class="btn btn-primary btn-xs pull-right" href="?path=tpleditor&name=<?php echo $PHPShopSystem->getParam('skin') ?>&file=/main/index.tpl&mod=html&video"><span class="glyphicon glyphicon-play"></span> <?php _e('Старт'); ?></a></div>
                    <div class="panel-body">
                        <?php _e('Обучающий урок по редактированию шаблона дизайна, описание переменных шаблонизатора, управление редактором кода'); ?>.
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading"><span class="glyphicon glyphicon-film text-primary"></span> <b class="text-primary"><?php _e('Урок 4: Настройки'); ?></b><a class="btn btn-primary btn-xs pull-right" href="?path=system"><span class="glyphicon glyphicon-play"></span> <?php _e('Старт'); ?></a>
                    </div>
                    <div class="panel-body">
                        <?php _e('Выбрать общий шаблон дизайна магазина можно в <a href="?path=system#4">Настройках дизайна</a>. Изменить цветовую тему оформления панели управления можно в  <a href="?path=system#7">Настройках управления</a>'); ?>.
                    </div>
                </div>
                <div class="checkbox text-muted">
                    <label>
                        <input type="checkbox" <?php echo $presentation_checked; ?> id="presentation-check">  <?php _e('Показывать при входе в панель управления'); ?>
                    </label>
                </div>
            </div>
        </div>
        <?php
        if (isset($_GET['video'])) {
            echo '<script>var video=true;</script>';
        }

        if ($_GET['path'] == 'intro' and $presentation_checked == 'checked' and ! PHPShopString::is_mobile() and empty($isSite))
            echo '<script>var presentation_start=true;</script>';

        if (PHPShopString::is_mobile())
            echo '<script>var is_mobile=true;</script>';
        ?>

        <!--/ Presentation -->


        <!-- Modal select -->
        <div class="modal" id="selectModal" tabindex="-1" role="dialog" aria-labelledby="selectModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form class="form-horizontal" role="form" data-toggle="validator" id="modal-form" method="post" enctype="multipart/form-data">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <h4 class="modal-title" id="selectModalLabel"></h4>
                        </div>
                        <div class="modal-body">

                            <?php if (!empty($selectModalBody)) echo $selectModalBody; ?>

                        </div>
                        <div class="modal-footer" >

                            <!-- Progress -->
                            <div class="progress hidden">
                                <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="5" aria-valuemin="0" aria-valuemax="100" style="width: 5%">
                                    <span class="sr-only">45% Complete</span>
                                </div>
                            </div>   
                            <!--/ Progress -->

                            <button type="button" class="btn btn-default btn-sm pull-left hidden btn-delete"><span class="glyphicon glyphicon-trash"></span> <?php _e('Удалить'); ?></button>
                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php _e('Отменить'); ?></button>
                            <button type="submit" class="btn btn-primary btn-sm"><?php _e('Применить'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--/ Modal select-->

        <!-- Modal filemanager -->
        <div class="modal bs-example-modal-lg" id="elfinderModal" tabindex="-1" role="dialog"  aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

                        <span class="btn btn-default btn-sm pull-left glyphicon glyphicon-fullscreen" id="filemanagerwindow" data-toggle="tooltip" data-placement="bottom" title="<?php _e('Увеличить размер'); ?>"></span>

                        <h4 class="modal-title"><?php _e('Найти файл'); ?></h4>
                    </div>
                    <div class="modal-body">
                        <iframe class="elfinder-modal-content" frameborder="0" marginheight="0" marginwidth="0" scrolling="no" data-path="image" data-option="return=icon_new"></iframe>

                    </div>
                </div>
            </div>
        </div>
        <!--/ Modal filemanager -->

        <!-- Modal product -->
        <div class="modal bs-example-modal-lg" id="adminModal" tabindex="-1" role="dialog"  aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">

                        <div class="pull-right">
                            <span class="btn btn-default btn-sm pull-left glyphicon glyphicon-fullscreen" id="filemanagerwindow" title="<?php _e('Увеличить размер'); ?>"></span>
                            <a class="btn btn-default btn-sm glyphicon glyphicon-eye-open" id="productlink" href="#" target="_blank" title="<?php _e('Предпросмотр'); ?>"></a>
                            <button class="btn btn-default btn-sm glyphicon glyphicon-remove" data-dismiss="modal" aria-label="Close" title="<?php _e('Закрыть'); ?>"></button> 
                        </div>

                        <h4 class="modal-title"><?php _e('Редактирование'); ?></h4>
                    </div>
                    <div class="modal-body" style="padding:0px">
                        <iframe name="adminModal" class="product-modal-content" frameborder="0" marginheight="0" marginwidth="0" scrolling="auto" width="100%"></iframe>

                    </div>
                </div>
            </div>
        </div>
        <!--/ Modal product  -->

        <!-- Modal external forms modules  -->
        <?php if (!empty($modalExternalModules)) echo $modalExternalModules; ?>
        <!--/ Modal external forms modules  -->

        <!-- Fixed mobile bar -->
        <div class="bar-padding-fix <?php echo $isMobile . $isFrame; ?>"> </div>
        <nav class="navbar navbar-statick navbar-fixed-bottom bar bar-tab visible-xs  <?php echo $isFrame; ?>" role="navigation">

            <?php
            if (empty($dialog))
                $dialog_mobile_check = 'hide';
            else
                $dialog_mobile_check = null;
            ?>

            <a class="tab-item <?php echo $menu_active_dialog; ?>" href="?path=dialog">
                <span class="icon icon-code"></span> <span class="badge badge-positive <?php echo $dialog_mobile_check; ?>" id="dialog-mobile-check"><?php echo $dialog; ?></span>
                <span class="tab-label"><?php _e('Диалоги'); ?></span>
            </a>
            <a class="tab-item <?php echo $menu_active_order . $hideCatalog; ?>" href="?path=order" id="bar-cart">
                <span class="icon icon-download"></span> <span class="badge badge-positive hide" id="orders-mobile-check"><?php echo $order; ?></span>
                <span class="tab-label"><?php _e('Заказы'); ?></span>
            </a>
            <a class="tab-item <?php echo $menu_active_catalogю . $hideSite; ?>" href="?path=catalog">
                <span class="icon icon-compose"></span>
                <span class="tab-label"><?php _e('Товары'); ?></span>
            </a>
            <a class="tab-item <?php echo $menu_active_shopusers; ?>"  href="?path=shopusers">
                <span class="icon icon-person"></span>
                <span class="tab-label"><?php _e('Пользователи'); ?></span>
            </a>
            <a class="tab-item" href="./?logout">
                <span class="icon icon-share"></span>
                <span class="tab-label"><?php _e('Выход'); ?></span>
            </a>
        </nav>
        <!--/ Fixed mobile bar -->

        <!-- jQuery plugins -->
        <script src="./js/bootstrap.min.js" data-rocketoptimized="false" data-cfasync="false"></script>
        <script src="./js/jquery.dataTables.min.js" data-rocketoptimized="false" data-cfasync="false"></script>
        <script src="./js/dataTables.bootstrap.js" data-rocketoptimized="false" data-cfasync="false"></script>
        <script src="./js/phpshop.js" data-rocketoptimized="false" data-cfasync="false"></script>
        <script src="./js/jquery.cookie.js" data-rocketoptimized="false" data-cfasync="false"></script>
        <script src="./js/jquery.form.js" data-rocketoptimized="false" data-cfasync="false"></script>
        <script src="./js/bootstrap-select.min.js" data-rocketoptimized="false" data-cfasync="false"></script>
        <script src="./js/messagebox.min.js" data-rocketoptimized="false" data-cfasync="false"></script>
        <script src="./js/bootstrap-toggle.js" data-rocketoptimized="false" data-cfasync="false"></script>
        <!--/ jQuery plugins -->

    </body>
</html>