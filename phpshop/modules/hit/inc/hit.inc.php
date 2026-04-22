<?php

if (!defined("OBJENABLED"))
    exit(header('Location: /?error=OBJENABLED'));

class AddToTemplateHitElement extends PHPShopProductIndexElements {
    var $check_index=null;

    function __construct() {
        parent::__construct();
    }

    public function renderHit() {

        if ($this->PHPShopNav->index($this->check_index)) {

            $PHPShopOrm = new PHPShopOrm('phpshop_modules_hit_system');
            $options = $PHPShopOrm->select();

            $this->cell = $this->PHPShopSystem->getParam('num_vitrina');

            (int) $options['hit_main'] > 0 ? $this->limit = (int) $options['hit_main'] : $this->limit = 20;

            $this->set('productInfo', $this->lang('productInfo'));

            $where['hit'] = "='1'";
            $where['enabled'] = "='1'";
            $where['parent_enabled'] = "='0'";

            // Мультибаза
            $queryMultibase = $this->queryMultibase();
            if (!empty($queryMultibase))
                $where['enabled'].= ' ' . $queryMultibase;

            $result = $this->select(array('*'), $where, array('order' => 'RAND()'), array('limit' => $this->limit), __FUNCTION__);

            if(isset($result['id'])) {
                $this->dataArray[] = $result;
            } else {
                $this->dataArray = $result;
            }

            if(!is_array($this->dataArray)) {
                $this->set('hitMain', '');
                $this->set('hitMainHidden', 'hide');
            } else {
                $this->product_grid($this->dataArray, $this->cell, $this->template);

                $this->set('hitMain', $this->compile());
            }
        }
    }
}

$AddToTemplateHitElement = new AddToTemplateHitElement();
$AddToTemplateHitElement->renderHit();

?>