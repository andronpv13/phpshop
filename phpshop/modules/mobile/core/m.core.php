<?php

if (!defined("OBJENABLED"))
    exit(header('Location: /?error=OBJENABLED'));

class PHPShopM extends PHPShopCore {

    /**
     * Конструктор
     */
    function __construct() {

        $this->debug = false;
        parent::__construct();

    }

    function index() {
        header('Location: /?mobile=true');

    }

}

?>
