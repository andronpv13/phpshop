<?php

function addProductIDProductsgroup($data) {
    global $PHPShopGUI;

    $productsgroup_products = unserialize($data['productsgroup_products']);
    $Tab10 = $PHPShopGUI->setCheckbox('productsgroup_check_new', 1, 'Включить вывод групп составных товаров', $data['productsgroup_check']);
    $tr = null;

    for ($i = 1; $i < 10; $i++) {

        $tr .= '<tr>
            <td>' . $PHPShopGUI->setInputText(null, 'productsgroup_products[' . $i . '][id]', @$productsgroup_products[$i]['id']) . ' </td>
            <td><button  class="btn btn-default btn-sm cart-add" data-id="' . $i . '"><span class="glyphicon glyphicon-plus"></span> ' . __('Добавить товар') . '</button></td>
            <td>' . $PHPShopGUI->setInputText(null, 'productsgroup_products[' . $i . '][num]', @$productsgroup_products[$i]['num']) . '</td>
        </tr>';
    }

    $Tab10 .= '<br><br><table class="table table-striped table-hover" style="width:550px;">
        <tr>
            <th class="text-center" width="40%">' . __('Товар') . ' ID</th>
            <th class="text-center" width="20%"></th>
            <th class="text-center">' . __('Кол-во') . '</th>
        </tr>
         ' . $tr . '
    </table>
  ';

    $Tab10 .= $PHPShopGUI->addJSFiles('../modules/productsgroup/admpanel/gui/productsgroup.gui.js');
    $PHPShopGUI->addTab(array("Группы", $Tab10, true));
}

function updateProductIDProductsgroup() {
    global $link_db;

    if (empty($_POST['ajax']))
        if (empty($_POST['productsgroup_check_new'])) {
            $_POST['productsgroup_check_new'] = 0;
        }

    if (is_array($_POST['productsgroup_products'])) {

        // Обновляем цену
        if ($_POST['productsgroup_check_new'] == 1) {
            $sql_where = $products_keys = '';
            foreach ($_POST['productsgroup_products'] as $prod) {
                if ($prod['id'] > 0) {
                    if ($sql_where != '')
                        $sql_where .= ' OR id=' . $prod['id'];
                    else
                        $sql_where = ' WHERE id=' . $prod['id'];

                    $products_num[$prod['id']] = $prod['num'];
                    $products_keys .= '#' . $prod['id'] . '#';
                }
            }

            $sql = 'SELECT * FROM ' . $GLOBALS['SysValue']['base']['products'] . ' ' . $sql_where;
            $query = mysqli_query($link_db, $sql);
            $products = mysqli_fetch_array($query);
            do {
                $price_all = $price_all + ($products['price'] * intval($products_num[$products['id']]));
            } while ($products = mysqli_fetch_array($query));

            $_POST['price_new'] = $price_all;

            $_POST['productsgroup_products_keys_new'] = $products_keys;
            $_POST['productsgroup_products_new'] = serialize($_POST['productsgroup_products']);
        }
        // Обновление цены у сборного товара при изменении товара в группе
        else {

            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);

            $data = $PHPShopOrm->select(['id,productsgroup_products,productsgroup_check'], ['productsgroup_products_keys' => ' LIKE "%#' . intval($_POST['rowID']) . '#%"'], null, ['limit' => 1000]);

            if (is_array($data)) {
                foreach ($data as $data_product) {
                    $price_all = 0;
                    if ($data_product['productsgroup_check'] == 1) {

                        $sql_where = $products_keys = '';
                        foreach (unserialize($data_product['productsgroup_products']) as $prod) {
                            if ($prod['id'] > 0) {
                                if ($sql_where != '')
                                    $sql_where .= ' OR id=' . $prod['id'];
                                else
                                    $sql_where = ' WHERE id=' . $prod['id'];

                                $products_num[$prod['id']] = $prod['num'];
                                $products_keys .= '#' . $prod['id'] . '#';
                            }
                        }

                        $sql = 'SELECT * FROM ' . $GLOBALS['SysValue']['base']['products'] . ' ' . $sql_where;
                        $query = mysqli_query($link_db, $sql);
                        $products = mysqli_fetch_array($query);
                        do {

                            // Текущий товар
                            if ($products['id'] == $_POST['rowID'])
                                $products['price'] = $_POST['price_new'];

                            $price_all = $price_all + ($products['price'] * intval($products_num[$products['id']]));
                        } while ($products = mysqli_fetch_array($query));

                        $PHPShopOrm->update(['price_new' => $price_all], ['id' => '=' . $data_product['id']]);
                    }
                }
            }
        }
    }
}

$addHandler = array(
    'actionStart' => 'addProductIDProductsgroup',
    'actionDelete' => false,
    'actionUpdate' => 'updateProductIDProductsgroup'
);
?>