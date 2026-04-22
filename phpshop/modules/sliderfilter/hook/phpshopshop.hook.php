<?php

function sliderfilter_CID_Product($obj, $data, $rout) {
    if ($rout == 'END') {

        // Фасетный фильтр
        $PHPShopSort = new PHPShopSort($obj->category, $obj->PHPShopCategory->getParam('sort'), true, 'sliderfilterhook', isset($_GET['v']) ? $_GET['v'] : false, true, true, true, $obj->cat_template);
        $obj->set('vendorDisp', $PHPShopSort->display());

        // Подключаем шаблон
        $filter = PHPShopParser::file($GLOBALS['SysValue']['templates']['sliderfilter']['sliderfilter_filter'], true, false, true);
        $obj->set('sliderFilter', $filter);
    }
}

/**
 * Шаблон вывода характеристик
 */
function sliderfilterhook($value, $n, $title, $vendor) {

    // Слайдер
    $slider = (new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']))->getOne(['sliderfilter_enabled'], ['id' => '=' . (int) $n])['sliderfilter_enabled'];

    $disp = null;
    $num = $slider_max[$n] = $slider_min[$n] = 0;

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


            if (!empty($slider)) {
                $slider_list[] = $text;
            }

            // Чекбокс
            if (empty($slider)) {

                PHPShopParser::set('sliderfilter_n', $n);
                PHPShopParser::set('sliderfilter_p', $p[1]);
                PHPShopParser::set('sliderfilter_num', $p[3]);
                PHPShopParser::set('sliderfilter_checked', $checked);
                PHPShopParser::set('sliderfilter_name', $text);

                $disp .= PHPShopParser::file($GLOBALS['SysValue']['templates']['sliderfilter']['sliderfilter_checkbox'], true, false, true);
            }

            $num++;
        }
        $GLOBALS['filter_count'] ++;
    }


    // Слайдер
    if (!empty($slider)) {

        if (empty((int) $_GET['vmin'][$n]))
            $slider_min = min($slider_list);
        else
            $slider_min = (int) $_GET['vmin'][$n];

        if (empty((int) $_GET['vmax'][$n]))
            $slider_max = max($slider_list);
        else
            $slider_max = (int) $_GET['vmax'][$n];

        PHPShopParser::set('sliderfilter_n', $n);
        PHPShopParser::set('sliderfilter_min', $slider_min);
        PHPShopParser::set('sliderfilter_max', $slider_max);


        $disp = '<div id="sort-filter-body-' . $n . '">
                   '.PHPShopParser::file($GLOBALS['SysValue']['templates']['sliderfilter']['sliderfilter_slider'], true, false, true).'

<script>
$( function() {

  $("#slider-sort-' . $n . '").slider({
       range: true,
       min: ' . min($slider_list) . ',
       max: ' . max($slider_list) . ',
       values: [' . $slider_min . ', ' . $slider_max . '],
       slide: function( event, ui ) {
        $("#sort-filter-body-' . $n . ' input[id=sort-min-' . $n . ']").val(ui.values[0]);
        $("#sort-filter-body-' . $n . ' input[id=sort-max-' . $n . ']").val(ui.values[1]);
      }
  });
  
});
</script>
                </div>';
    }

    if (PHPShopString::is_mobile())
        $return = '<div class="pb-4 mb-4"><h6>' . $title . '</h6><div>' . $disp . $disp_limit . '</div></div>';
    else
        $return = '<div class="border-bottom pb-4 mb-4 faset-filter-block-wrapper"><h4>' . $title . '</h4><div>' . $disp . $disp_limit . '</div></div>';

    return $return;
}

$addHandler = array
    (
    'CID_Product' => 'sliderfilter_CID_Product',
);
