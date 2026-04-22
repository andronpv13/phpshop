<?php

/**
 * Авторизация для 1С
 * @package PHPShopExchange
 * @author PHPShop Software
 * @version 2.4
 */
// UTF-8 Env Fix
if (ini_get("mbstring.func_overload") > 0) {
    ini_set("mbstring.internal_encoding", null);
}

$_classPath = "../phpshop/";
include($_classPath . "class/obj.class.php");
include($_classPath . "lib/phpass/passwordhash.php");
PHPShopObj::loadClass(array("base", "system", "math", "array", "valuta", "security"));

// Подключение к БД
$PHPShopBase = new PHPShopBase($_classPath . "/inc/config.ini", true, true);

// Модули
$PHPShopModules = new PHPShopModules($_classPath . "modules/");

// Подключение хука
function loadHooks($file = false) {

    $path = "hook";

    if (empty($file)) {

        if (@$dh = opendir('hook')) {
            while (($file = readdir($dh)) !== false) {
                $fstat = explode(".", $file);
                if ($fstat[1] == "php" and ! strstr($fstat[0], '#'))
                    include_once($path . '/' . $file);
            }
            closedir($dh);
        }
    } else
        include_once($path . '/' . $file);
}

// Подключение хука
loadHooks();

// CML
if (!empty($_GET['cml']))
   loadHooks('#cml.php');

/**
 * Авторизация пользователей
 * @package PHPShopExchange
 * @author PHPShop Software
 * @version 1.3
 */
class UserChek {

    var $logPHPSHOP;
    var $pasPHPSHOP;
    var $idPHPSHOP;
    var $statusPHPSHOP;
    var $mailPHPSHOP;
    var $OkFlag = 0;

    function __construct($logPHPSHOP, $pasPHPSHOP, $tokenPHPSHOP) {
        global $PHPShopBase;

        $this->logPHPSHOP = $logPHPSHOP;
        $this->pasPHPSHOP = $this->myDecode($pasPHPSHOP);
        $this->tokenPHPSHOP = $tokenPHPSHOP;
        $this->ChekBase($PHPShopBase->getParam("base.users"));
        $this->BadUser();
    }

    function ChekBase($table_name) {
        global $link_db;

        $hasher = new PasswordHash(8, false);

        $sql = "select * from " . $table_name . " where enabled='1'";
        @$result = mysqli_query($link_db, $sql);
        while (@$row = mysqli_fetch_array(@$result)) {

            // Логин и пароль
            if ($this->logPHPSHOP == $row['login']) {

                if ($hasher->CheckPassword($this->pasPHPSHOP, $row['password'])) {
                    $this->OkFlag = 1;
                }
            }

            // Токен
            if ($this->tokenPHPSHOP != '' and $this->tokenPHPSHOP == $row['token']) {
                $this->OkFlag = 1;
            }
        }
    }

    function myDecode($disp) {
        $decode = substr($disp, 0, strlen($disp) - 4);
        $decode = str_replace("I", 11, $decode);
        $decode = explode("O", $decode);
        $disp_pass = "";
        for ($i = 0; $i < (count($decode) - 1); $i++)
            $disp_pass .= chr($decode[$i]);
        return $disp_pass;
    }

    function BadUser() {
        if ($this->OkFlag == 0)
            exit("Login Error");
    }

}

// Проверка авторизации
$UserChek = new UserChek($_REQUEST['log'], $_REQUEST['pas'], $_REQUEST['token']);
?>