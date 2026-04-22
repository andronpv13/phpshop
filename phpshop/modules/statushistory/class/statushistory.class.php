<?php

class PHPShopStatusHistory {
    var $PHPShopOrm;
    
    // Конструктор
    function __construct() {
        $this->PHPShopOrm = new PHPShopOrm('phpshop_modules_status_history');
        $this->PHPShopOrm->debug = false;
    }
    
    public function add($order_id, $status, $adm = false) {
        if ($adm) 
            $user = $_SESSION['idPHPSHOP']; 
        else 
            $user = 0;
             
        $data = array (
            'unix_data' => time(),
            'user_id' => $user,
            'user_ip' => $_SERVER['REMOTE_ADDR'],
            'ouid' => $order_id,
            'status' => $status
        );
        
        $this->PHPShopOrm->insert($data, '');
    }
    
    public function delete($order_id) {
        $this->PHPShopOrm->delete(array('ouid' => "='$order_id'"));        
    }
    
    public function table($order_id) {
        $dis = '';
        $color = $this->get_array('order_status', 'color');
        $statuses = $this->get_array('order_status');
        $statuses[0] = 'Новый заказ';
        $users = $this->get_array('users');
        $users[0] = 'Неизвестен';
        
        $PHPShopInterface = new PHPShopInterface();
        $PHPShopInterface->checkbox_action = false;
        $PHPShopInterface->setCaption(array("Дата", "20%"),array("Пользователь", "20%"),  array("Статус", "40%"));

        $data = $this->PHPShopOrm->select(array('*'), array('ouid' => "='$order_id'"), array('order' => 'id ASC'), array('limit' => 50));
        if (is_array($data)) {
            foreach ($data as $val) {
                $PHPShopInterface->setRow(array('name' => PHPShopDate::dataV($val['unix_data'])), array('name' => $users[$val['user_id']].'<br>'.$val['user_ip']), array('name' => '<span class="hidden-xs" style="color:' . $color[$val['status']] . '">' . $statuses[$val['status']] . '</span>', 'class' => 'label-link'));
            }
        return '<table class="table table-hover">'.$PHPShopInterface->_CODE.'</table>';    
        }
        
            
    }
    
    private function get_array($table, $field = 'name') {
        $res = array();
        $PHPShopOrm = new PHPShopOrm('phpshop_' . $table);
        $PHPShopOrm->debug = false;
        $data = $PHPShopOrm->select(array('id', $field), false, false, array('limit' => 100));
        if (is_array($data)) {
            foreach ($data as $val)
                $res[$val['id']] = $val[$field];
        }
        return $res;
    }
    
}

?>