<?php

function UID_panorama_hook($obj, $data, $rout) {

    if ($rout == "MIDDLE") {

        $img_panorama360 = trim($data['img_panorama360']);

        if ($img_panorama360) {

            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['panorama360']['panorama360_system']);
            $option = $PHPShopOrm->select();

            if (empty($option['frame']))
                $option['frame'] = 28;

            $obj->set('framePanorama', $option['frame']);
            $obj->set('imgPanorama', $img_panorama360);
            $obj->set('panorama360', PHPShopParser::file($GLOBALS['SysValue']['templates']['panorama360']['modal_forma'], true, false, true));
        }
    }
}

$addHandler = array(
    'UID' => 'UID_panorama_hook'
);
