<?php

class PHPShopFormgeneratorElement {

    function __construct() {
        $this->objBase = $GLOBALS['SysValue']['base']['formgenerator']['formgenerator_forms'];
    }

    function fixtags($str) {
        return str_replace(array('<//'), array('</'), $str);
    }

    function forma($path) {
        $error = null;

        $path = PHPShopSecurity::TotalClean($path, 4);
        $PHPShopOrm = new PHPShopOrm($this->objBase);
        $data = $PHPShopOrm->select(array('*'), array('path' => "='" . $path . "'", 'enabled' => "='1'"), false, array('limit' => 1));

        // Сообщение обязательных полей
        if (!empty($_GET['error']))
            $error = $data['error_message'];

        if (is_array($data)) {
            $forma_content = '<p>' . $error . '</p><h3 class="formgenerator-title">' . $data['name'] . '</h3><form method="post" enctype="multipart/form-data" name="formgenerator" id="formgenerator" action="/formgenerator/' . $path . '/">
            ' . Parser($this->fixtags($data['content']));

            // Защитная каптча
            if (!empty($data['captcha']))
                $forma_content.='<div class="form-group">' . Parser('@captcha@') . '</div>';

            $forma_content.='<p id="formgenerator_buttons">
            <input type="hidden" name="forma_id" value="' . $data['id'] . '">
            <input class="btn btn-default" type="reset" value="Очистить">
            <input class="btn btn-primary" type="submit" name="forma_send" value="Отправить">
             </p>
</form>';
            return $forma_content;
        }
        else
            return 'Форма не найдена в базе';
    }

}

?>