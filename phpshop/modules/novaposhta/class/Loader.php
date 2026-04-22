<?php

include_once dirname(__DIR__) . '/class/Request.php';

/**
 * Методы загрузки справочников с Новой почты.
 *
 * Class Loader
 */
class Loader {

    /**
     * @var Request
     */
    private $request;

    private $storedWh = array();

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function getWarehouses()
    {
        $PHPShopOrm = new PHPShopOrm('phpshop_modules_novaposhta_warehouses');
        $PHPShopOrm->query('TRUNCATE `phpshop_modules_novaposhta_warehouses`');

        $loaded = 0;
        $total =  1;
        for($page = 1; $loaded < $total; $page++) {
            $result = $this->request->post(Request::ADDRESS_MODEL, Request::WAREHOUSES_METHOD, [
                'Page' => $page,
                'Limit' => 500
            ]);
            $total = $result['info']['totalCount'];

            if($result['success']) {
                $query_values = [];
                foreach ($result['data'] as $warehouse) {
                    $query_values[] = "(" . "'" . str_replace("'", '`', $warehouse['Description']) . "','" .
                        $warehouse['Ref'] . "','" . str_replace("'", '`', $warehouse['ShortAddress']) . "','" . $warehouse['Phone'] . "','" .
                        $warehouse['TypeOfWarehouse'] . "','" . $warehouse['Number'] . "','" . $warehouse['SettlementRef'] . "','" .
                        $warehouse['Longitude'] . "','" . $warehouse['Latitude'] . "')";
                    $loaded++;
                }

                $PHPShopOrm->query('INSERT INTO `phpshop_modules_novaposhta_warehouses` (`title`, `ref`, `address`, `phone`, `type`, `number`, `city`, `longitude`, `latitude`) VALUES ' . implode(',', $query_values));
            }
        }
        $PHPShopOrm->query('UPDATE `phpshop_modules_novaposhta_system` SET `last_warehouses_update`="' . time() . '" WHERE `id`=1');
    }

    public function getWarehouseTypes()
    {
        $result = $this->request->post(Request::ADDRESS_MODEL, Request::WAREHOUSE_TYPES_METHOD);

        if($result['success']) {
            $query_values = array();
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['novaposhta']['novaposhta_whtypes']);
            $PHPShopOrm->query('TRUNCATE ' . $GLOBALS['SysValue']['base']['novaposhta']['novaposhta_whtypes']);
            foreach ($result['data'] as $type) {
                $query_values[] = '(' . '"' . $type['Description'] . '","' . $type['Ref'] . '")';
            }
            $PHPShopOrm->query('INSERT INTO ' . $GLOBALS['SysValue']['base']['novaposhta']['novaposhta_whtypes'] . ' (`title`, `ref`) VALUES ' . implode(',', $query_values));

            $PHPShopOrm->query('UPDATE ' . $GLOBALS['SysValue']['base']['novaposhta']['novaposhta_system'] . ' SET `last_whtypes_update`="' . time() . '" WHERE `id`=1');
        }
    }

    public function getCities()
    {
        $this->setStoredWh();

        $PHPShopOrm = new PHPShopOrm('phpshop_modules_novaposhta_cities');
        $PHPShopOrm->query('TRUNCATE `phpshop_modules_novaposhta_cities`');

        $result = $this->request->post(Request::ADDRESS_MODEL, Request::SETTLEMETS_METHOD, array('Warehouse' => 1, 'Page' => 1));

        $this->storeCities($result);

        if($result['success']) {
            $pages = ceil($result['info']['totalCount'] / Request::API_PER_PAGE);
            for($i = 2; $i <= $pages; $i++) {
                $this->storeCities($this->request->post(Request::ADDRESS_MODEL, Request::SETTLEMETS_METHOD, array('Warehouse' => 1, 'Page' => $i)));
            }

            $PHPShopOrm->query('UPDATE `phpshop_modules_novaposhta_system` SET `last_cities_update`="' . time() . '" WHERE `id`=1');
        }
    }

    /**
     * Сохранение населенных пунктов со страницы.
     * @param array $result
     */
    private function storeCities($result)
    {
        if($result['success']) {
            $query_values = array();
            $PHPShopOrm = new PHPShopOrm('phpshop_modules_novaposhta_cities');
            foreach ($result['data'] as $area) {
                if(isset($this->storedWh[$area['Ref']]) && $this->storedWh[$area['Ref']] == 1) {
                    $query_values[] = '(' . '"' .
                        $area['Description'] . '","' .
                        $area['Ref'] . '","' .
                        $area['Latitude'] . '","' .
                        $area['Longitude'] . '","' .
                        $area['Area'] . '","' .
                        $this->getFormattedAreaDescription($area) . '","' .
                        $this->getFormattedAreaDescriptionRu($area) . '")';
                }
            }
            if(count($query_values) > 0) {
                $PHPShopOrm->query('INSERT INTO `phpshop_modules_novaposhta_cities` (`city`, `ref`, `latitude`, `longitude`, `region`, `area_description`,  `area_description_ru`) VALUES ' . implode(',', $query_values));
            }
        }
    }

    /**
     * @param string $type
     * @return string
     */
    private function getSettlementType($type)
    {
        switch ($type) {
            case 'село':
                $shortType = 'с.';
                break;
            case 'селище міського типу':
                $shortType = 'смт.';
                break;
            case 'місто':
                $shortType = 'м.';
                break;
            case 'поселок городского типа':
                $shortType = 'пгт.';
                break;
            case 'город':
                $shortType = 'г.';
                break;
            default:
                $shortType = $type;
        }

        return $shortType;
    }

    /**
     * @param array $area
     * @return string
     */
    private function getFormattedAreaDescription($area)
    {
        // Почему-то с БД НП Киевская обл идет без области
        if(trim($area['AreaDescription']) === 'Київська') {
            $area['AreaDescription'] = $area['AreaDescription'] . ' обл.';
        }

        $area['AreaDescription'] = str_replace('область', 'обл.', $area['AreaDescription']);

        $region = '';
        if(!empty($area['RegionsDescription'])) {
            $region = $area['RegionsDescription'] . ', ';
        }

        return $this->getSettlementType($area['SettlementTypeDescription']) . ' ' . $area['Description'] . ', ' . $region . $area['AreaDescription'];
    }

    /**
     * @param array $area
     * @return string
     */
    private function getFormattedAreaDescriptionRu($area)
    {
        // Почему-то с БД НП Киевская обл идет без области
        if(trim($area['AreaDescription']) === 'Київська') {
            $area['AreaDescription'] = $area['AreaDescription'] . ' обл.';
        }

        $area['AreaDescription'] = str_replace('область', 'обл.', $area['AreaDescription']);

        $region = '';
        if(!empty($area['RegionsDescriptionRu'])) {
            $region = $area['RegionsDescriptionRu'] . ', ';
        }

        return $this->getSettlementType($area['SettlementTypeDescriptionRu']) . ' ' . $area['DescriptionRu'] . ', ' . $region . $area['AreaDescriptionRu'];
    }

    // Костыль для отсева городов, в которых нет ПВЗ. Когда API НП начнет отдавать корректно города с параметром Warehouse => 1 - убрать.
    private function setStoredWh()
    {
        $PHPShopOrm = new PHPShopOrm('phpshop_modules_novaposhta_warehouses');

        $result = $PHPShopOrm->getList(array('city'));

        foreach ($result as $pvz) {
            $this->storedWh[$pvz['city']] = 1;
        }
    }
}