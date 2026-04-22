<?php

class PHPShopOrderDelivery {

    private $orm;
    private $isAjaxRequest = false;
    private $allowedDeliveries = 0;
    private $deliverySelected = false;

    public function __construct()
    {
        if(!empty($_REQUEST['type']) and $_REQUEST['type'] === 'json') {
            $this->isAjaxRequest = true;
        }

        $this->orm = new PHPShopOrm($GLOBALS['SysValue']['base']['delivery']);
    }

    // Вывод всех способов доставки.
    public function getDeliveryMethods()
    {
        $html = '';
        $where = [
            'enabled' => '="1"',
            'PID' => '="0"' . $this->servers()
        ];

        foreach ($this->orm->getList(['*'], $where, ['order' => 'num,city']) as $delivery) {
            $html .= $this->getOneDelivery($delivery);
        }

        // Если нет доступных доставок
        if($this->allowedDeliveries === 0) {
            PHPShopParser::set('deliveryId', 0);
            //PHPShopParser::set('deliveryTitle', __('[Доставка по умолчанию]'));
            //$html .= ParseTemplateReturn($this->getTemplate(), true);
            $html .= '<INPUT TYPE="HIDDEN" id="makeyourchoise" VALUE="DONE">';
        }
        
        // Если доставка только одна
        /*
        if($this->allowedDeliveries === 1) {
            $html .= '<IMG onload="UpdateDeliveryJq(' . (int) $delivery['id'] . ',this);" SRC="' . $this->getTemplateAsset('images/shop/flag_green.gif') . '"  style="display:none;">';
        }*/

        return $html;
    }

    // Вывод способов доставки, когда пользователь выбрал.
    public function getDeliveryMethodsById($id)
    {
        $html = '';
        $selectedDelivery = $this->orm->getOne(['*'], ['id' => sprintf('="%s"', $id)]);

        $where = ['enabled' => '="1"' . $this->servers()];
        if ((int) $selectedDelivery['is_folder'] === 1) { //Если прислали папку, то варианты будут потомки папки
            $where['PID'] = sprintf('="%s"', $id);
            $PIDpr = $id;
        } else { //Если прислали вариант, то варианты будут соседи
            $where['PID'] = sprintf('="%s"', $selectedDelivery['PID']);
            $PIDpr = $selectedDelivery['PID'];
        }

        // Кнопка вернуться назад.
        $html .= $this->getBackBtn((int) $PIDpr);

        foreach ($this->orm->getList(['*'], $where, ['order' => 'num,city']) as $delivery) {
            $html .= $this->getOneDelivery($delivery, (int) $selectedDelivery['id'], (int) $selectedDelivery['is_folder'] === 1);
        }

        if($this->deliverySelected) {
            $html .= '<INPUT TYPE="HIDDEN" id="makeyourchoise" VALUE="DONE">';
        }

        return $html;
    }

    public function getAddressFields($deliveryId)
    {
        $delivery = $this->orm->getOne(['data_fields', 'city_select', 'comment', 'is_folder'], ['id' => sprintf('="%s"', $deliveryId)]);
        $mass = unserialize($delivery['data_fields']);

        if((int) $delivery['is_folder'] === 1 && !is_array($mass)) {
            return __("Выберите доставку");
        }

        if (!is_array($mass))
            return __("Для данного типа доставки не требуется дополнительных данных");

        $num = $mass['num'];
        asort($num);
        $enabled = $mass['enabled'];

        if ($delivery['city_select']) {
            $disp = "<div id='citylist'>";
            // Cтрана
            if ($delivery['city_select'] == 2) {
                $disabled = "disabled";
                $countryOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['citylist_country']);
                foreach ($countryOrm->getList(['country_id', 'name'], false, ['order' => 'name']) as $country) {
                    $disOpt .= "<option value='" . $country['name'] . "' for='" . $country['country_id'] . "'>" . $country['name'] . "</option>";
                }

                $class = 'citylist form-control';
                $star = null;
                if ($enabled['country']['req']) {
                    $class .= " req";
                    $star = '<span class="required">*</span>';
                }
                $disp .= "$star " . $enabled['country']['name'] . "<p> <select name='country_new' class='$class'><option value='' for='0'>-----------</option>$disOpt</select></p>";
            }

            // регион
            $rfId = 3159; // ID РФ
            $disOpt = "";
            $regionOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['citylist_region']);
            $regions = $regionOrm->getList(['region_id', 'name'], ['country_id' => sprintf('="%s"', $rfId)], ['order' => 'name']);
            foreach ($regions as $region) {
                $disOpt .= "<option value='" . $region['name'] . "' for='" . $region['region_id'] . "'>" . $region['name'] . "</option>";
            }
            $class = 'citylist form-control';
            $star = null;
            if ($enabled['state']['req']) {
                $class .= " req";
                $star = '<span class="required">*</span>';
            }
            $disp .= "$star " . $enabled['state']['name'] . "<p><select name='state_new' class='$class' $disabled><option value='' for='0'>-----------</option>$disOpt</select></p>";

            //Город
            $class = 'citylist form-control';
            $star = null;
            if ($enabled['city']['req']) {
                $class .= " req";
                $star = '<span class="required">*</span>';
            }
            $disp .= "$star " . $enabled['city']['name'] . "<p> <select name='city_new' class='$class' $disabled><option value='' for='0'>-----------</option></select></p></div>";
        }

        foreach ($num as $key => $value) {
            if ($delivery['city_select'] AND ($key == "state" OR $key == "city" OR $key == "country"))
                continue;
            $class = 'form-control';
            $required = null;
            if ($enabled[$key]['enabled'] == 1) {
                if ($enabled[$key]['req']) {
                    $class .= ' req';
                    $required = 'required';
                }
                
                $val=null;
                
                // Телефон
                if($key == 'tel' and !empty($_SESSION['UsersTel']))
                    $val = $_SESSION['UsersTel'];
                
                // ФИО
                if($key == 'fio' and !empty($_SESSION['UsersName']))
                    $val = $_SESSION['UsersName'];
                
                
                $disp .= '<p><input type="text" class="' . $class . '" value="'.$val.'" name="' . $key . '_new" ' .  $required . ' placeholder="' . $enabled[$key]['name'] . '"></p>';
            }
        }

        if(!empty($delivery['comment'])) {
            $disp = '<div class="well well-sm delivery-comment">'. $delivery['comment'] .'</div>' . $disp;
        }

        return $disp;
    }

    private function checkDeliveryMethodAllow($delivery)
    {
        $PHPShopCart = new PHPShopCart();
        $PHPShopSystem = new PHPShopSystem();

        if ((int) $delivery['sum_max'] > 0 && (int) $delivery['sum_max'] <= $PHPShopCart->getSum()) {
            throw new \Exception(__('Превышена максимальная сумма заказа'));
        }

        if ((int) $delivery['sum_min'] > 0 && (int) $delivery['sum_min'] >= $PHPShopCart->getSum()) {
            throw new \Exception(
                sprintf(__('Для данного способа доставки сумма в корзине должна быть больше: %s %s'), (int) $delivery['sum_min'], $PHPShopSystem->getDefaultValutaCode(true))
            );
        }
        
        // Сумма длин сторон
        if ((int) $delivery['sum_side_max'] > 0 && $PHPShopCart->getMaxSumSide() > (int) $delivery['sum_side_max']) {
            throw new \Exception(__('Превышена максимальная сумма длин сторон '.$delivery['sum_side_max'].' см'));
        }
        
        // Максимальная длина
        if ((int) $delivery['length_max'] > 0 && $PHPShopCart->getMaxLength() > (int) $delivery['length_max']) {
            throw new \Exception(__('Превышена максимальная длина '.$delivery['length_max'].' см'));
        }
        
        // Максимальная высота
        if ((int) $delivery['height_max'] > 0 && $PHPShopCart->getMaxHeight() > (int) $delivery['height_max']) {
            throw new \Exception(__('Превышена максимальная высота '.$delivery['height_max'].' см'));
        }
        
        // Максимальная ширина
        if ((int) $delivery['width_max'] > 0 && $PHPShopCart->getMaxWidth() > (int) $delivery['width_max']) {
            throw new \Exception(__('Превышена максимальная ширина '.$delivery['width_max'].' см'));
        }


        $weight = $PHPShopCart->getWeight();
        $volume_weight = $PHPShopCart->getVolumeWeight();
        
        if($volume_weight > $weight)
            $weight=$volume_weight;

        if((int) $weight > 0) {
            if ((int) $delivery['weight_max'] > 0 && (int) $delivery['weight_max'] <= $weight) {
                throw new \Exception(__('Превышен максимальный вес заказа'));
            }

            if ((int) $delivery['weight_min'] > 0 && (int) $delivery['weight_min'] >= $weight) {
                throw new \Exception(
                    sprintf(__('Для данного способа доставки вес в корзине должен быть больше: %s кг.'), (int) $delivery['weight_min'] / 1000)
                );
            }
        }
    }

    private function servers()
    {
        $servers=null;
        if (defined("HostID")) {
            $servers = " and servers REGEXP 'i" . HostID . "i'";
        } elseif (defined("HostMain"))
            $servers = " and (servers = '' or servers REGEXP 'i1000i')";

        return $servers;
    }

    private function clearVariables()
    {
        PHPShopParser::set('deliveryActive', '');
        PHPShopParser::set('deliveryChecked', '');
        PHPShopParser::set('deliveryIcon', '');
        PHPShopParser::set('deliveryDisabled', '');
        PHPShopParser::set('deliveryDisabledReason', '');
    }

    private function getOneDelivery($delivery, $selectedId = null, $isFolderSelected = false)
    {
        $html = '';
        $this->clearVariables();

        if ($this->isActiveDelivery($delivery, $selectedId)) {
            PHPShopParser::set('deliveryActive', 'active');
            PHPShopParser::set('deliveryChecked', 'checked');
            PHPShopParser::set('deliveryComment', $delivery['comment']);
            if(is_null($selectedId)) {
                $html .= '<IMG onload="UpdateDeliveryJq(' . (int) $delivery['id'] . ');" SRC="' . $this->getTemplateAsset('images/shop/flag_green.gif') . '"  style="display:none;">';
            } else {
                if(!$isFolderSelected) { // Завершаем выбор только если выбрана НЕ папка
                    $this->deliverySelected = true;
                } else {
                    $html .= '<IMG onload="UpdateDeliveryJq(' . (int) $delivery['id'] . ');" SRC="' . $this->getTemplateAsset('images/shop/flag_green.gif') . '"  style="display:none;">';
                }
            }
        }

        if(empty($delivery['is_folder']) || $this->getCountSubDeliveries((int) $delivery['id']) > 0) {
            PHPShopParser::set('deliveryId', $delivery['id']);
            PHPShopParser::set('deliveryPayment', $delivery['payment']);
            PHPShopParser::set('deliveryIcon', $delivery['icon']);
            PHPShopParser::set('deliveryTitle', $delivery['city']);
            PHPShopParser::set('deliveryComment', $delivery['comment']);

            try {
                $this->checkDeliveryMethodAllow($delivery);
                $this->allowedDeliveries++;
            } catch (\Exception $exception) {
                PHPShopParser::set('deliveryDisabled', 'disabled="disabled"');
                PHPShopParser::set('deliveryDisabledReason', $exception->getMessage());
            }

            $html .= ParseTemplateReturn($this->getTemplate(), true);
        }

        return $html;
    }

    private function getCountSubDeliveries($id)
    {
        $count = $this->orm->select(['COUNT(id) as count'], ['PID' => sprintf('="%s"', $id) . $this->servers()]);

        return (int) $count['count'];
    }

    private function isActiveDelivery($delivery, $selectedId = null)
    {
        // Если выбрана текущая доставка.
        if(!is_null($selectedId) && (int) $delivery['id'] === (int) $selectedId) {
            return true;
        }

        // Если выбран каталог доставки, а внутри него доставка по умолчанию
        if(!is_null($selectedId) && (int) $delivery['PID'] === (int) $selectedId && (int) $delivery['flag'] === 1) {
            return true;
        }

        // Если выбрана доставка, но не текущая
        if(!is_null($selectedId)) {
            return false;
        }
        
        return (int) $delivery['flag'] === 1; // По умолчанию.
    }

    private function getBackBtn($pid)
    {
        if($pid === 0) {
            return ''; // Корневой уровень, кнопка не нужна.
        }

        $parent = $this->orm->getOne(['*'], ['enabled' => '="1"', 'id' => sprintf('="%s"', $pid) . $this->servers()]);
        $childrens = $this->orm->getList(['*'], ['enabled' => '="1"', 'PID' => sprintf('="%s"', $parent['PID']) . $this->servers()], ['order' => 'num,city']);

        if(count($childrens) === 1) {
            return ''; // На уровне выше только 1 элемент. Кнопка не нужна.
        }

        return __('Выбрано') . ': ' . $parent['city'] . ' <A href="javascript:UpdateDeliveryJq(\'' . $parent['PID'] . '\')"><img src="' . $this->getTemplateAsset('images/shop/check_green.svg') . '" alt="" border="0" align="absmiddle">&nbsp;' . __('Выбрать другой способ доставки') . '</A> <BR><BR> ';
    }

    private function getTemplateAsset($asset)
    {
        if($this->isAjaxRequest) {
            return $GLOBALS['SysValue']['dir']['dir'] . '/' . $GLOBALS['SysValue']['dir']['templates'] . '/' . $_SESSION['skin'] . '/' . $asset;
        }

        return $GLOBALS['SysValue']['dir']['dir'] . $GLOBALS['SysValue']['dir']['templates'] . '/' . $_SESSION['skin'] . '/' . $asset;
    }

    private function getTemplate()
    {
        if(is_null($GLOBALS['SysValue']['templates']['delivery']) or
            !is_file(dirname(dirname(__DIR__)) . '/templates/' . $_SESSION['skin'] . '/' . $GLOBALS['SysValue']['templates']['delivery'])) {
            return dirname(dirname(__DIR__)) . '/lib/templates/order/delivery.tpl';
        }
        return dirname(dirname(__DIR__)) . '/templates/' . $_SESSION['skin'] . '/' . $GLOBALS['SysValue']['templates']['delivery'];
    }
}

/**
 * @deprecated since PHPShop 6.1.0. Use PHPShopOrderDelivery istead.
 */
function delivery($obj, $deliveryID, $sum = 0) {
    $PHPShopOrderDelivery = new PHPShopOrderDelivery();

    if(is_null($deliveryID) === false) {
        return [
            'dellist'   => $PHPShopOrderDelivery->getDeliveryMethodsById((int) $deliveryID),
            'adresList' => $PHPShopOrderDelivery->getAddressFields((int) $deliveryID)
        ];

    }
    $obj->set('orderDelivery', PHPShopText::div($PHPShopOrderDelivery->getDeliveryMethods(), 'none', false, 'seldelivery'));
}
?>