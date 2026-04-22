<?php

function template_CID_Product($obj, $data, $rout) {
    if ($rout == 'START') {

        // Фасетный фильтр
        $obj->sort_template = 'sorttemplatehook';

        // Виртуальные каталоги
        $obj->cat_template = 'sortсattemplatehook';

        if (empty($_GET['gridChange']))
            $_GET['gridChange'] = null;

        if (empty($_GET['s']))
            $_GET['s'] = null;

        if (empty($_GET['f']))
            $_GET['f'] = null;

        switch ($_GET['gridChange']) {
            case 1:
                $obj->set('gridSetAactive', 'active');
                break;
            case 2:
                $obj->set('gridSetBactive', 'active');
                break;
            default: if ($obj->cell == 1)
                    $obj->set('gridSetAactive', 'active');
                else
                    $obj->set('gridSetBactive', 'active');
        }


        switch ($_GET['s']) {
            case 1:
                $obj->set('sSetAactive', 'active');
                break;
            case 2:
                $obj->set('sSetBactive', 'active');
                if ((int) $_GET['f'] === 1) {
                    $obj->set('unitPriceLowActive', 'active');
                    $obj->set('unitPriceLowChecked', 'checked="checked"');
                } else {
                    $obj->set('unitPriceHighActive', 'active');
                    $obj->set('unitPriceHighChecked', 'checked="checked"');
                }
                break;
            default:
                $obj->set('unitPopularActive', 'active');
                $obj->set('unitPopularChecked', 'checked="checked"');
                $obj->set('sSetCactive', 'active');
        }


        switch ($_GET['f']) {
            case 1:
                $obj->set('fSetAactive', 'active');
                break;
            case 2:
                $obj->set('fSetBactive', 'active');
                break;
            //default: $obj->set('fSetAactive', 'active');
        }
    }
}

/**
 * Вывод подтипов в подробном описании 
 */
function template_parent($obj, $dataArray, $rout) {

    if ($rout == 'END') {

        $currency = $obj->currency;

        $true_color_array = $true_size_color_array = $color_array = array();
        $size = $color = null;

        if (is_array($obj->select_value) and count($obj->select_value) > 0) {

            foreach ($obj->select_value as $value) {

                $row = $value[3];
                if (!empty($row['parent_enabled'])) {

                    $row['price_n'] = number_format($obj->price($row, true), $obj->format, '.', ' ');
                    $row['price'] = number_format($obj->price($row), $obj->format, '.', ' ');

                    // Цена для YML ?option=ID
                    if (!empty($_GET['option'])) {
                        if ($value[1] == $_GET['option'])
                            $obj->set('productPrice', $row['price']);
                    } else
                        $obj->set('productPrice', number_format($obj->price($dataArray), $obj->format, '.', ' '));

                    $obj->set('productValutaName', $currency);
                    $obj->set('parentName', $value[0]);
                    $obj->set('parentCheckedId', $value[1]);

                    // Единица измерения
                    if (empty($row['ed_izm']))
                        $row['ed_izm'] = $obj->lang('product_on_sklad_i');

                    $size_color_array[$value[3]['id']] = array('id' => $row['id'], 'size' => $row['parent'], 'price' => $row['price'], 'color' => array($row['parent2']), 'image' => $row['pic_small'], 'price_n' => $row['price_n'], 'items' => $row['items'], 'ed_izm' => $row['ed_izm']);

                    if (!empty($value[3]['color']))
                        $color_array[$value[3]['parent2']] = $value[3]['color'];
                    else if (!empty($value[3]['parent2']))
                        $color_array[$value[3]['parent2']] = PHPShopString::getColor($value[3]['parent2']);
                }
            }


            if (is_array($size_color_array)) {
                foreach ($size_color_array as $v) {

                    if (empty($true_size_color_array[$v['size']])) {
                        $true_size_color_array[$v['size']] = $v;
                    } else {
                        $true_size_color_array[$v['size']]['color'][] = $v['color'][0];
                    }
                }
            }


            if (is_array($true_size_color_array) and count($true_size_color_array) > 0) {
                $parentSizeEnabled = true;
                foreach ($true_size_color_array as $key => $val) {

                    // Размер
                    if (empty($key)) {
                        $obj->set('parentSizeHide', 'hide hidden');
                        $obj->set('parentSizeChecked', 'checked');
                        $parentSizeEnabled = false;
                    } else {
                        $obj->set('parentSizeChecked', null);
                    }

                    $obj->set('parentSize', $key);
                    $obj->set('parentId', $val['id']);
                    $obj->set('parentPrice', $val['price']);
                    $obj->set('parentImage', $size_color_array[$val['id']]['image']);

                    // Дополнительнеы склады
                    if ($obj->PHPShopSystem->isDisplayWarehouse()) {
                        $warehouse = [];

                        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['warehouses']);

                        $where = [];
                        $where['enabled'] = "='1'";

                        if (defined("HostID") or defined("HostMain")) {

                            if (defined("HostID"))
                                $where['servers'] = " REGEXP 'i" . HostID . "i'";
                            elseif (defined("HostMain"))
                                $where['enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';
                        }

                        $data = $PHPShopOrm->select(array('*'), $where, array('order' => 'num'), array('limit' => 100));
                        if (is_array($data))
                            foreach ($data as $row) {
                                if (!empty($row['description']))
                                    $warehouse[$row['id']] = $row['description'];
                                else
                                    $warehouse[$row['id']] = $row['name'];
                            }

                        if (is_array($warehouse) and count($warehouse) > 0) {
                            $items = null;

                            $itemsData = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['*'], ['id' => '=' . (int) $val['id']]);

                            if (empty($itemsData['ed_izm']))
                                $itemsData['ed_izm'] = __('шт.');

                            // Общий склад
                            if ($obj->PHPShopSystem->getSerilizeParam('admoption.sklad_sum_enabled') == 1)
                                $items = __('Общий склад') . ": " . $itemsData['items'] . " " . $itemsData['ed_izm'].PHPShopText::br();

                            foreach ($warehouse as $store_id => $store_name) {
                                if (isset($itemsData['items' . $store_id])) {
                                    $items .= $store_name . ": " . $itemsData['items' . $store_id] . " " . $itemsData['ed_izm'].PHPShopText::br();
                                }
                            }
                        } else
                            $items = $obj->PHPShopBase->SysValue['lang']['product_on_sklad'] . " " . $val['items'] . " " . $val['ed_izm'];
                    } else
                        $items = null;
                    $obj->set('parentItems',$items);

                    if ((float) $size_color_array[$val['id']]['price_n'] > 0)
                        $obj->set('parentPriceOld', $size_color_array[$val['id']]['price_n']);
                    else
                        $obj->set('parentPriceOld', '');


                    $size .= ParseTemplateReturn("product/product_odnotip_product_parent_one.tpl");

                    // Цвет
                    foreach ($val['color'] as $colors) {
                        $true_color_array[$colors][] = $val['id'];
                    }
                }
            }

            if (is_array($color_array)) {

                foreach ($color_array as $true_name => $true_colors) {
                    $obj->set('parentColor', $true_colors);
                    $obj->set('parentName', $true_name);
                    $id = null;

                    if (is_array($true_color_array[$true_name]))
                        foreach ($true_color_array[$true_name] as $ids) {
                            $id .= ' select-color-' . $ids;
                        }

                    $obj->set('parentColorId', $id);

                    // Цвет
                    if (!empty($true_colors)) {
                        $color .= ParseTemplateReturn("product/product_odnotip_product_parent_one_color.tpl");
                    }
                    // Параметр
                    else {
                        $color .= ParseTemplateReturn("product/product_odnotip_product_parent_one_value.tpl");
                    }
                }
            }

            // Отладка
            /*
              print_r($true_size_color_array);
              print_r($true_color_array);
              print_r($color_array);
             */

            if ($parentSizeEnabled)
                $obj->set('parentListSizeTitle', $obj->parent_title);

            $obj->set('parentListSize', $size, true);

            if (!empty($color))
                $obj->set('parentListColorTitle', $obj->parent_color);

            $obj->set('parentListColor', $color, true);
            $obj->set('parentSizeMessage', $obj->lang('select_size'));

            // Наличие
            if (!$obj->get('elementCartOptionHide'))
                $obj->set('elementCartOptionHide', null);

            $obj->set('ComStartCart', null);
            $obj->set('ComEndCart', null);
            //$obj->set('productParentJson',json_safe_encode($true_color_array));
            $obj->set('productParentList', ParseTemplateReturn("product/product_odnotip_product_parent.tpl"));
        }
    }
}

function template_UID($obj, $dataArray, $rout) {
    if ($rout == 'MIDDLE') {
        if ($obj->get('optionsDisp') != '' and $obj->get('parentList') == '') {
            $obj->set('ComStartCart', '<!--');
            $obj->set('ComEndCart', '-->');
            $obj->set('optionsDisp', ParseTemplateReturn("product/product_option_product.tpl"));
        }
        // Спецпредложения
        if (!empty($dataArray['spec']))
            $obj->set('specIcon', ParseTemplateReturn('product/specIcon.tpl'));
        else
            $obj->set('specIcon', '');

        // Новинки
        if (!empty($dataArray['newtip']))
            $obj->set('newtipIcon', ParseTemplateReturn('product/newtipIcon.tpl'));
        else
            $obj->set('newtipIcon', '');

        $cart = new PHPShopCart();

        if ($cart->isItemInCart($dataArray['id'])) {
            $obj->set('unitProductSale', $obj->lang('productSaleReady') . '<span class="icons icons-incart"></span>');
        } else {
            $obj->set('unitProductSale', $obj->lang('product_sale') . '<span class="icons icons-cart"></span>');
        }
    }
}

/**
 * Шаблон вывода характеристик
 */
function sorttemplatehook($value, $n, $title, $vendor) {
    $limit = 5;
    $disp = null;
    $num = 0;

    if (empty($GLOBALS['filter_count']))
        $GLOBALS['filter_count'] = 1;

    if (is_array($value)) {
        foreach ($value as $p) {

            $text = $p[0];
            $checked = null;
            if (is_array($vendor)) {
                foreach ($vendor as $sortId => $v) {
                    if (is_array($v)) {
                        foreach ($v as $s)
                            if ($s == $p[1])
                                $checked = 'checked';
                    } else {
                        if ($n == $sortId && $p[1] == $v)
                            $checked = 'checked';
                    }
                }
            }


            // Определение цвета
            if ($text[0] == '#')
                $text = '<div class="filter-color" style="background:' . $text . '"></div>';

            $disp .= '<div class="checkbox">
  <label>
    <input type="checkbox" value="1" name="' . $n . '-' . $p[1] . '" ' . $checked . ' data-count="' . $GLOBALS['filter_count'] . '" data-url="v[' . $n . ']=' . $p[1] . '"  data-name="' . $n . '-' . $p[1] . '">
    <span class="filter-item"  title="' . $p[0] . '">' . $text . '</span>
  </label>
  <small class="pull-right" data-num="' . $n . '-' . $p[1] . '">' . $p[3] . '</small>
</div>';
            $num++;
        }
        $GLOBALS['filter_count'] ++;
    }

    if ($num > $limit) {
        $style = "collapse";
        $chevron = 'fa fa-angle-up';
        $help = __('Показать');
    } else {
        $style = "collapse in";
        $chevron = 'fa fa-angle-down';
        $help = __('Скрыть');
    }

    return '<div class="h4" data-toggle="collapse" data-target="#collapseSort' . $n . '" title="' . $help . '">' . $title . ' <i class="' . $chevron . '"></i></div><div class="' . $style . '" id="collapseSort' . $n . '">' . $disp . '</div>';
}

/**
 * Шаблон вывода характеристик виртуальные каталоги
 */
function sortсattemplatehook($value, $n, $title, $vendor) {
    global $PHPShopSeoPro, $PHPShopNav;
    $disp = null;

    if (is_array($value)) {
        foreach ($value as $p) {

            $text = $p[0];
            $checked = null;
            if (is_array($vendor)) {
                foreach ($vendor as $v) {
                    if ($v == $p[1])
                        $checked = 'active';
                }
            }
            if ($p[3] != null)
                $text .= ' (' . $p[3] . ')';

            if (!empty($p[5])) {

                if (strpos($GLOBALS['SysValue']['nav']['truepath'], '.filters/') !== false) {
                    $path = preg_replace('#^(.*)/filters/.*$#', '$1', $GLOBALS['SysValue']['nav']['truepath']);
                    $filters = preg_replace('#^.*/filters/(.*)$#', '$1', $GLOBALS['SysValue']['nav']['truepath']);
                } else
                    $path = preg_replace('#^(.*).html/.*$#', '$1', $GLOBALS['SysValue']['nav']['truepath']);


                if ($filters == $p[5])
                    $checked = 'active';
                else
                    $checked = null;
            }


            PHPShopParser::set('podcatalogIcon', $p[4]);
            PHPShopParser::set('podcatalogName', $text);

  
            // SEO ссылка
            if (!empty($p[5])){
                PHPShopParser::set('podcatalogId', $PHPShopSeoPro->getCID());
                $disp .= PHPShopParser::file($GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . '/catalog/cid_category.tpl', true,['.html' => '.html/filters/' . $p[5]]);
            }else{
                PHPShopParser::set('podcatalogId', $PHPShopNav->getId());
                $disp .= PHPShopParser::file($GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . '/catalog/cid_category.tpl', true, ['.html' => '.html?v[' . $n . ']=' . $p[1]]);
            }
        }
    }

    return $disp;
}

/**
 *  Фотогалерея
 */
function template_image_gallery($obj, $array) {

    $bxslider = $bxsliderbig = $bxpager = null;
    $PHPShopOrm = new PHPShopOrm($obj->getValue('base.foto'));
    $data = $PHPShopOrm->select(array('*'), array('parent' => '=' . $array['id']), array('order' => 'num'), array('limit' => 100));
    $i = 0;
    $s = 1;

    // Нет данных в галерее
    if (!is_array($data) and ! empty($array['pic_big']))
        $data[] = array('name' => $array['pic_big']);

    if (is_array($data)) {

        // Сортировка
        foreach ($data as $k => $v) {

            if ($v['name'] == $array['pic_big']) {
                $sort_data[0] = $v;
            } else
                $sort_data[$s] = $v;

            $s++;
        }

        ksort($sort_data);

        foreach ($sort_data as $k => $row) {

            $name = $row['name'];
            $name_s = str_replace(".", "s.", $name);
            $name_bigstr = str_replace(".", "_big.", $name);

            if (empty($row['info']))
                $row['info'] = $array['name'];

            $alt = str_replace('"', '', $row['info']);

            // Поддержка Webp
            if (method_exists($obj, 'setImage') and ! in_array(pathinfo($name, PATHINFO_EXTENSION), ['mp4', 'mov'])) {
                $name = $obj->setImage($name);
                $name_s = $obj->setImage($name_s);
                $name_bigstr = $obj->setImage($name_bigstr);
            }

            // Подбор исходного изображения
            if (!$obj->PHPShopSystem->ifSerilizeParam('admoption.image_save_source') or ! file_exists($_SERVER['DOCUMENT_ROOT'] . $name_bigstr))
                $name_bigstr = $name;
            if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $name_s))
                $name_s = $name;

            // Видео
            if (in_array(pathinfo($name, PATHINFO_EXTENSION), ['mp4', 'mov'])) {
                $bxslider .= '<div><div class="embed-responsive embed-responsive-4by3"><video class="embed-responsive-item" src="'.$name.'" controls></video></div></div>';
                $bxsliderbig .= '<li><div class=\'embed-responsive embed-responsive-16by9\'><video class=\'embed-responsive-item\' src=\''.$name.'\' controls style=\'max-height:500px\'></video></div></li>';
                $bxpager .= '<a data-slide-index=\'' . $i . '\' href=\'\'><img class=\'img-thumbnail\' alt=\'\' title=\'\' src=\'images/video.jpg\' data-big-image=\'\'images/video.jpg\'\'></a>';
            }
            // Изображение
            else {
                $bxslider .= '<div><a class href="#"><img src="' . $name . '" title="' . $alt . '" alt="' . $alt . '" /></a></div>';
                $bxsliderbig .= '<li><a class href=\'#\'><img src=\'' . $name_bigstr . '\' title=\'' . $alt . '\' alt=\'' . $alt . '\'></a></li>';
                $bxpager .= '<a data-slide-index=\'' . $i . '\' href=\'\'><img class=\'img-thumbnail\' alt=\'\' title=\'\' src=\'' . $name_s . '\' data-big-image=\'' . $name . '\'></a>';
            }

            $i++;
        }

        if ($i < 2)
            $bxpager = null;

        $obj->set('productFotoList', '<img class="bxslider-pre" itemprop="image"  src="' . $name_s . '" title="' . $array['name'] . '" alt="' . $array['name'] . '" /><div class="bxslider hide">' . $bxslider . '</div><div class="bx-pager">' . $bxpager . '</div>');
        $obj->set('productFotoListBig', '<ul class="bxsliderbig" data-content="' . $bxsliderbig . '" data-page="' . $bxpager . '"></ul><div class="bx-pager-big">' . $bxpager . '</div>');
        return true;
    }
}

$addHandler = array
    (
    'CID_Product' => 'template_CID_Product',
    'parent' => 'template_parent',
    'UID' => 'template_UID',
    'image_gallery' => 'template_image_gallery'
);
?>