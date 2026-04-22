<?php

/**
 * Фото-галерея
 * @package PHPShopAjaxElements
 */
$_classPath = "../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("system");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
$PHPShopSystem = new PHPShopSystem();

function getFotoIconPodrobno($n, $f) {
    global $SysValue, $FotoArray, $link_db,$PHPShopSystem;

    $fRComSatrt = null;
    $sql = "select * from " . $SysValue['base']['foto'] . " where parent='" . intval($n) . "' order by num";
    $result = mysqli_query($link_db, $sql);
    $num = mysqli_num_rows($result);
    while (@$row = mysqli_fetch_array(@$result)) {
        $name = $row['name'];
        $name_s = str_replace(".", "s.", $name);
        $name_b = str_replace(".", "_big.", $name);

        // Подбор исходного изображения
        if (!$PHPShopSystem->ifSerilizeParam('admoption.image_save_source') or ! file_exists($_SERVER['DOCUMENT_ROOT'] . $name_b))
            $name_b = $name;

        $id = $row['id'];
        $info = $row['info'];

        $FotoArray[] = array(
            "id" => $id,
            "name" => $name,
            "name_s" => $name_s,
            "name_b" => $name_b,
            "info" => $info
        );
    }

    $dBig = '<div align="center" id="IMGloader" style="padding-bottom: 10px">
<a class=highslide onclick="return hs.expand(this)" href="' . $FotoArray[$f]["name_b"] . '" target=_blank getParams="null">
 <img id="currentBigPic" src="' . $FotoArray[$f]["name"] . '" border="1" class="imgOn" alt="' . $info . '"  align="middle"></a><br>' . $FotoArray[$f]["info"] . '
</div>';

    foreach ($FotoArray as $k => $v) {

        $fL = $f - 1;
        $fR = $f + 1;

        if ($f == 0) {
            $fL = 0;
            $f = 1;
            $fR = 2;
        }

        if ($fR == $num) {

            $fRComSatrt = "<!-- ";
            $fRComEnd = " -->";
        }

        $disp = '<tr>
            <td><a href="javascript:fotoload(' . $n . ',' . $fL . ');" ><img src="../phpshop/lib/templates/icon/prev.png" border="0"></a></td>
  <td align="center">
  <a href="javascript:fotoload(' . $n . ',' . $fL . ');" title=' . $FotoArray[$fL]["info"] . '><img src="' . $FotoArray[$fL]["name_s"] . '" alt="' . $FotoArray[$fL]["info"] . '" border="1" class="imgOff" onmouseover="ButOn(this)" onmouseout="ButOff(this)"></a>
  </td>
  <td align="center">
    <a href="javascript:fotoload(' . $n . ',' . $f . ');" title="' . $FotoArray[$f]["info"] . '"><img src="' . $FotoArray[$f]["name_s"] . '" alt="' . $FotoArray[$f]["info"] . '" border="1" class="imgOn"></a>
  </td>
  <td align="center">' . $fRComSatrt . '
    <a href="javascript:fotoload(' . $n . ',' . $fR . ');" title="' . $FotoArray[$fR]["info"] . '"><img src="' . $FotoArray[$fR]["name_s"] . '" alt="' . $FotoArray[$fR]["info"] . '" border="1" class="imgOff" onmouseover="ButOn(this)" onmouseout="ButOff(this)">
</a>' . $fRComEnd . '
  </td>
  <td>' . $fRComSatrt . '
  <a href="javascript:fotoload(' . $n . ',' . $fR . ');" ><img src="../phpshop/lib/templates/icon/next.png" border="0"></a>
      ' . $fRComEnd . '
</td>
</tr>';
    }

    $d = $dBig;
    if ($num > 1)
        $d .= '<table class="foto">' . $disp . '</table>';
    return $d;
}

if (PHPShopSecurity::true_num($_REQUEST['xid'])) {

    $_RESULT = array(
        'foto' => PHPShopString::win_utf8(getFotoIconPodrobno($_REQUEST['xid'], $_REQUEST['fid'])),
        'current' => $FotoArray[$_REQUEST['fid']]["name"],
        "success" => 1
    );

    echo json_encode($_RESULT);
}
?>