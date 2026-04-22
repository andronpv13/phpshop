<?php

/**
 * Имя каталога
 * @param int $id ИД категории
 * @return string 
 */
function getfullname($id) {
    global $PHPShopShopCatalogElement;

    $parent = $PHPShopShopCatalogElement->CategoryArray[$id]['parent_to'];

    if (!empty($parent))
        $name = $PHPShopShopCatalogElement->CategoryArray[$parent]['name'] . ' / ' . $PHPShopShopCatalogElement->CategoryArray[$id]['name'];
    else
        $name = $PHPShopShopCatalogElement->CategoryArray[$id]['name'];

    return $name;
}

/**
 * Обработчик сравнения
 * @author PHPShop Software
 * @version 1.2
 * @package PHPShopTest
 */
class PHPShopCompare extends PHPShopCore {

    /**
     * Конструктор
     */
    function __construct() {
        parent::__construct();

        // Навигация хлебные крошки
        $this->navigation(false, __('Сравнение'));
    }

    /**
     * Экшен по умолчанию
     */
    function index() {
        global $SysValue, $PHPShopSystem, $PHPShopValutaArray, $link_db;

        $limit = 4; //Максимум товаров для сравнения

        $LoadItems['Valuta'] = $PHPShopValutaArray->getArray();
        $LoadItems['System'] = $PHPShopSystem->getArray();

        if (!empty($_SESSION['compare']))
            $copycompare = $_SESSION['compare'];
        else
            $copycompare = array();


        if (!($SysValue['nav']['id'] == "ALL")) {
            $SysValue['nav']['id'] = intval($SysValue['nav']['id']);
        }

        $COMCID = 0;

        if ($SysValue['nav']['nav'] == "COMCID") {
            if (isset($SysValue['nav']['id']) && ($SysValue['nav']['id'])) {
                $COMCID = $SysValue['nav']['id'];
            }
        }

        // Подготовка массивов товаров и категорий
        if (is_array($copycompare))
            sort($copycompare); //Сортируем по категориям
        $oldcatid = ''; //Первоначальный идентификатор категории


        if (is_array($copycompare))
            foreach ($copycompare as $id => $val) {

                //Если идентификатор категории товара изменился, меняем категорию
                if ($oldcatid != $val['category']) {

                    $catid = $val['category'];
                    $oldcatid = $catid; //Меняем идентификатор.
                    $cats[$catid] = getfullname($catid);
                }
                $goods[$oldcatid][$id]['name'] = $val['name'];
                $goods[$oldcatid][$id]['id'] = $val['id'];
                $goods[$oldcatid][$id]['url'] = $val['url'];
                $compare[$id] = $goods[$oldcatid][$id];
            }



        $dis = "";

        if (empty($cats))
            $cats = 0;

        if (is_array($cats))
            foreach ($cats as $catid => $name) {
                if ((count($goods[$catid]) > 1) && (count($goods[$catid]) <= $limit)) {
                    if ($catid != $COMCID) {
                        $as = '<b>' . __('Сравнить в категории') . '</b>: <a href="../compare/COMCID_' . $catid . '.html#list" title="' . __('Сравнить в') . ' ' . $name . '">';
                        $ae = '</a>';
                    } else {
                        $as = '<b>';
                        $ae = '</b>';
                    }
                    $dis.='

		<tr><td colspan="2">' . $as . $name . $ae . '</td></tr>';
                    $green[] = $catid; //Добавить каталог в разрешенные
                } elseif (count($goods[$catid]) > $limit) {
                    $dis.='
		<tr><td><br><b>' . $name . '</b> <p class="text-danger">' . __('Cлишком много товаров в сравнении, максимум <b>' . $limit . '</b>. Удалите лишние') . '.</p></td>
		<td width="50"></td></tr>';
                } else {
                    $dis.='
  <tr><td id=allspec colspan="2"><b>' . $name . '</b> </td>
  </tr>';
                }
                foreach ($goods[$catid] as $id => $val) {
                    $dis.='<tr><td>' . $val['name'] . ' </td><td width="50" class="text-center"><a href="../compare/DID_' . $val['id'] . '.html" class="btn btn-danger btn-xs" title="{Удалить}"><span>X</span></a></td></tr>';
                }
            }

        // Дополнение - сравнить по всем категориям
        if (is_array($cats) and count($cats) > 1) { //Если больше двух каталогов
            $name = 'по всем категориям';
            if ((count($compare) > 1) && (count($compare) <= $limit)) {
                if ($COMCID != "ALL") {
                    $as = '<a href="../compare/COMCID_ALL.html#list" title="' . __('Сравнить по ВСЕМ категориям') . '"><span class="glyphicon glyphicon-ok"></span> ' . __('Сравнить') . ' ';
                    $ae = '</a>';
                } else {
                    $as = '<b>' . __('Сравнивается') . ': ';
                    $ae = '</b>';
                }
                $dis.='
		<tr><td colspan="2" id=allspec>' . $as . $name . $ae . '</td>
		<!--<td>&nbsp;</td>--></tr>';
                $green[] = "ALL"; //Добавить каталог в разрешенные
            } elseif (count($compare) > $limit) {
                $dis.='
		<tr><td colspan="2"><b>' . $name . '</b> <p class="text-danger">' . __('Cлишком много товаров в сравнении, максимум <b>' . $limit . '</b>. Удалите лишние') . '.</p></td>
		</tr>';
            } else {
                $dis.='
		<tr><td colspan="2"><b>' . $name . '</b> <p class="text-danger">' . __('Недостаточно товаров для сравнения. Добавьте еще товары из этой категории') . '</p></td>
		</tr>';
            }
        }

        // Вывод управляющего интерфейса
        $disp = '<table class="table table-bordered table-responsive">' . $dis . '</table>';

        // Выбор каталога для показа
        if (!$COMCID) { //Если не указан каталог
            if (is_array($green) and count($green) > 0) {//Если хоть один каталог можно показать
                krsort($green);
                foreach ($green as $c) {
                    $COMCID = $c;
                    break;
                }
            } else {
                $disp.='<p class="text-danger">' . __('Выберите товары для сравнения') . '.</p>';
            }
        }

        // Обработка действия пользователей
        if ($SysValue['nav']['nav'] == "DID") {
            $id = $SysValue['nav']['id'];
            if ($id == "ALL") {
                $_SESSION['compare'] = null;
                unset($_SESSION['compare']);
                header("Location: ../compare/");
            } else {
                unset($_SESSION['compare'][$id]);
                header("Location: ../compare/");
            }
        }

        $catid = $COMCID;

        // Сравение
        if (!empty($_SESSION['compare']))
            if (($COMCID && (count($goods[$catid]) > 1) && (count($goods[$catid]) <= $limit)) ||
                    ((($COMCID == "ALL") && (count($_SESSION['compare']) > 1) && (count($_SESSION['compare']) <= $limit)))) { //Если выбран каталог сравнения
                if ($COMCID == "ALL") {
                    $comparing = __('все категории');
                } else {
                    $comparing = getfullname($COMCID);
                }

                $disp.='<a name="list"></a><P><h4>' . $comparing . '</h4></P>';

                if ($COMCID != "ALL") {
                    $sql = 'select sort from ' . $SysValue['base']['table_name'] . ' where id=' . intval($COMCID);
                    $result = mysqli_query($link_db, $sql);
                    @$row = mysqli_fetch_array(@$result);
                    $sorts = unserialize($row['sort']);
                } else {
                    foreach ($cats as $catid => $name) {
                        $sql = 'select sort from ' . $SysValue['base']['table_name'] . ' where id=' . intval($catid);
                        $result = mysqli_query($link_db, $sql);
                        @$row = mysqli_fetch_array(@$result);
                        $tempsorts = unserialize($row['sort']);
                        if (is_array($tempsorts))
                            foreach ($tempsorts as $curtempsort) {
                                $sorts[] = $curtempsort;
                            }
                    }
                }
                if (is_array($sorts))
                    $sorts = array_unique($sorts); //Оставляем только уникальные сортировки

                $sorts_name = array();

                if (is_array($sorts))
                    foreach ($sorts as $sort) {
                        $sql = 'select name from ' . $SysValue['base']['sort_categories'] . ' where id=' . intval($sort);
                        $result = mysqli_query($link_db, $sql);
                        @$row = mysqli_fetch_array(@$result);
                        $sorts_name[$sort] = $row['name'];
                    }

                /*
                 * Товары могут быть из разных категорий, в которых одинаковые характеристики могут иметь разное название
                 * Поэтому реверсируем массив. Получаем массив Имя_характеристики = массив идентификаторов
                 */

                if (is_array($sorts_name))
                    foreach ($sorts_name as $sort => $name) {
                        $sql = 'select id from ' . $SysValue['base']['sort_categories'] . ' where name LIKE \'' . $name . '\'';
                        $result = mysqli_query($link_db, $sql);
                        while ($row = mysqli_fetch_array(@$result)) {
                            $sorts_name2[$name][$row['id']] = 1;
                        }
                    }

                if (empty($sorts_name2))
                    $sorts_name2 = 0;

                // Подготовка Матрицы для будущей таблицы
                $tdR[0][] = __('Товар');
                $tdR[0][] = __('Фото');
                $tdR[0][] = __('Цена');
                if (is_array($sorts_name2))
                    foreach ($sorts_name2 as $name => $id) {
                        $tdR[0][] = $name;
                    }
                $tdR[0][] = __('Описание');
                $igood = 0;

                if ($COMCID != "ALL") {
                    $goodstowork = $goods[$COMCID];
                } else {
                    foreach ($cats as $catid => $name) {
                        foreach ($goods[$catid] as $curtempgood) {
                            $goodstowork[] = $curtempgood;
                        }
                    }
                }

                foreach ($goodstowork as $id => $val) {
                    $igood++;

                    $tdR[$igood][] = '<A href="' . $val['url'] . '.html" title="' . $val['name'] . '">' . $val['name'] . '</A>';


                    //Выбираем товар из базы
                    $sql = 'select id,price,pic_small,vendor_array,content,baseinputvaluta from ' . $SysValue['base']['products'] . ' where id=' . intval($val['id']);
                    $result = mysqli_query($link_db, $sql);
                    @$row = mysqli_fetch_array(@$result);
                    if (trim($row['pic_small'])) {
                        $tdR[$igood][] = '<img class="media-object" src="' . $row['pic_small'] . '">';
                    } else {
                        $tdR[$igood][] = 'Изображение отсутствует';
                    }

                    $id = $row['id'];

                    $admoption = unserialize($LoadItems['System']['admoption']);

                    // Если цены показывать только после аторизации
                    if ($admoption['user_price_activate'] == 1 and !$_SESSION['UsersId']) {
                        $price = "-";
                    }
                    else
                        $price = PHPShopProductFunction::GetPriceValuta($row['id'], array($row['price'], $row['price2'], $row['price3'], $row['price4'], $row['price5']), $row['baseinputvaluta']);

                    $tdR[$igood][] = $price;
                    $chars = unserialize($row['vendor_array']);

                    if (is_array($chars))
                        foreach ($chars as $k => $char) {
                            $chars[$k] = array_unique($char);
                        }

                    if (is_array($sorts_name2))
                        foreach ($sorts_name2 as $name => $ids) {
                            $curchar = '';
                            foreach ($ids as $id => $true) {
                                @$ca = $chars[$id];
                                if (is_array($ca))
                                    foreach ($ca as $charid) {
                                        $sql2 = 'select name from ' . $SysValue['base']['sort'] . ' where id=' . intval($charid);
                                        $result2 = mysqli_query($link_db, $sql2);
                                        @$row2 = mysqli_fetch_array(@$result2);
                                        $curchar.=' ' . $row2['name'] . '<br>';
                                    }
                            }
                            $tdR[$igood][] = $curchar;
                        }
                    $tdR[$igood][] = stripslashes($row['content']);
                }

                //троим таблицу по матрице
                $rows = count($tdR[0]);
                $cols = count($goodstowork) + 1;
                $disp.='<TABLE class="table table-striped table-responsive">';

                for ($row = 0; $row < $rows; $row++) {
                    $disp.='<tr>';
                    for ($col = 0; $col < $cols; $col++) {
                        $value = trim($tdR[$col][$row]);
                        if (!$value) {
                            $value = '&nbsp;';
                        }

                        $disp.='<td>' . $value . '</td>';
                    }
                    $disp.='</tr>';
                }
                $disp.='</TABLE>';
            }

        //Если нет товаров, показать пусто. ДОЛЖНО БЫТЬ ПОСЛЕДНЕЙ СТРОКОЙ
        if (is_array($cats)  and count($cats) == 0) {
            $disp = '<P><h5>' . __('Вы не выбрали товары для сравнения') . '!</h5></P>';
        }

        // Определяем переменые
        $SysValue['other']['pageTitle'] = $SysValue['other']['pageTitl'] = $SysValue['other']['catalogCat'] = __("Сравнение товаров");
        $SysValue['other']['pageContent'] = '<div class="compare_list">' . $disp . '</div>';
        $SysValue['other']['catalogCategory'] = __("Выбраны товары для сравнения");

        // Мета
        $this->description = $SysValue['other']['pageTitle'];
        $this->title = $SysValue['other']['pageTitle'] . ' - ' . $this->PHPShopSystem->getValue("name");


        // Определяем переменые
        $this->set('pageContent', $disp);
        $this->set('pageTitle', $SysValue['other']['pageTitle']);


        // Подключаем шаблон
        if (PHPShopParser::checkFile("users/compare/compare_page_list.tpl"))
            $this->parseTemplate('users/compare/compare_page_list.tpl');
        else
            $this->parseTemplate($this->getValue('templates.page_page_list'));
    }
}

?>