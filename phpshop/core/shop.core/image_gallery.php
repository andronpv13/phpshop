<?php

/**
 * Вывод изображений для подробного описания
 * @author PHPShop Software
 * @version 1.4
 * @package PHPShopCoreFunction
 * @param obj $obj объект класса
 * @param row $row масив данных
 */
function image_gallery($obj, $row) {

    if (!empty($row['pic_big'])) {
        $n = $row['id'];
        $pic_big = $row['pic_big'];
        $name_foto = $row['name'];
        $disp = null;
        $PHPShopOrm = new PHPShopOrm($obj->getValue('base.foto'));
        $data = $PHPShopOrm->select(array('*'), array('parent' => '=' . $row['id']), array('order' => 'num'), array('limit' => 100));
        if (is_array($data)) {
            foreach ($data as $row) {

                $name = $row['name'];
                $name_s = str_replace(".", "s.", $name);
                $name_bigstr = str_replace(".", "_big.", $pic_big);
                $name_big = $_SERVER['DOCUMENT_ROOT'] . $name_bigstr;

                // Подбор исходного изображения
                if (file_exists($name_big))
                    $name_b =  $name_bigstr;
                else
                    $name_b = $pic_big;

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


            if (is_array($FotoArray)) {
                if (!empty($row['info']))
                    $alt = $row['info'];
                else
                    $alt = $name_foto;
                $dBig = '<div id="IMGloader" style="text-align:center;padding-bottom: 10px">
<a class=highslide onclick="return hs.expand(this)" href="' . $name_b. '" target=_blank><img id="currentBigPic" src="' . $pic_big . '"  class="imgOn" alt="' . $name_foto . '" title="'.$name_foto.'"
    onerror="NoFoto2(this)" itemprop="image"></a><div class="highslide-caption">' . $name_foto . '</div><br>' . $FotoArray[0]["info"] . '
</div>';
            }
            if (is_array($FotoArray[0]) and count($FotoArray) > 1)
                $disp.='<td  style="text-align:center">
  <a href="javascript:fotoload(' . $n . ',0);"><img src="' . $FotoArray[0]["name_s"] . '" alt="' . $FotoArray[0]["info"] . '" class="imgOn" onerror="NoFoto2(this)"></a></td>';

            if (is_array($FotoArray[1]))
                $disp.='<td style="text-align:center">
    <a href="javascript:fotoload(' . $n . ',1);"><img src="' . $FotoArray[1]["name_s"] . '" alt="' . $FotoArray[1]["info"] . '" class="imgOff" onmouseover="ButOn(this)" onmouseout="ButOff(this)" onerror="NoFoto2(this)"></a></td>';

            if (is_array($FotoArray[2]))
                $disp.='<td style="text-align:center">
     <a href="javascript:fotoload(' . $n . ',2);"><img src="' . $FotoArray[2]["name_s"] . '" alt="' . $FotoArray[2]["info"] . '" class="imgOff" onmouseover="ButOn(this)" onmouseout="ButOff(this)" onerror="NoFoto2(this)"></td><td>
<a href="javascript:fotoload(' . $n . ',2);" title="' . __('Далее') . '"><img src="phpshop/lib/templates/icon/next.png" alt="' . __('Далее') . '" border="0"></a></td>';

            $d = $dBig;
            if (count($data) > 1)
                $d.='<table class="foto">
<tr>
' . $disp . '</tr>
</table>
<div>' . __('Доступно изображений') . ': <strong>' . count($data) . '</strong> </div>
';
        }
        else {

            // Подбор исходного изображения
            $name_bigstr = str_replace(".", "_big.", $pic_big);
            $name_big = $_SERVER['DOCUMENT_ROOT'] . $name_bigstr;
            if (file_exists($name_big))
                $name_b = $name_bigstr;
            else
                $name_b = $pic_big;

            $d = '<div id="IMGloader" style="text-align:center;padding-bottom: 10px">
<a class=highslide onclick="return hs.expand(this)" href="' . $name_b. '" target=_blank><img id="currentBigPic" src="' . $pic_big. '" class="imgOn" class="imgOn" alt="' . $name_foto . '" title="'.$name_foto.'" 
    itemprop="image"></a><div class="highslide-caption">' . $name_foto . '</div>
</div>';
}

        // Результат
        $obj->set('productFotoList', $d);
    }
}

?>