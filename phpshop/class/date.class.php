<?php

/**
 * Ѕиблиотека работы с датами
 * @author PHPShop Software
 * @version 1.4
 * @package PHPShopClass
 * @subpackage Helper
 */
class PHPShopDate {

    /**
     * ѕреобразование даты из Unix к строковый вид. 
     * —иноним PHPShopDate::dataV()
     * @return string 
     */
    static function get($nowtime = false, $full = false, $revers = false, $delim = '-', $months_enabled = false) {
        return PHPShopDate::dataV($nowtime, $full, $revers, $delim, $months_enabled);
    }

    /**
     * ѕреобразование даты из Unix к строковый вид.
     * @param int $nowtime формат даты в Unix
     * @param bool $full вывод часов и минут
     * @param bool $revers обратна€ строка даты
     * @return string
     */
    static function dataV($nowtime = false, $full = true, $revers = false, $delim = '-', $months_enabled = false) {

        if (empty($nowtime))
            $nowtime = date("U");

        $Months = array("01" => "€нвар€", "02" => "феврал€", "03" => "марта",
            "04" => "апрел€", "05" => "ма€", "06" => "июн€", "07" => "июл€",
            "08" => "августа", "09" => "сент€бр€", "10" => "окт€бр€",
            "11" => "но€бр€", "12" => "декабр€");
        $d_array = array(
            'y' => date("Y", $nowtime),
            'm' => date("m", $nowtime),
            'd' => date("d", $nowtime),
            'h' => date("H:i", $nowtime)
        );

        if ($months_enabled)
            $d_array['m'] = $Months[$d_array['m']];

        if (!empty($revers))
            $time = $d_array['y'] . $delim . $d_array['m'] . $delim . $d_array['d'];
        else
            $time = $d_array['d'] . $delim . $d_array['m'] . $delim . $d_array['y'];

        if (!empty($full))
            $time.=" " . $d_array['h'];

        return $time;
    }

    /**
     * ѕреобразование даты из строкового вида в Unix
     * @param string $data дата в формате строки
     * @param string $delim разделитель даты [-] или [.]
     * @return string
     */
    static function GetUnixTime($data, $delim = '-',$revers = false) {
        $array = explode($delim, $data);
        
        if(empty($revers))
        $time = @mktime(date('H'), date('i'), date('s'), $array[1], $array[0], $array[2]);
        else $time = @mktime(date('H'), date('i'), date('s'), $array[1], $array[2], $array[0]);

        return $time;
    }

    /**
     * ѕроверка выходных дней
     * @param type $time формат даты в Unix
     * @return bool
     */
    static function isweekend_hook($time) {
        $weekday = date('w', $time);
        return ($weekday == 0 || $weekday == 6);
    }

    /**
     * –асчет даты доставки с учетом выходных дней
     * @param int $time формат даты в Unix
     * @param int $until час доставки на следующий день
     * @param array $day массив дней увеличени€ доставки
     * @return string
     */
    static function setDeliveryDate_hook($time,$until=17,$day=array("+2 day","+1 day")) {

        if (PHPShopDate::isweekend_hook($time))
            $result = strtotime("next Monday");
        else {

            if (date('H', $time) > $until)
                $result = strtotime($day[0]);
            else
                $result = strtotime($day[0]);
        }

        if (PHPShopDate::isweekend_hook($result))
            $result = strtotime("next Monday");

        return $result;
    }

}

?>