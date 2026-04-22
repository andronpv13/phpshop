<?php

include_once("phpshop/core/shop.core.php");
include_once dirname(__DIR__) . '/class/include.php';

class PHPShopBranches extends PHPShopShopCore {

    public function __construct() {

        $this->debug = false;
        $this->path = '/branches';
        $this->action = array("nav" => "index");

        parent::__construct();
    }

    public function index() {

        $Branches = new Branches();

        $this->title = 'Пункты выдачи';
        $this->set('branches_page_title', $this->title);

        $this->description = 'Запчасти для газовых котлов. Контакты наших пунктов выдачи в России.';
        $this->keywords = 'Пункты выдачи ALVATER, запчасти для котлов, насос котла';

        $branches = $Branches->getBranchesCoords();
        foreach ($branches as $key => $city) {
            foreach ($city as $k => $branch) {
                $branches[$key][$k]['name'] = PHPShopString::win_utf8($branch['name']);
            }
        }

        PHPShopParser::set('branches_active', 'active');

        $this->set('branches_yandex_key', $Branches->options['yandex_api_key']);
        $this->set('branches_branches_coords', json_encode($branches));
        $this->set('branches_cities', $Branches->getCitiesInHTML());
        $this->set('branches_current_city_name', $Branches->getCurrentCityName());
        $this->set('branches_branches', $Branches->getBranchesInHTML());

        $this->parseTemplate($GLOBALS['SysValue']['templates']['branches']['branches_page_template'], true);
    }
}
?>