<?php

if (!defined("OBJENABLED"))
    exit(header('Location: /?error=OBJENABLED'));

class PHPShopDeliverycalcElement extends PHPShopElements {

    function __construct() {
        $this->debug = false;
        $this->objBase = $GLOBALS['SysValue']['base']['deliverycalc']['deliverycalc_system'];
       
        parent::__construct();
        $this->data = $this->PHPShopOrm->select();

    }

    // Вывод ссылок
    function deliverycalc_end() {
        $data = $this->data;

        if (!empty($data['target'])) {

            $dirs = @explode(",", $data['target']);
            foreach ($dirs as $dir)
                if (!empty($dir))
                    if (strpos($_SERVER['REQUEST_URI'], trim($dir)) or $_SERVER['REQUEST_URI'] == trim($dir)) {

                        $this->set('pageContent', $data['code'], true);
                    }
        }
    }
    
    
    function deliverycalc_start(){
         if (empty($this->data['target']) and !empty($this->data['code'])) 
              $this->set('deliverycalc', $this->data['code']);
        
    }

}

?>