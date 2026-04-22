<?php

/*
 * Подвал
 */

// Перехват модуля
if ($PHPShopNav->notPath('print')) {
    $PHPShopModules->setHookHandler('footer', 'footer');
}

// PopUp
$PHPShopBannerElement->getPopup();

// Аналитика
$PHPShopAnalitica->counter();

echo '
    </div>
  </body>
</html>';
?>