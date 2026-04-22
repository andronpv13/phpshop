<?php

/**
 * Генерация календаря
 * @package PHPShopElementsDepricated
 * @param int $year год
 * @param int $month месяц
 * @return string
 */
function makeCalendar($year, $month) {
    global $SysValue,$link_db;

    // Получаем номер дня недели для 1 числа месяца. Корректируем
    // его, чтобы воскресенье соответствовало числу 7, а не числу 0.
    if (function_exists('GregorianToJD'))
        $wday = JDDayOfWeek(GregorianToJD($month, 1, $year), 0);
    if ($wday == 0) $wday = 7;
    // Начинаем с этого числа в месяце (если меньше нуля
    // или больше длины месяца, тогда в календаре будет пропуск).
    $n = - ($wday - 2);
    $cal = array();

    for ($y=0; $y<6; $y++) {

        $row = array();
        $notEmpty = false;
        // Цикл внутри строки по дням недели.
        for ($x=0; $x<7; $x++, $n++) {
            // Текущее число >0 и < длины месяца?
            if (checkdate($month, $n, $year)) {
                // Да. Заполняем клетку.
                $timeststart=mktime(0,0,0,$month,$n,$year);
                $timestend=mktime(23,59,59,$month,$n,$year);
                $sql="select * from ".$SysValue['base']['table_name8']." where ((datau>=".$timeststart.") AND (datau<=".$timestend."));";
                $result=mysqli_query($link_db,$sql);
                @$SysValue['sql']['num']++;
                @$num_rows=mysqli_num_rows($result);
                if ($num_rows) {
                    $row[]='<A href="../news/?timestamp='.$timeststart.'"><B>'.$n.'</B></A>';
                } else {
                    $row[]= $n;
                }


                $notEmpty = true;
            } else {
                // Нет. Клетка пуста.
                $row[] = "";
            }
        }

        // Если в данной строке нет ни одного непустого элемента,
        // значит, месяц кончился.
        if (!$notEmpty) break;
        // Добавляем строку в массив.
        $cal[] = $row;
    }
    return $cal;
}

/**
 * Месяцы
 * @package PHPShopElementsDepricated
 * @param int $mo числовое обозначение месца
 * @return string
 */
function moname($mo="1") {

    if ($mo=="12") return "Декабрь";
    if ($mo=="11") return "Ноябрь";
    if ($mo=="10") return "Октябрь";
    if ($mo=="9") return "Сентябрь";
    if ($mo=="8") return "Август";
    if ($mo=="7") return "Июль";
    if ($mo=="6") return "Июнь";
    if ($mo=="5") return "Май";
    if ($mo=="4") return "Апрель";
    if ($mo=="3") return "Март";
    if ($mo=="2") return "Февраль";
    if ($mo=="1") return "Январь";

    return $mo;
}

/**
 * Элемент Календарь
 * @package PHPShopCoreFunction
 * @author PHPShop Software
 * @param int $year год
 * @param int $month месяц
 * @return string
 */
function calendar($obj,$year=false,$month=false) {
    global $SysValue,$PHPShopSystem,$link_db;

        
    $disp=null;
    
    if($PHPShopSystem->ifSerilizeParam('admoption.user_calendar')) {

        if (!$year) {
            $year=date("Y");
        } //Если не прислали управление годом - активен текущий год
        if (!$month) {
            $month=date("m");
        }//Если не прислали управление месяцем - активен текущий месяц



        $cal = makeCalendar($year, $month); // Генерация календаря
        $timestamp=mktime(0,0,0,$month,1,$year);

        foreach ($cal as $row) {
            $disp.='<tr>';
            foreach ($row as $i=>$v) {
                if ($i==6) {
                    $st='style="color:red"';
                } else {
                    $st="";
                }
                if (!$v) {
                    $v="&nbsp;";
                }
                $disp.='<td '.$st.'>'.$v.'</td>';
            }
            $disp.='</tr>';
        }

        $cmonth=date("m");
        $cyear=date("Y");


        $numyearleft=$year-1;
        $numyearright=$year+1;
        $nummonthleft=$month-1;
        $nummonthright=$month+1;

        // Проверяем было ли что год назад
        $timestampyear=mktime(0,0,0,1,1,$year);
        $sql="select * from ".$SysValue['base']['table_name8']." where ((datau<=".$timestampyear."));";
        $result=mysqli_query($link_db,$sql);
        @$SysValue['sql']['num']++;
        @$num_rows=mysqli_num_rows($result);
        // ПРоверяем было ли что год назад

        if ($num_rows) {
            if ($year>1) $yearleft='<A href="javascript:calres('.$numyearleft.',12)"><<</A> ';
        }

        if ($year<$cyear) $yearright='<A href="javascript:calres('.$numyearright.',1)">>></A> ';

        // Проверяем было ли что месяц назад
        $timestampyear=mktime(0,0,0,$month,1,$year);
        $sql="select * from ".$SysValue['base']['table_name8']." where ((datau<=".$timestampyear."));";
        $result=mysqli_query($link_db,$sql);
        @$SysValue['sql']['num']++;
        @$num_rows=mysqli_num_rows($result);
        //ПРоверяем было ли что месяц назад


        if ($num_rows) {
            //Если было, определяем первый месяц, в котором это было
            $go=0;
            $mm=$month;
            $num_rows=0;
            while (($go==0) && ($mm!=1)) {

                $timestamps=mktime(0,0,0,$mm-1,1,$year);
                $timestampe=mktime(0,0,0,$mm,1,$year);
                $sql="select * from ".$SysValue['base']['table_name8']." where ((datau>=".$timestamps.") AND (datau<=".$timestampe."));";
                $result=mysqli_query($link_db,$sql);
                @$SysValue['sql']['num']++;
                @$num_rows=mysqli_num_rows($result);
                if ($num_rows) {
                    $go=1;
                } else {
                    $mm=$mm-1;
                }
            }
            $nummonthleft=$mm-1;
            if ($month>1) {
                $monthleft='<A href="javascript:calres('.$year.','.$nummonthleft.')"><<</A> ';
            } else {
                $monthleft='<A href="javascript:calres('.$numyearleft.',12)"><<</A> ';
            }
        }

        // Проверяем есть ли что месяц вперед
        $timestampyear=mktime(0,0,0,$month+1,1,$year);
        $sql="select * from ".$SysValue['base']['table_name8']." where ((datau>=".$timestampyear."));";
        $result=mysqli_query($link_db,$sql);
        @$SysValue['sql']['num']++;
        @$num_rows=mysqli_num_rows($result);
        // Проверяем есть ли что месяц вперед

        if ($num_rows) {
            //Если было, определяем первый месяц, в котором это было

            if (($month>=$cmonth) && ($year==$cyear)) {
                $monthright='';
            }  else {
                if ($month<12) {
                    //Если было, определяем первый месяц, в котором это было
                    $go=0;
                    $mm=$month;
                    $num_rows=0;
                    while (($go==0) && ($mm!=12) ) {
                        $timestamps=mktime(0,0,0,$mm+1,1,$year);
                        $timestampe=mktime(0,0,0,$mm+2,1,$year);
                        $sql="select * from ".$SysValue['base']['table_name8']." where ((datau>=".$timestamps.") AND (datau<=".$timestampe."));";
                        $result=mysqli_query($link_db,$sql);
                        @$SysValue['sql']['num']++;
                        @$num_rows=mysqli_num_rows($result);
                        if ($num_rows) {
                            $go=1;
                        } else {
                            $mm=$mm+1;
                        }
                    }
                    $nummonthright=$mm+1;

                    $monthright='<A href="javascript:calres('.$year.','.$nummonthright.')">>></A> ';
                } else {
                    $monthright='<A href="javascript:calres('.$numyearright.',1)">>></A> ';
                }
            }
        }


        setlocale(LC_ALL,'');
        $yearname=strftime('%Y',$timestamp); //Название активного года
        $monthname=moname($month);


        $SysValue['other']['yearleft']=$yearleft;
        $SysValue['other']['yearname']=$yearname;
        $SysValue['other']['yearright']=$yearright;
        $SysValue['other']['monthleft']=$monthleft;
        $SysValue['other']['monthname']=$monthname;
        $SysValue['other']['monthright']=$monthright;
        $SysValue['other']['dispCalendarDays']=$disp;

        if(function_exists('ParseTemplateReturn')) {
            $SysValue['other']['calendar']=ParseTemplateReturn("calendar/calendar_main_forma.tpl");
        }
        else {
            return PHPShopParser::file('../../'.$SysValue['dir']['dir'].$SysValue['dir']['templates'].chr(47).$_SESSION['skin']."/calendar/calendar_main_forma.tpl",true);
        }
    }
}

?>