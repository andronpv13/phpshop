<?php

/**
 * Выгрузка прайс-листов
 * @author PHPShop Software
 * @version 1.4
 * @package PHPShopForms
 */
session_start();

$_classPath = "../phpshop/";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("valuta");
PHPShopObj::loadClass("product");

$PHPShopValutaArray = new PHPShopValutaArray();
$PHPShopSystem = new PHPShopSystem();

// Мультибаза
$PHPShopBase->checkMultibase();

class PHPShopPriceSave {

    var $csv;

    function __construct() {
        $this->debug = false;
        $this->objBase = $GLOBALS['SysValue']['base']['products'];
    }

    function select() {
        global $PHPShopSystem, $PHPShopValutaArray;
        if (is_numeric(@$_GET['catId']))
            $str = " (category=$_GET[catId] or dop_cat LIKE '%#$_GET[catId]#%') and ";
        else {
            $str = null;
            $queryMultibase = $this->queryMultibase();
            if (!empty($queryMultibase))
                $str = ' ' . $queryMultibase;
        }

        // Системная валюта
        $system_currency = $PHPShopSystem->getValue('dengi');
        $ValutaArray = $PHPShopValutaArray->getArray();
        $valuta = $ValutaArray[$system_currency]['code'];

        // Настрока показа цен после авторизации
        if ($PHPShopSystem->getSerilizeParam('admoption.user_price_activate') == 1 and empty($_SESSION['UsersId']))
            $user_price_activate = true;

        $PHPShopOrm = new PHPShopOrm();
        $PHPShopOrm->comment = __CLASS__ . '.' . __FUNCTION__;
        $PHPShopOrm->debug = $this->debug;
        $result = $PHPShopOrm->query("select * from " . $this->objBase . " where " . $str . " enabled='1'");
        while ($row = mysqli_fetch_array($result)) {
            $price_array = array($row['price'], $row['price2'], $row['price3'], $row['price4'], $row['price5']);
            $price = PHPShopProductFunction::GetPriceValuta($row['id'], $price_array, $row['baseinputvaluta']);

            // Если цены показывать только после аторизации
            if (!empty($user_price_activate) and !$_SESSION['UsersId']) {
                $price = "~";
            }

            $this->csv.=$row['uid'] . ';' . $row['name'] . ';' . $price . ' ' . $valuta . '
';
        }
    }

    // GZIP сжатие
    function gzcompressfile($source, $level = false) {
        $dest = $source . '.gz';
        $mode = 'wb' . $level;
        $error = false;
        if (@$fp_out = gzopen($dest, $mode)) {
            if (@$fp_in = fopen($source, 'rb')) {
                while (!feof($fp_in))
                    gzwrite($fp_out, fread($fp_in, 1024 * 512));
                fclose($fp_in);
            }
            else
                $error = true;
            @gzclose($fp_out);
            unlink($source);
            rename($dest, $source . '.gz');
        }
        else
            $error = true;
        if ($error)
            return false;
        else
            return $dest;
    }

    // Вывод результата
    function compile() {
        $file = "base_" . date("d_m_y_His") . ".csv";
        @$fp = fopen("price/" . $file, "w+");
        if ($fp) {
            fputs($fp, $this->csv);
            fclose($fp);
            $sorce = "price/" . $file;
        }
        // Пишес  GZIP
        if (!empty($_GET['gzip'])) {
            $this->gzcompressfile($sorce);
            header("Location: price/" . $file . ".gz");
        } else {
            header("Location: " . $sorce);
        }
    }

    public function queryMultibase() {

        // Мультибаза
        if (defined("HostID") or defined("HostMain")) {

            $multi_cat = [];
            $multi_dop_cats=null;

            // Не выводить скрытые каталоги
            $where['skin_enabled '] = "!='1'";

            if (defined("HostID"))
                $where['servers'] = " REGEXP 'i" . HostID . "i'";
            elseif (defined("HostMain"))
                $where['skin_enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';

            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
            $PHPShopOrm->debug = $this->debug;
            $data = $PHPShopOrm->getList(array('id'), $where);

            foreach ($data as $row) {
                $multi_cat[] = $row['id'];
                $multi_dop_cats .= ' OR dop_cat LIKE \'%#' . $row['id'] . '#%\' ';
            }

            if (count($multi_cat) > 0)
                $multi_select = ' (category IN (' . implode(',', $multi_cat) . ') '.$multi_dop_cats.') and ';

            return $multi_select;
        }
    }
}

$PHPShopPriceSave = new PHPShopPriceSave();
$PHPShopPriceSave->select();
$PHPShopPriceSave->compile();
?>