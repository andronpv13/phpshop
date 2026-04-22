<?php

function dolyame_footer_hook() {

    require_once "./phpshop/modules/dolyame/class/Dolyame.php";
    $Dolyame = new Dolyame();

    if (!empty($Dolyame->site_id)) {

        $dis = "
    <!-- Dolyame -->     
    <script type='text/javascript'>
    var digiScript = document.createElement ('script');
    digiScript.src = '//aq.dolyame.ru/" . $Dolyame->site_id . "/client.js?ts=' + Date.now();
    digiScript.defer = true;
    digiScript.async = true;
    document.body.appendChild (digiScript);
    </script>

    ";
        echo $dis;
    }
    else echo '<script src="/phpshop/modules/dolyame/templates/dolyame.js"></script>';
}

$addHandler = array('footer' => 'dolyame_footer_hook');
?>