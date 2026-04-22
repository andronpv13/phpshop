<?php

/**
 * Комментарии
 * @package PHPShopAjaxElements
 */
session_start();
$_classPath = "../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
PHPShopObj::loadClass("date");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("user");
PHPShopObj::loadClass("parser");
PHPShopObj::loadClass("mail");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("lang");
PHPShopObj::loadClass("product");

$PHPShopSystem = new PHPShopSystem();
$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'shop'));
$_REQUEST['message'] = PHPShopString::utf8_win1251($_REQUEST['message']);

/**
 * Создание запроса БД на вывод комментариев
 * @package PHPShopAjaxElementsDepricated
 * @param int $id ИД категории комментариев
 * @return string
 */
function Page_comment($id) {
    global $SysValue;

    $p = intval($_REQUEST['page']);
    if (empty($p))
        $p = 1;
    $num_row = 10;
    $num_ot = 0;
    $q = 0;
    while ($q < $p) {
        $sql = "select * from " . $SysValue['base']['comment'] . " where parent_id=" . intval($id) . "  and enabled='1'  order by id desc LIMIT $num_ot, $num_row";
        $q++;
        $num_ot = $num_ot + $num_row;
    }
    return $sql;
}

/**
 * Навигация по элементу комментарии
 * @package PHPShopAjaxElementsDepricated
 * @param int $id ИД категории комментариев
 * @return string
 */
function Nav_comment($id) {
    global $SysValue, $link_db;

    $navigat = null;
    $p = $_REQUEST['page'];
    if (empty($p))
        $p = 1;

    // Ко-во позиций на странице
    $num_row = 10;
    $sql = "select id from " . $SysValue['base']['comment'] . " where parent_id=" . intval($id) . " and enabled='1'";
    @$result = mysqli_query($link_db, $sql);
    $num_page = mysqli_num_rows(@$result);
    $i = 1;
    $num = $num_page / $num_row;
    while ($i < $num + 1) {
        if ($i != $p) {

            if ($i == 1)
                $pageOt = $i + @$pageDo;
            else
                $pageOt = $i + @$pageDo - $i;

            $pageDo = $i * $num_row;
            $navigat .= '<li class=""><a href="javascript:commentList(' . $id . ',\'list\',' . $i . ');">' . $i . '</a></li>';
        }
        else {

            if ($i == 1)
                $pageOt = $i + @$pageDo;
            else
                $pageOt = $i + @$pageDo - $i;

            $pageDo = $i * $num_row;
            $navigat .= '<li class="active"><a>' . $i . '</a></li>';
        }
        $i++;
    }
    if ($num > 1) {
        if ($p > $num) {
            $p_to = $i - 1;
        } else {
            $p_to = $p + 1;
        }
        $nava = '<nav>
  <ul class="pagination">
    <li class=""><a href="javascript:commentList(' . $id . ',\'list\',' . ($p - 1) . ');" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>
    ' . $navigat . '
    <li class=""><a href="javascript:commentList(' . $id . ',\'list\',' . $p_to . ');" aria-label="Previous"><span aria-hidden="true">&raquo;</span></a></li>
  </ul>
</nav>';
    }
    return $nava;
}

/**
 * Форматирование смайликов в комментариях
 * @package PHPShopAjaxElementsDepricated
 * @param string $string текст
 * @return string
 */
function returnSmile($string) {

    $Smile = array(
        ':-D' => '<img src="images/smiley/grin.gif" alt="Смеется" border="0">',
        ':\)' => '<img src="images/smiley/smile3.gif" alt="Улыбается" border="0">',
        ':\(' => '<img src="images/smiley/sad.gif" alt="Грустный" border="0">',
        ':shock:' => '<img src="images/smiley/shok.gif" alt="В шоке" border="0">',
        ':cool:' => '<img src="images/smiley/cool.gif" alt="Самоуверенный" border="0">',
        ':blush:' => '<img src="images/smiley/blush2.gif" alt="Стесняется" border="0">',
        ':dance:' => '<img src="images/smiley/dance.gif" alt="Танцует" border="0">',
        ':rad:' => '<img src="images/smiley/happy.gif" alt="Счастлив" border="0">',
        ':lol:' => '<img src="images/smiley/lol.gif" alt="Под столом" border="0">',
        ':huh:' => '<img src="images/smiley/huh.gif" alt="В замешательстве" border="0">',
        ':rolly:' => '<img src="images/smiley/rolleyes.gif" alt="Загадочный" border="0">',
        ':thuf:' => '<img src="images/smiley/threaten.gif" alt="Злой" border="0">',
        ':tongue:' => '<img src="images/smiley/tongue.gif" alt="Показывает язык" border="0">',
        ':smart:' => '<img src="images/smiley/umnik2.gif" alt="Умничает" border="0">',
        ':wacko:' => '<img src="images/smiley/wacko.gif" alt="Запутался" border="0">',
        ':yes:' => '<img src="images/smiley/yes.gif" alt="Соглашается" border="0">',
        ':yahoo:' => '<img src="images/smiley/yu.gif" alt="Радостный" border="0">',
        ':sorry:' => '<img src="images/smiley/sorry.gif" alt="Сожалеет" border="0">',
        ':nono:' => '<img src="images/smiley/nono.gif" alt="Нет Нет" border="0">',
        ':dash:' => '<img src="images/smiley/dash.gif" alt="Бьется об стенку" border="0">',
        ':dry:' => '<img src="images/smiley/dry.gif" alt="Скептический" border="0">',
    );

    foreach ($Smile as $key => $val)
        $string = str_replace($key, $val, $string);

    return $string;
}

/**
 * Вывод комментариев
 * @package PHPShopAjaxElementsDepricated
 * @param int $id ИД категории комментариев
 * @return string
 */
function DispComment($id) {
    global $SysValue, $link_db;

    $dis = null;
    $sql = Page_comment($id);
    $result = mysqli_query($link_db, $sql);
    while ($row = mysqli_fetch_array($result)) {
        $user_id = $row['user_id'];

        // Редактирование
        if ($_SESSION['UsersId'] == $user_id)
            $SysValue['other']['commentEdit'] = '<a href="#addComment" onclick="javascript:commentList(' . $user_id . ',\'edit\',1,' . $row['id'] . ');">Править</a>';
        else
            $SysValue['other']['commentEdit'] = "";

        // Определяем переменые
        $SysValue['other']['commentData'] = PHPShopDate::dataV($row['datas'], false);
        $SysValue['other']['commentName'] = $row['name'];
        $SysValue['other']['commentStarCount'] = $row['rate'];
        $SysValue['other']['commentContent'] = returnSmile($row['content']);
        $SysValue['other']['avgRateWidth'] = avg_rate($row['rate']);

        // Подключаем шаблон
        if (is_file('../../' . $SysValue['dir']['templates'] . chr(47) . $_SESSION['skin'] . "/comment/main_comment_forma.tpl"))
            $dis .= PHPShopParser::file('../../' . $SysValue['dir']['templates'] . chr(47) . $_SESSION['skin'] . "/comment/main_comment_forma.tpl", true);
    }

    // Определяем переменные
    $SysValue['other']['producUid'] = $SysValue['nav']['id'];
    $SysValue['other']['UsersId'] = $_SESSION['UsersId'];
    $SysValue['other']['productPageThis'] = $p;
    $SysValue['other']['productPageNav'] = Nav_comment($id);
    $SysValue['other']['productPageDis'] = str_replace("#imagesSavePathLabel#", "images", $dis);

    // Подключаем шаблон
    $disp = PHPShopParser::file('../../' . $SysValue['dir']['templates'] . chr(47) . $_SESSION['skin'] . "/comment/comment_page_list.tpl", true, false);
    return $disp;
}

function avg_rate($rate) {

    $oneStarWidth = 20; // ширина одной звёздочки
    $oneSpaceWidth = 0; // пробел между звёздочками
    // берём параметры с конфига, если заданы
    if (@$_SESSION['Memory']["rateForComment"]["oneStarWidth"])
        $oneStarWidth = $_SESSION['Memory']["rateForComment"]["oneStarWidth"];
    if (@$_SESSION['Memory']["rateForComment"]["oneSpaceWidth"])
        $oneSpaceWidth = $_SESSION['Memory']["rateForComment"]["oneSpaceWidth"];


    return $oneStarWidth * $rate + $oneSpaceWidth * ceil($rate);
}

// Действия
switch ($_REQUEST['comand']) {

    case("add"):
        $message = strip_tags($_REQUEST['message']);
        $message = PHPShopSecurity::TotalClean($message, 2);
        $myRate = abs(intval($_REQUEST['rateVal']));
        $xid = intval($_REQUEST['xid']);
        if (!$myRate)
            $myRate = 0;
        elseif ($myRate > 5)
            $myRate = 5;
        if (!empty($_SESSION['UsersId']) and ! empty($message)) {

            $PHPShopUser = new PHPShopUser($_SESSION['UsersId']);
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['comment']);
            $PHPShopOrm->insert(array('datas_new' => time(), 'name_new' => $PHPShopUser->getName(), 'parent_id_new' => $xid, 'content_new' => $message, 'user_id_new' => intval($_SESSION['UsersId']), 'enabled_new' => 0, 'rate_new' => $myRate));


            // Имя товара
            $product = new PHPShopProduct((int) $xid);
            $name = $product->getName();

            // Письмо администратору
            PHPShopParser::set('mail', $PHPShopUser->getLogin());
            PHPShopParser::set('content', $message);
            PHPShopParser::set('name', $PHPShopUser->getName());
            PHPShopParser::set('product', $name);
            PHPShopParser::set('product_id', $product->objID);
            PHPShopParser::set('rating', $myRate);
            PHPShopParser::set('date', PHPShopDate::dataV(false, false));


            // Подключаем шаблон
            $message = PHPShopParser::file("../lib/templates/comment/mail.tpl", true);

            $system = new PHPShopSystem();
            $title = __("Добавлен отзыв к товару") . ' "' . $name . '"';

            (new PHPShopMail($system->getValue('adminmail2'), $system->getValue('adminmail2'), $title, '', true, true, ['replyto' => $email]))->sendMailNow(PHPShopParser::file('../lib/templates/users/mail_admin_review.tpl', true, false));


            $error = "done";
            //writeLangFile();
        } else
            $error = "error";
        $interfaces = DispComment($_REQUEST['xid']);
        break;

    case("list"):
        $interfaces = DispComment($_REQUEST['xid']);
        break;

    case("edit"):
        $sql = "select content from " . $SysValue['base']['table_name36'] . " where id=" . intval($_REQUEST['cid']) . " and user_id=" . $_SESSION['UsersId'];
        $result = mysqli_query($link_db, $sql);
        $row = mysqli_fetch_array($result);
        $interfaces = $row['content'];
        break;

    case("edit_add"):
        $myMessage = strip_tags($_REQUEST['message']);
        $myMessage = PHPShopSecurity::TotalClean($myMessage, 2);
        if ($_SESSION['UsersId'] > 0 and ! empty($myMessage)) {
            $sql = "UPDATE " . $SysValue['base']['table_name36'] . "
            SET
            datas='" . date("U") . "',
            enabled='0',
            content='" . $myMessage . "' 
            where id='" . intval($_REQUEST['cid']) . "'";
            mysqli_query($link_db, $sql);

            // пересчитываем рейтинг для товара.
            $sql = "SELECT parent_id FROM " . $SysValue['base']['table_name36'] . " where id='" . intval($_REQUEST['cid']) . "'";
            $result = mysqli_query($link_db, $sql);
            $row = mysqli_fetch_array($result);
            $parent_id = $row['parent_id'];

            $result = mysqli_query($link_db, "select avg(rate) as rate, count(id) as num from " . $SysValue['base']['table_name36'] . " WHERE parent_id=$parent_id AND enabled='1' AND rate>0 group by parent_id LIMIT 1");
            if (mysqli_num_rows($result)) {
                $row = mysqli_fetch_array($result);
                extract($row);
                $rate = round($rate, 1);
                mysqli_query($link_db, "UPDATE  " . $SysValue['base']['products'] . " SET rate = '$rate', rate_count='$num' WHERE id=$parent_id");
            } else {
                mysqli_query($link_db, "UPDATE  " . $SysValue['base']['products'] . " SET rate = '0', rate_count='0' WHERE id=$parent_id");
            }
        } else
            $error = "error";
        $interfaces = DispComment($_REQUEST['xid']);
        break;

    case("dell"):
        $sql = "delete from " . $SysValue['base']['table_name36'] . "
where id='" . intval($_REQUEST['cid']) . "'";
        mysqli_query($link_db, $sql);
        $interfaces = DispComment($_REQUEST['xid']);
        break;
}


$_RESULT = array(
    'comment' => $interfaces,
    'status' => $error,
    'success' => 1
);

// JSON 
$_RESULT['comment'] = PHPShopString::win_utf8($interfaces);
echo json_encode($_RESULT);
?>