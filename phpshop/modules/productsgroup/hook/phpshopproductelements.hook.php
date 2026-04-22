<?php
function phpshopproductelements_product_grid_productsgroup($obj,$row,$rout) {
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
            <div class="btn btn-primary addToCartListGroup basket_put btn-sm" role="button" data-num="1" data-uid-group="'.$data_uid_group.'"><span>В корзину</span></div>';

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
    'product_grid' => 'phpshopproductelements_product_grid_productsgroup'
    );
?>