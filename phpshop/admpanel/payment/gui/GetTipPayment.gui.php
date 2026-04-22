<?php
// Типы оплат
function TipPayment($payment) {
    $TIP = array(
        "message" => __("Сообщение"),
        "bank" => __("Счет в банк"),
        "modules" => __("Модуль платежной системы")
    );

    foreach ($TIP as $k => $v)
        if ($k == $payment)
            return $v;
    return __("Оплата")." " . $payment;
}

// Выбор файла
function GetTipPayment($dir) {

    $path = "../../payment/";
    $arr=null;

    if ($dh = @opendir($path)) {

        while (($file = readdir($dh)) !== false) {
            if ($file != "." && $file != "..") {
                if (is_dir($path . $file)) {
                    if ($dir == $file)
                        $s = "selected";
                    else
                        $s = "";
                    $arr[] = array(TipPayment($file), $file, $s);
                }
            }
        }
        closedir($dh);
    }

    $arr[] = array(TipPayment('modules'), 'modules', $dir === 'modules' ? 'selected' : '');

    if (is_array($arr))
        return $arr;
    else
        return null;
}

?>