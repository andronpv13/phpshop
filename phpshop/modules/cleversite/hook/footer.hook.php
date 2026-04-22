<?php
function cleversite_footer_hook() {
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['cleversite']['cleversite_system']);
    $option = $PHPShopOrm->select();
    $dis="
      <!-- Cleversite chat button -->
      <script type='text/javascript'>
      (function() {
      var s = document.createElement('script');
      s.type = 'text/javascript';
      s.async = true;
      s.charset = 'utf-8';
      s.src = '//cleversite.ru/cleversite/widget_new.php?supercode=1&referer_main='+encodeURIComponent(document.referrer)+'"
        ."&clid=".$option['client']."sHGQD"
        ."&siteNew=".$option['site']."';
      var ss = document.getElementsByTagName('script')[0];
      ss.parentNode.insertBefore(s, ss);
      })();
      </script>
      <!-- / End of Cleversite chat button -->";
    echo $dis;
}

$addHandler = array(
    'footer' => 'cleversite_footer_hook'
);
?>