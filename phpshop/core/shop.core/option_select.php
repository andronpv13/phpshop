<?php

/**
 * Выбор опций товаров
 * @author PHPShop Software
 * @version 1.4
 * @package PHPShopCoreFunction
 * @param obj $obj объект класса
 * @return mixed
 */
function option_select($obj, $data) {

    $category = $data['category'];
    $xid = $data['id'];

    $vendor_array = unserialize($data['vendor_array']);
    $sort = $obj->select(array('*'), array('id' => '=' . $category), false, false, __FUNCTION__, array('base' => $obj->getValue('base.categories'), 'cache' => 'true'));
    $sort = unserialize($sort['sort']);

    // Список для выборки
    $sortList = null;
    if (is_array($sort))
        foreach ($sort as $value) {
            $sortList.=' id=' . trim($value) . ' OR';
        }
    $sortList = substr($sortList, 0, strlen($sortList) - 2);

    $disp = null;
    $adder = null;
    $numel = $num = 0;
    if (is_array($sort)) {
        $PHPShopOrm = new PHPShopOrm();
        $PHPShopOrm->debug = $obj->debug;
        $PHPShopOrm->comment = get_class($obj) . '.' . __FUNCTION__;
        $result = $PHPShopOrm->query("select * from " . $obj->getValue('base.sort_categories') . " where (" . $sortList . ") and goodoption='1' order by num,name");
        while (@$row = mysqli_fetch_array($result)) {
            $id = $row['id'];
            $name = $row['name'];
            $opt_sel = option_select_add($vendor_array, $id, $name, $numel, $xid, $row['optionname'], $obj->debug);
            if (!empty($opt_sel)) {
                $num++;
                $disp.= '<div>' . $opt_sel . '</div>';
                $adder.='+document.getElementById("opt' . $numel . $xid . '").value';
                $numel++;
            }
        }
    }

    if (!empty($num)) {
        $disp = '<script>function alloptions' . $xid . '() { var optsvalue=""' . $adder . ';document.getElementById("allOptionsSet' . $xid . '").value=optsvalue; }</script>
<div class="table-optionsDisp optionsDisp">' . $disp . '</div>
<input type="hidden" id="allOptionsSet' . $xid . '" value="">';

        $obj->set('optionMessage', $obj->lang('select_size'));
        $obj->set('optionsDisp', $disp);
    }
}

function option_check($data, $n, $v) {
    if (is_array($data[$n]))
        foreach ($data[$n] as $val)
            if ($val == $v)
                return true;
}

/**
 * Вывод опций
 */
function option_select_add($vendor_array, $n, $title, $numel, $xid, $optionname, $debug = false) {

    if (!empty($GLOBALS['SysValue']['nav']['query']['v']))
        $vendor = $GLOBALS['SysValue']['nav']['query']['v'];
    else
        $vendor = null;

    if (!empty($vendor_array[$n]) and !is_array($vendor_array[$n]))
        return '';

    $dis = $disp = null;
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort']);
    $PHPShopOrm->debug = $debug;
    $PHPShopOrm->comment = 'phpshopshop.' . __FUNCTION__;
    $data = $PHPShopOrm->select(array('*'), array('category' => '=' . intval($n)), array('order' => 'num,name'), array('limit' => 1000));
    if (is_array($data))
        foreach ($data as $row)
            if (option_check($vendor_array, $n, $row['id'])) {

                $id = $row['id'];
                $name = substr($row['name'], 0, 35);
                if ($optionname) {
                    $ct = $title . ':';
                } else {
                    $ct = "";
                }
                $sel = "";
                if (is_array($vendor))
                    foreach ($vendor as $k => $v) {
                        if ($id == $v)
                            $sel = "selected";
                    }
                $dis.='<option value="[' . $ct . $name . ']" data-icon="'.$row['icon'].'">' . $name . '</option>' . "\n";
            }

    if (!empty($dis)) {
        
 
        if(empty($optionname)) $requared='req';
        else $requared=null;

        $disp = '<select name=v[' . $n . '] size=1 id="opt' . $numel . $xid . '" onChange="alloptions' . $xid . '()" class="form-control '.$requared.'" >
    <option value="">-- ' . $title . ' --</option>' . $dis . '</select>';
    }

    return $disp;
}

?>