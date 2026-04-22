<?php

namespace PaySystem;

require_once("LanguageHelper.php");

class VATs {

    const vatRateOf20 = array(
        "id" => 1,
        "textVar" => "vatRateOf",
        "ending" => " 20%",
    );
    const vatRateOf10 = array(
        "id" => 2,
        "textVar" => "vatRateOf",
        "ending" => " 10%",
    );
    const vatRateCalculated20_120 = array(
        "id" => 3,
        "textVar" => "vatRateCalculated",
        "ending" => " 20/120",
    );
    const vatRateCalculated10_110 = array(
        "id" => 4,
        "textVar" => "vatRateCalculated",
        "ending" => " 10/110",
    );
    const vatRateOf0 = array(
        "id" => 5,
        "textVar" => "vatRateOf",
        "ending" => " 0%",
    );
    const vatIsNotAppearing = array(
        "id" => 6,
        "textVar" => "vatIsNotAppearing",
        "ending" => "",
    );

    private static function getText($textVar) {
        return LanguageHelper::getInstance()->validateAndGetTextValue($textVar, "vat");
    }

    public static function getList() {
        $result = array();
        $reflectionClass = new \ReflectionClass(__CLASS__);
        foreach ($reflectionClass->getConstants() as $vat) {
            $result[] = array(
                "id" => $vat['id'],
                "name" => self::getText($vat['textVar']) . $vat['ending'],
            );
        }
        return $result;
    }

}

?>
