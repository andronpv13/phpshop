<?php

/**
 * Автозагрузка элементов
 * @package PHPShopInc
 */
// Защищаем от дублей /index.php/index.php
if (strstr($_SERVER['REQUEST_URI'], 'index.php')) {
    header('Location: /error/');
    exit();
}

// Шаблон дизайна по умолчанию
$PHPShopCoreElement = new PHPShopCoreElement();
$PHPShopCoreElement->init('service');
$PHPShopCoreElement->init('skin', false);
$PHPShopCoreElement->init('checkskin');
$PHPShopCoreElement->init('setdefault');

// Промоакции
$PHPShopPromotions = new PHPShopPromotions();

// Выбор шаблона
$PHPShopSkinElement = new PHPShopSkinElement();
$PHPShopSkinElement->init('skinSelect');

// Стили шаблона дизайна
$PHPShopCoreElement->init('pageCss', false);

// Загрузка модулей
PHPShopObj::loadClass('modules');
$PHPShopModules = new PHPShopModules();
$PHPShopModules->doLoad();

// Подключаем файлы autoload
foreach ($GLOBALS['SysValue']['autoload'] as $val)
    if (is_file($val))
        include_once($val);

// Мобильные устройства
$mobil = new Mobile_Detect();
if ($mobil->isMobile() or $mobil->isTablet()) {
    define("isMobil", true);
    
    $isIOS = intval($mobil->version('iPad', $mobil::VERSION_TYPE_FLOAT) . $mobil->version('iPhone', $mobil::VERSION_TYPE_FLOAT));
    if (!empty($isIOS) and $isIOS < 14)
        define("isIOS", $isIOS);
}

// JS настройки
$PHPShopCoreElement->init('setjs');

// Выбор валюты
$PHPShopCurrencyElement = new PHPShopCurrencyElement();
$PHPShopCurrencyElement->init('valutaDisp');

// Корзина
$PHPShopCartElement = new PHPShopCartElement();
$PHPShopCartElement->init('miniCart');

// Новинки в центр
$PHPShopProductIndexElements = new PHPShopProductIndexElements();
$PHPShopProductIndexElements->init('specMain');

// Последние покупки
$PHPShopProductIndexElements->init('nowBuy');

// Меню каталогов
$PHPShopShopCatalogElement = new PHPShopShopCatalogElement();
$PHPShopShopCatalogElement->init('leftCatal');
$PHPShopShopCatalogElement->init('leftCatalTable');

// Новинки в колонку
$PHPShopProductIconElements = new PHPShopProductIconElements();
$PHPShopProductIconElements->init('specMainIcon');

// Меню каталогов страниц
$PHPShopPageCatalogElement = new PHPShopPageCatalogElement();
if ($PHPShopNav->notPath(array('order', 'done')))
    $PHPShopPageCatalogElement->init('pageCatal');
$PHPShopPageCatalogElement->init('getLastPages');

// Мини-новости
$PHPShopNewsElement = new PHPShopNewsElement();
$PHPShopNewsElement->init('miniNews');

// Мини-отзывы
$PHPShopGbookElement = new PHPShopGbookElement();
$PHPShopGbookElement->init('miniGbook');

// Слайдер
$PHPShopSliderElement = new PHPShopSliderElement();
$PHPShopSliderElement->init('imageSlider');

// Баннеры
$PHPShopBannerElement = new PHPShopBannerElement();
$PHPShopBannerElement->init('banersDisp');
$PHPShopBannerElement->init('banersDispHorizontal');
$PHPShopBannerElement->init('banersDispMenu');

// Аналитика
$PHPShopAnalitica = new PHPShopAnalitica();

// Облако тегов
$PHPShopCloudElement = new PHPShopCloudElement();
$PHPShopCloudElement->init('cloud');

// Текстовый блок
$PHPShopTextElement = new PHPShopTextElement();
$PHPShopTextElement->init('leftMenu', true);
$PHPShopTextElement->init('rightMenu', true);
$PHPShopTextElement->init('topMenu', true);
$PHPShopTextElement->init('bottomMenu', true);
$PHPShopShopCatalogElement->init('topcatMenu', true);
$PHPShopPageCatalogElement->init('topMenu', true);

// Фотогалерея
$PHPShopPhotoElement = new PHPShopPhotoElement();
$PHPShopPhotoElement->init('getPhotos');

// Captcha
$PHPShopRecaptchaElement = new PHPShopRecaptchaElement();
$PHPShopRecaptchaElement->init('captcha');
PHPShopParser::set('pricemail_captcha', $PHPShopRecaptchaElement->captcha('pricemail'));
PHPShopParser::set('notice_captcha', $PHPShopRecaptchaElement->captcha('notice'));
PHPShopParser::set('review_captcha', $PHPShopRecaptchaElement->captcha('review'));
PHPShopParser::set('forma_captcha', $PHPShopRecaptchaElement->captcha('forma'));

// Диалоги
$PHPShopDialogElement = new PHPShopDialogElement();
$PHPShopDialogElement->dialog();

// Авторизация пользователей
$PHPShopUserElement = new PHPShopUserElement();
$PHPShopUserElement->init('wishlist');
$PHPShopUserElement->init('usersDisp');

// Меню витрин
$PHPShopShowcaseElement = new PHPShopShowcaseElement();
$PHPShopShowcaseElement->init('showcaseMenu');

// RSS грабер новостей
new PHPShopRssParser();