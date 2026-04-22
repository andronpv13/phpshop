<?php

/**
 * Панель дополнительных шаблонов
 * @param array $row массив данных
 * @return string 
 */
function tab_base($data) {
    global $PHPShopGUI, $skin_base_path, $PHPShopBase;

    $disp = null;

    // Установленные шаблоны
    if (is_array($data))
        foreach ($data as $val) {
            $path_parts = pathinfo($val);
            $ready_theme[] = $path_parts['basename'];

            // Версия шаблона
            $Template = parse_ini_file_true("../templates/" . $path_parts['basename'] . '/php/inc/config.ini', 1);
            $ready_version[$path_parts['basename']] = $Template['sys']['version'];
        }

    // Дефолтные шаблоны 
    $i = 1;
    $count = 0;
    $data_pic = xml2array($skin_base_path . '/template5.php', "template", true);
    arsort($data_pic);

    $title_default = '<p class="text-muted hidden-xs data-row">' . __('Ниже представлены штатные бесплатные шаблоны, адаптированные для мобильных устройств. Для редактирования шаблона, кликните на кнопку "Настроить". Выбранный новый шаблон нужно сохранить в <a href="?path=system#1"><span class="glyphicon glyphicon-share-alt"></span>Основых настройках</a> для отображения посетителям магазина') . '.</p>';
    $img_list_default = null;
    if (is_array($data_pic))
        foreach ($data_pic as $row) {

            if ($i == 1)
                $img_list_default .= '<div class="row">';

            if (in_array($row['name'], $ready_theme)) {
                $main = "hide";
                $panel = 'panel-default';
                $mes = '  <span class="pull-right text-muted">' . __('текущая версия') . ' ' . $ready_version[$row['name']] . '</span>';
                $demo = null;
                $href=null;
                $reload = 'skin-load';

                if ((float) $row['version'] > (float) $ready_version[$row['name']]){
                    $load = __('Обновить');
                    $icon = 'glyphicon-cloud-download';
                    $button = 'btn-warning';
                }
                else{
                    $load = __('Перегрузить');
                    $icon = 'glyphicon-save';
                    $button = 'btn-default';
                }

                
            } else {
                $main = "btn-default";
                $panel = 'panel-default';
                $mes = null;
                $reload = null;
                $href=null;
                $demo = "hide";
                $load = __('Загрузить');
                $icon = 'glyphicon-plus';
                $button = 'btn-success';
            }

            
            if (empty($_SESSION['update'])){
                $reload = null;
                $href='?path=update';
            }

            if ($row['type'] == 'new')
                $new = ' <span class="label label-primary">new</span>';
            else
                $new = null;

            $img_list_default .= '<div class="col-md-4"><div class="panel ' . $panel . '"><div class="panel-heading">' . $row['name'] . $new . $mes . '</div><div class="panel-body text-center"><img class="image-shadow image-skin"  src="' . $row['icon'] . '"></div>
                
           <div class="text-center panel-footer">
                    
                        <div class="btn-group" role="group" aria-label="...">
                        <a class="btn btn-sm btn-primary ' . $demo . '" data-toggle="tooltip" data-placement="top" title="' . __('Настроить') . '" href="?path=' . $_GET['path'] . '&name=' . $row['name'] . '"><span class="glyphicon glyphicon-cog"></span> ' . __('Настроить') . '</a>
                            
                        <a class="btn btn-sm '.$button.' ' . $reload . '" href="'.$href.'"  data-path="' . $row['name'] . '" data-type="default" data-toggle="tooltip" data-placement="top" title="' . $load . '"><span class="glyphicon ' . $icon . '"></span> ' . $row['version'] . '</a>
                              
                        <a class="btn btn-sm btn-default ' . $demo . '" data-toggle="tooltip" data-placement="top" title="' . __('Посмотреть демо') . '" href="../../?skin=' . $row['name'] . '" target="_blank"><span class="glyphicon glyphicon-eye-open"></span> ' . __('Демо') . '</a>
                            
                        

                        </div>
                     </div>

</div></div>';

            if ($i == 3) {
                $img_list_default .= '</div>';
                $i = 1;
            } else
                $i++;

            $count++;
        }


    if (count($data_pic) % 3 != 0)
        $img_list_default .= '</div>';

    if (!empty($img_list_default)) {

        if (stristr($_SESSION['lang'], "utf"))
            $promo = __($promo);

        $PHPShopGUI->_CODE = $title_default . $img_list_default;
    } else
        $disp = $PHPShopGUI->setAlert('Ошибка связи с сервером ' . $skin_base_path, $type = 'warning');


    return $disp;
}