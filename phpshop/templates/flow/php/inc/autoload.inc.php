<?php

$_SESSION['Memory']["rateForComment"]["oneStarWidth"] = 20; // ширина одной звёздочки
$_SESSION['Memory']["rateForComment"]["oneSpaceWidth"] = 0;
define("SkinName", "flow");

// Цветовые темы CSS
if (isset($_COOKIE[SkinName . '_theme'])) {
    if (PHPShopSecurity::true_skin($_COOKIE[SkinName . '_theme'])) {
        $GLOBALS['SysValue']['other'][SkinName . '_theme'] = $_COOKIE[SkinName . '_theme'];
    } else
        $GLOBALS['SysValue']['other'][SkinName . '_theme'] = 'bootstrap-theme-default';
} /* elseif (!empty($GLOBALS['SysValue']['other']['template_theme']))
  $GLOBALS['SysValue']['other']['bootstrap_theme'] = $GLOBALS['SysValue']['other']['template_theme']; */
elseif (empty($GLOBALS['SysValue']['other'][SkinName . '_theme'])) {
    $GLOBALS['SysValue']['other'][SkinName . '_theme'] = 'bootstrap-theme-default';
    setcookie(SkinName . '_theme', 'bootstrap-theme-default', time() + 360000, '/');
} else
    setcookie(SkinName . '_theme', $GLOBALS['SysValue']['other'][SkinName . '_theme'], time() + 360000, '/');

function create_theme_menu($file) {

    $current = $GLOBALS['SysValue']['other'][SkinName . '_theme'];
    if (empty($current))
        $current = 'bootstrap-theme-default';

    $color = array(
        'default' => '#377dff',
        'red' => '#ed4c78',
        'indigo' => '#2d1582',
        'cyant' => '#09a5be',
        'gray' => '#71869d',
        'green' => '#00c9a7',
        'dark' => '#21325b',
    );
    if (preg_match("/^bootstrap-theme-([a-zA-Z0-9_]{1,30}).css$/", $file, $match)) {
        $icon = $color[$match[1]];
        if (empty($icon))
            $icon = $match[1];

        if ($current == 'bootstrap-theme-' . $match[1])
            $check = '<span class="fa fa-check"></span>';
        else
            $check = null;

        return '<div class="bootstrap-theme text-center" style="background:' . $icon . '" title="' . $match[1] . '" data-random="?rand=' . time() . '" data-skin="bootstrap-theme-' . $match[1] . '">' . $check . '</div>';
    }
}

// Редактор тем оформления
if ($GLOBALS['SysValue']['template_theme']['user'] == 'true' or ! empty($GLOBALS['SysValue']['other']['skinSelect'])) {

    // CSS
    $PHPShopCssParser = new PHPShopCssParser($GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . '/css/' . $GLOBALS['SysValue']['other'][SkinName . '_theme'] . '.css');
    $css_parse = $PHPShopCssParser->parse();

    // XML
    PHPShopObj::loadClass(array('xml', 'admgui'));
    $PHPShopGUI = new PHPShopGUI();

    // bootstrap-
    $PHPShopGUI->addCSSFiles($GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . '/css/editor.css');
    $PHPShopGUI->addJSFiles($GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . '/js/editor-lite.js');

    $option = xml2array($GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . '/editor/style.xml', false, true);
    $css_edit .= $PHPShopGUI->includeJava . $PHPShopGUI->includeCss;

    $option['element'][] = $option['element'];
    if (is_array($option))
        foreach ($option['element'] as $id => $element) {

            if (!empty($element['var']))
                $element_var[0] = $element['var'];

            if (is_array($element_var))
                foreach ($element_var as $var) {

                    // Цвет
                    if ($var['type'] == 'color') {
                        $css_edit .= $PHPShopGUI->setField($element['description'], $PHPShopGUI->setInputColor($var['name'], str_replace(array('!important'), array(''), $css_parse[$element['name']][$var['name']]), 130, 'color-' . $id, $element['name']), 5, $element['content']);
                    }

                    // Фильтр
                    else if ($var['type'] == 'slider') {
                        $current_filter = $PHPShopCssParser->getParam($element['name'], '-editor-filter');

                        $filter = '<div id="color-slide" data-option="' . $current_filter . '"></div><input type="hidden" name="filter" class="color-filter color-value" value="' . $current_filter . '" data-option="' . $element['name'] . '" id="color-' . $id . '"> ';
                        $css_edit .= $PHPShopGUI->setField($element['description'], $filter, 5, $element['content']);
                    }

                    // Тема
                    else if ($var['type'] == 'theme') {

                        if (!empty($_COOKIE['bootstrap_theme']) and $_COOKIE['bootstrap_theme'] == 'bootstrap')
                            $check = '<span class="fa fa-check"></span>';
                        else
                            $check = null;



                        $theme .= PHPShopFile::searchFile($GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . '/css/', 'create_theme_menu');
                        $css_edit .= $PHPShopGUI->setField($element['description'], $theme, 3, $element['content'], 'row');
                    }
                }
        }

    // Сохранить
    if (!empty($_SESSION['logPHPSHOP'])) {
        $css_edit .= '<br>' . $PHPShopGUI->setButton('Сохранить', 'floppy-disk', 'btn-xs btn-primary saveTheme');
        $admin_edit .= $PHPShopGUI->setButton('Управлять', 'cog', 'btn-xs btn-primary openAdminModal');

        if (!empty($_COOKIE['debug_template']))
            $debug_active = ' active ';
        else
            $debug_active = null;

        $css_edit .= $PHPShopGUI->setButton('Отладка шаблона', 'picture', 'btn-xs btn-primary setDebug' . $debug_active);
    }

    // Память вывода панели
    if (!empty($_COOKIE['style_selector_status'])) {
        if ($_COOKIE['style_selector_status'] == 'enabled') {
            $editor['right'] = 0;
            $editor['close'] = 'ss-close';
        } else {
            $editor['right'] = -280;
            $editor['close'] = false;
        }
    } else if ($GLOBALS['SysValue']['template_theme']['demo'] == 'true' and ! PHPShopString::is_mobile()) {
        $editor['right'] = 0;
        $editor['close'] = 'ss-close';
    } else {
        $editor['right'] = -280;
        $editor['close'] = false;
    }

    // Память коллапса
    $collapseCSS = $collapseAdmin = null;
    unset($_COOKIE['style_collapse_collapseCSS']);
    unset($_COOKIE['style_collapse_collapseAdmin']);
    if (isset($_COOKIE['style_collapse_collapseCSS'])) {
        $collapseCSS = null;
        $collapseIconCSS = 'fa-chevron-down';
    } else {
        $collapseCSS = 'show';
        $collapseIconCSS = 'fa-chevron-up';
    }
    if (isset($_COOKIE['style_collapse_collapseAdmin'])) {
        $collapseAdmin = null;
        $collapseIconAdmin = 'fa-chevron-down';
    } else {
        $collapseAdmin = 'show';
        $collapseIconAdmin = 'fa-chevron-up ';
    }

    //if ($collapseCSS == $collapseAdmin)
    //$collapseAdmin = null;

    if (!empty($_SESSION['logPHPSHOP']))
        $admin_help = __('Вы можете управлять содержанием текущей страницы');
    else if ($GLOBALS['SysValue']['template_theme']['demo'] == 'true')
        $admin_help = __('Для управления текущей страницей требуется') . ' <a href="https://www.phpshop.ru/mydemo/" target="_blank">' . __('авторизоваться') . '</a>';
    else
        $admin_help = __('Для управления текущей страницей требуется') . ' <a href="//' . $_SERVER['SERVER_NAME'] . $GLOBALS['SysValue']['dir']['dir'] . 'phpshop/admpanel/" target="_blank">' . __('авторизоваться') . '</a>';

    // Выбор БД
    if (is_array($GLOBALS['SysValue']['connect_select'])) {
        foreach ($GLOBALS['SysValue']['connect_select'] as $k => $v) {
            if ($_SESSION['base'] == $k)
                $sel = "selected";
            else
                $sel = null;
            $connect_select[] = array($v, $k, $sel);
        }

        $forma = PHPShopText::div(PHPShopText::form(PHPShopText::select('base', $connect_select, '100%', $float = "none", $caption = false, $onchange = "javascript:document.BdForm.submit()"), 'BdForm', 'get', '/'), 'left', 'padding:10px');
        PHPShopParser::set('leftMenuContent', $forma);
        PHPShopParser::set('leftMenuName', __("Сменить базу"));
        $GLOBALS['SysValue']['other']['skinSelect'] .= PHPShopParser::file($GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . chr(47) . $GLOBALS['SysValue']['templates']['left_menu'], true, true, false);
    }

    $collapse_menu = '<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
  <div class="panel panel-default card">
    <div class="panel-heading card-header" role="tab">
      <div class="panel-title card-title">
        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseCSS" aria-expanded="true" aria-controls="collapseOne">
          ' . __('Оформление') . ' <span class="fa ' . $collapseIconCSS . ' float-right" data-parent="collapseCSS"></span>
        </a>
      </div>
    </div>
    <div id="collapseCSS" class="panel-collapse collapse ' . $collapseCSS . ' form-horizontal" role="tabpanel">
      <div class="panel-body card-body">
     
         ' . $css_edit . $theme_menu . '

      </div>
    </div>
  </div>
  <div class="panel panel-default card">
    <div class="panel-heading hidde card-header " role="tab" id="adminModalHelp">
      <div class="panel-title card-title">
        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseAdmin" aria-expanded="false" aria-controls="collapseTwo">
          ' . __('Управление') . ' <span class="fa ' . $collapseIconAdmin . ' float-right" data-parent="collapseAdmin"></span>
        </a>
      </div>
    </div>
    <div id="collapseAdmin" class="panel-collapse collapse ' . $collapseAdmin . '" role="tabpanel">
      <div class="panel-body card-body">
      <p class="text-muted">' . $admin_help . '</p>
' . $admin_edit . '
      </div>
    </div>
  </div>
</div>';

    // Редактор CSS
    $theme_menu = '
        <div id="style-selector" style="width: 280px; right: ' . $editor['right'] . 'px;" class="hidden-xs hidden-sm">
        <div class="fa style-toggle ' . $editor['close'] . '" title="' . __('Панель оформления') . '"></div>
           <div id="style-selector-container">
              <div class="style-selector-wrapper">
              ' . $GLOBALS['SysValue']['other']['skinSelect'] . $collapse_menu . '
              </div>
           </div>
        </div>';

    // Редактор БД
    $edit_frame = ' <!-- Modal admin -->
        <div class="modal bs-example-modal-lg" id="adminModal" tabindex="-1" role="dialog"  aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <div class="modal-title">
                        <a class="h4" id="editorwindow" href="#" data-toggle="tooltip" data-placement="bottom" title="{Увеличить размер}">{Панель управления}</a>
                        </div>
                    </div>
                    <div class="modal-body">
                      
                        <iframe class="admin-modal-content" id="admin-modal" frameborder="0" marginheight="0" marginwidth="0" scrolling="auto" width="100%" height="600"></iframe>
                        <div style="height:30px">
                         <!-- Progress -->
                            <div class="progress" style="margin:0px 5px 3px 5px">
                                <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 20%">
                                    <span class="sr-only">45% Complete</span>
                                </div>
                            </div>   
                            <!--/ Progress -->
                         </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Modal admin -->';


    if (!empty($GLOBALS['SysValue']['other']['skinSelect']))
        $GLOBALS['SysValue']['other']['editor'] = $theme_menu . $edit_frame;
}


// Меню брендов
$PHPShopBrandsElement = new PHPShopBrandsElement();
$PHPShopBrandsElement->init('topBrands');

// Верхнее меню категорий
$PHPShopShopCatalogElement = new PHPShopShopCatalogElement();

// Меняем имена шаблонов каталога для @topCatal@
$PHPShopShopCatalogElement->setValue('sys.multimenu', false);
$PHPShopShopCatalogElement->setValue('templates.catalog_forma', 'catalog/top_catalog_forma.tpl');
$PHPShopShopCatalogElement->setValue('templates.catalog_forma_3', 'catalog/top_catalog_forma_3.tpl');
$PHPShopShopCatalogElement->setValue('templates.podcatalog_forma', 'catalog/top_podcatalog_forma.tpl');
$PHPShopShopCatalogElement->set('topCatal', $PHPShopShopCatalogElement->leftCatal());

// Возвращаем имена шаблонов каталога для @leftCatal@
$PHPShopShopCatalogElement->setValue('templates.catalog_forma', 'catalog/catalog_forma.tpl');
$PHPShopShopCatalogElement->setValue('templates.catalog_forma_3', 'catalog/catalog_forma_3.tpl');
$PHPShopShopCatalogElement->setValue('templates.podcatalog_forma', 'catalog/podcatalog_forma.tpl');
$PHPShopShopCatalogElement->setValue('sys.multimenu', true);
?>