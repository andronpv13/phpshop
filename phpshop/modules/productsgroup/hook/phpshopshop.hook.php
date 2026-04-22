<?php


function UID_modules_productsgroup($obj,$row,$rout){
    global $link_db;

    if($rout == 'MIDDLE'){
        if( $row['productsgroup_check']==1 ) {

            $productsgroup_products = unserialize($row['productsgroup_products']);
            if(is_array($productsgroup_products)) {
                foreach ($productsgroup_products as $group) {
                    if($group['id']!='') {
                        $sql_where .= ' OR id='.intval($group['id']);
                        $productsgroup_array[$group['id']] = $group['num'];
                    }
                }
                $all_price = 0;
                $sql = 'SELECT * FROM `phpshop_products` WHERE id=0 '.$sql_where.' ';
                $query = mysqli_query($link_db, $sql);
                $products = mysqli_fetch_array($query);
                do {
                    $price = $obj->price($products);
                    $price_data = $obj->price($products);
                    $all_price += $price*intval($productsgroup_array[ $products['id'] ]);            
                    // Форматирование
                    $price = number_format($price, $obj->format, '.', ' ');

                    $obj->set('productsgroup_pic_small', $products['pic_small']);
                    $obj->set('productsgroup_name', $products['name']);
                    $obj->set('productsgroup_price', $price);
                    $obj->set('productsgroup_price_data', $price_data);
                    $obj->set('productsgroup_num', $productsgroup_array[ $products['id'] ]);
                    $obj->set('productsgroup_id', $products['id']);
                    $obj->set('currency', $obj->currency );

                    $tr .= PHPShopParser::file('./phpshop/modules/productsgroup/templates/productsgroup_main_table_tr.tpl', true, true, true);

                    $data_uid_group .= $products['id'].':'.$productsgroup_array[ $products['id'] ].'|';
                }
                while ($products = mysqli_fetch_array($query));
            }


            if($tr!='') {
                $obj->set('all_price', $all_price);
                $obj->set('data_uid_group', $data_uid_group);
                $obj->set('productsgroup_table_tr', $tr);

                $table .= PHPShopParser::file('./phpshop/modules/productsgroup/templates/productsgroup_main.tpl', true, true, true);
            }

            $obj->set('ComStartCart', '<!--');
            $obj->set('ComEndCart', '-->');
            $obj->set('productsgroup_list', $table);
        }
    }
}

function phpshopshop_product_grid_productsgroup($obj,$row,$rout) {
    if($rout=='MIDDLE') {
        if( $row['productsgroup_check']==1 ) {
            global $link_db;

            $productsgroup_products = unserialize($row['productsgroup_products']);

            foreach ($productsgroup_products as $group) {
                if($group['id']!='') {
                    $sql_where .= ' OR id='.intval($group['id']);
                    $productsgroup_array[$group['id']] = $group['num'];
                }
            }

            $sql = 'SELECT * FROM `phpshop_products` WHERE id=0 '.$sql_where.' ';
            $query = mysqli_query($link_db, $sql);
            $products = mysqli_fetch_array($query);
            do {
                $price = $obj->price($products);
                $all_price += $price*intval($productsgroup_array[ $products['id'] ]);            
                $data_uid_group .= $products['id'].':'.$productsgroup_array[ $products['id'] ].'|';
            }
            while ($products = mysqli_fetch_array($query));

            $productsgroup_button_buy = '
            <button class="btn btn-primary addToCartListGroup basket_put btn-sm" role="button" data-num="1" data-uid-group="'.$data_uid_group.'"><span>В корзину</span></button>';

            $obj->set('productPrice', $all_price);
            $obj->set('ComStartCart', '<!--');
            $obj->set('ComEndCart', '-->');
            $obj->set('productsgroup_button_buy', $productsgroup_button_buy);
        }
        else {
            $obj->set('ComStartCart', '');
            $obj->set('ComEndCart', '');
            $obj->set('productsgroup_button_buy', '');

        }
    }
}

$addHandler = array
    (
    'UID' => 'UID_modules_productsgroup',
    'product_grid' => 'phpshopshop_product_grid_productsgroup'
);
?>