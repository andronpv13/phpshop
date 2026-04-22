<?php

include_once dirname(__DIR__) . '/Xml/BaseAvitoXml.php';
include_once dirname(__DIR__) . '/Xml/AvitoPriceInterface.php';

/**
 * XML остатки
 * @author PHPShop Software
 * @version 1.1
 */
class AvitoStock extends BaseAvitoXml implements AvitoPriceInterface {

    public static function getXml($product) {

        if ($product['items'] < 0)
            $product['items'] = 0;

        // Ключ обновления
        if (Avito::getOption('type') == 2)
            $product['id'] = $product['uid'];
        
        // Лимит остатков
        if(!empty(Avito::getOption('export_items_limit')) and $product['items'] > Avito::getOption('export_items_limit'))
            $product['items']=Avito::getOption('export_items_limit');
            
        $xml = '<item>
        <id>' . $product['id'] . '</id>
        <stock>' . $product['items'] . '</stock>
       </item>';

        return $xml;
    }

    public function setAds() {
        $this->xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $this->xml .= '<items date="' . date('Y-m-d\TH:i:s') . '" formatVersion="1">';

        $products = $this->getProducts($_GET['getall']);

        foreach ($products as $product) {
            $this->xml .= static::getXml($product);
        }

        $this->xml .= '</items>';
    }

}

?>