<?php

/**
 * Библиотека проверки прав администрирования
 * @author PHPShop Software
 * @version 1.2
 * @package PHPShopClass
 */
class PHPShopAdminRule {

    protected $UserStatus;

    /**
     * Конструктор
     */
    function __construct() {

        // Проверка авторизации
        $this->UserStatus = $this->ChekBase();

        $this->fixRules = array(
            'banner' => 'baner',
            'order' => 'visitor',
            'payment' => 'order',
            'catalog' => 'cat_prod',
            'product' => 'report',
            'slider' => 'baner',
            'report' => 'stats1',
            'menu' => 'page_menu',
            'page' => 'page_menu',
            'rss' => 'rsschanels',
            'modules' => 'module',
            'system' => 'visitor',
            'exchange' => 'cat_prod',
            'sort' => 'catalog',
            'catpage' => 'page',
            'photo' => 'page',
            'intro' => 'report',
            'upload' => 'update',
            'currency' => 'valuta',
            'tpleditor' => 'system',
            'metrica' => 'report',
            'support' => 'report',
            'promotions' => 'system',
            'citylist' => 'delivery',
            'lead' => 'order',
            'company' => 'system',
            'dialog' => 'shopusers'
        );
    }

    /**
     * Проверка авторизации
     * @return mixed
     */
    function ChekBase() {

        // Проверка сессии
        $session_id = session_id();
        if (!$session_id)
            session_start();

        if (!empty($_SESSION['idPHPSHOP']))
            $idPHPSHOP = $_SESSION['idPHPSHOP'];
        else
            $idPHPSHOP = null;

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['users']);
        $data = $PHPShopOrm->select(array('*'), array('enabled' => "='1'", 'id' => "='" . intval($idPHPSHOP) . "'"), false, array('limit' => 1));

        if (is_array($data)) {
            $status = unserialize($data['status']);
            $hasher = new PasswordHash(8, false);
            if ($_SESSION['logPHPSHOP'] == $data['login']) {
                if ($hasher->CheckPassword($_SESSION['pasPHPSHOP'], $data['password'])) {

                    // Проверка журнала авторизации
                    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['jurnal']);
                    $jurnal = $PHPShopOrm->select(array('id'), array('ip' => '="' . PHPShopSecurity::TotalClean($_SERVER['REMOTE_ADDR']) . '"', 'user' => "='" . $data['login'] . "'", 'datas' => '>' . (time() - 3600 * 24 * 2), 'flag' => "='0'"), false, array('limit' => 1));

                    if (!empty($jurnal))
                        return $status;
                    elseif ((new PHPShopSystem())->getSerilizeParam("admoption.ip_enabled") == 1)
                        return $status;
                }
            }
        }

        if (!empty($_SERVER['QUERY_STRING']))
            $_SESSION['return'] = $_SERVER['QUERY_STRING'];

        header("Location: " . $GLOBALS['SysValue']['dir']['dir'] . "/phpshop/admpanel/");
        exit("No access");
    }

    /**
     * Проверка прав
     * @param string $path раздел администрирования [news|gbook]
     * @param string $do действие [view|edit|remove]
     * @return boolean 
     */
    function CheckedRules($path, $do = 'view') {

        $rules_array = array(
            'view' => 0,
            'edit' => 1,
            'create' => 2,
            'remove' => 3,
            'rule' => 4
        );


        if (empty($this->UserStatus[$path]) and ! empty($this->fixRules[$path]))
            $path = $this->fixRules[$path];

        $array = explode("-", $this->UserStatus[$path]);

        if (!empty($array[$rules_array[$do]]))
            return true;
    }

    /**
     * Собщение об отсутствии права
     */
    function BadUserFormaWindow() {
        echo'
          <div class="alert alert-danger" id="rules-message" role="alert"><span class="glyphicon glyphicon-exclamation-sign"></span>' . __('<strong>Внимание!</strong> Недостаточно прав для выполнения. <a href="#" class="btn btn-xs btn-danger"><span class="glyphicon glyphicon-arrow-left"></span> Вернуться</a> или поменять <a href="?path=users&id=' . $_SESSION['idPHPSHOP'] . '&tab=1" class="btn btn-xs btn-primary">Права <span class="glyphicon glyphicon-arrow-right"></span></a> Администратора.') . '</div>
';
        return true;
    }

    /**
     * Раскодирование пароля для CRM обмена
     * @param string $disp
     * @return string
     */
    static function decodeCrm($disp) {
        $decode = substr($disp, 0, strlen($disp) - 4);
        $decode = str_replace("I", 11, $decode);
        $decode = explode("O", $decode);
        $disp_pass = null;
        for ($i = 0; $i < (count($decode) - 1); $i++)
            $disp_pass .= chr($decode[$i]);
        return $disp_pass;
    }

    /**
     * Кодирование пароля для CRM обмена
     * @param string $pas
     * @return string
     */
    static function encodeCrm($pas) {
        $encode = null;
        for ($i = 0; $i < (strlen($pas)); $i++)
            $encode .= ord($pas[$i]) . "O";

        $encode = str_replace(11, "I", $encode);
        return $encode . "I10O";
    }

}

?>