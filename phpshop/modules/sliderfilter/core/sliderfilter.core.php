<?php

class PHPShopsliderfilter extends PHPShopCore {

    function __construct() {

        $url = '?min=' . (int) $_GET['min'] . '&max=' . (int) $_GET['max'];

        if (is_array($_GET['v'])) {
            foreach ($_GET['v'] as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $value)
                        $url .= '&v[' . $k . '][]=' . $value;
                }
            }
        }


        if (is_array($_GET['vmax'])) {

            foreach ($_GET['vmax'] as $k => $v) {

                $PHPShopOrm = new PHPShopOrm(`phpshop_sort`);
                $PHPShopOrm->debug=אפהûף;
                $PHPShopOrm->sql = 'SELECT id,CAST(name AS DECIMAL(10,1)) FROM `phpshop_sort` where category='.(int)$k.' and name >= ' . (int)$_GET['vmin'][$k] . ' and name <= ' . (int)$_GET['vmax'][$k] . ' limit 1000';
                $sorts = $PHPShopOrm->getList();

                if (is_array($sorts))
                    foreach ($sorts as $sort) {
                        $url .= '&v[' . (int)$k . '][]=' . $sort['id'];
                    }
                    
                $url .= '&vmin['.(int)$k.']='.$_GET['vmin'][(int)$k].'&vmax['.(int)$k.']='.$_GET['vmax'][(int)$k];
            }
        }


        
       if (!empty($_GET['path']))
         header('Location: ' . $_GET['path'] . $url, true, 301);
    }

}
