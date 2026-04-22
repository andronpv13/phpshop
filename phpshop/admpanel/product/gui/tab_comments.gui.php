<?php

function tab_comments($data_product, $option) {


    $PHPShopInterface = new PHPShopInterface();
    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setCaption(array("Дата", "15%"), array("Пользователь", "25%"), array("Отзыв", "45%"), array("Статус", "5%"), array("Оценка", "15%", array('align' => 'right')));


    // Таблица с данными заказов
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['comment']);
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), array('parent_id' => '=' . intval($data_product['id'])), array('order' => 'datas desc'), array('limit' => 100));
    if (is_array($data)) {
        foreach ($data as $row) {

            $datas = PHPShopDate::get($row['datas'], true);

            $content = substr(strip_tags($row['content']), 0, 50);

            $star = '<span class="glyphicon glyphicon-star text-primary"></span>';
            $star_empty = '<span class="glyphicon glyphicon-star-empty text-primary"></span>';

            if (!empty($row['enabled']))
                $enabled = '<span class="glyphicon glyphicon-ok"></span>';
            else
                $enabled = '<span class="glyphicon glyphicon-remove"></span>';

            $i = 0;
            $rate = null;
            while ($i < $row['rate']) {
                $rate .= $star;
                $i++;
            }

            $i = $row['rate'];
            while ($i < 5) {
                $rate .= $star_empty;
                $i++;
            }


            $PHPShopInterface->setRow(array('name' => $datas), array('name' => $row['name'], 'link' => '?path=shopusers&return=product.' . $data_product['id'] . '&id=' . $row['user_id'], 'target' => '_blank'), array('name' => $content, 'link' => '?path=shopusers.comment&return=product.' . $data_product['id'] . '&id=' . $row['id'], 'target' => '_blank'),$enabled, array('name' => $rate, 'align' => 'right', 'class' => ''));
        }

        return '<table class="table table-hover">' . $PHPShopInterface->_CODE . '</table>';
    }
}

?>