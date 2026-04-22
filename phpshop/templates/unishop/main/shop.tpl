<!DOCTYPE html>
<html lang="@lang@">
    <head>
        <meta charset="@charset@">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@pageTitl@</title>
        <meta name="description" content="@pageDesc@">
        <meta name="keywords" content="@pageKeyw@">
        <meta name="copyright" content="@pageReg@">
        <link rel="apple-touch-icon" href="@icon@">
        <link rel="icon" href="@icon@" type="image/x-icon">
        <link rel="mask-icon" href="@icon@" >
        <link rel="icon" href="@icon@" type="image/x-icon">
        <link rel="mask-icon" href="@icon@" >

        <!-- OpenGraph -->
        <meta property="og:title" content="@ogTitle@">
        <meta property="og:image" content="http://@serverName@@ogImage@">
        <meta property="og:url" content="http://@ogUrl@">
        <meta property="og:type" content="website">
        <meta property="og:description" content="@ogDescription@">

        <!-- Preload -->
        <link rel="preload" href="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin']; php@css/bootstrap.min.css" as="style">
        <link rel="preload" href="@pageCss@" as="style">
        <link rel="preload" href="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin']; php@css/@unishop_theme@.css" as="style">
        <link rel="preload" href="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin']; php@css/swiper.min.css" as="style">

        <link rel="preload" href="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin']; php@css/font-awesome.min.css"  as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin']; php@css/iconfont.css"  as="font" type="font/woff2" crossorigin>

        <!-- Bootstrap -->
        <link href="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin']; php@css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body id="body" data-dir="@ShopDir@" data-path="@php echo $GLOBALS['PHPShopNav']->objNav['path']; php@" data-id="@php echo $GLOBALS['PHPShopNav']->objNav['id']; php@" data-subpath="@php echo $GLOBALS['PHPShopNav']->objNav['name']; php@" data-token="@dadataToken@">

        <!-- Template -->
        <link href="@pageCss@" rel="stylesheet">

        <!-- Theme -->
        <link id="bootstrap_theme" data-name="@php echo $_SESSION['skin']; php@" href="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin']; php@css/@unishop_theme@.css" rel="stylesheet">

        <!-- Стикер-полоска -->
        <div class="@php __hide('sticker_top'); php@">
            <div class="top-banner @php __hide('sticker_close','cookie'); php@">
                <div class="sticker-text">@sticker_top@</div>
                <span class="close sticker-close">x</span>
            </div>
        </div>
        <!-- /Стикер-полоска -->

        <div class="navbar-offcanvas" id="js-bootstrap-offcanvas">
            <ul class="offcanvas-list">
                <li class="dropdown @hideSite@">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{Каталог} <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        @menuCatal@
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        <i class="feather iconz-user"></i> {Кабинет }<span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li class="@hideCatalog@"><a href="/users/order.html">{Отследить заказ}</a></li>
                        <li class="@hideCatalog@"><a href="/users/notice.html">{Уведомления о товарах}</a></li>
                        <li><a href="/users/message.html">{Связь с менеджерами}</a></li>
                        @php if($_SESSION['UsersId']) echo '<li><a href="?logout=true">{Выйти}</a></li>'; else echo '<li><a href="#" data-toggle="modal" data-target="#userModal">{Войти}</a></li>'; php@
                    </ul>
                </li>
                @topMenu@
                <li class=""><a href="/news/">{Новости}</a></li>
                <li class=""><a href="/gbook/">{Отзывы}</a></li>
                <li class="@hideCatalog@"><a href="/price/">{Прайс-лист}</a></li>
                <li class="@hideSite@"><a href="/map/">{Карта сайта}</a></li>
            </ul>
        </div>

        <!-- Header Section Starts -->
        <header>
            <div class="header-top">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-6 top-mobile-fix">
                            <a class="header-link-color header-top-link header-link-contact" href="mailto:@adminMail@"><i class="fa fa-envelope-o" aria-hidden="true"></i> @adminMail@</a>
                            <a class="header-link-color header-top-link header-link-contact" href="tel:@telNum@"><i class="fa fa-bell-o" aria-hidden="true"></i> @telNum@</a>
                            <a class="header-link-color header-top-link header-link-contact" href="tel:@telNum2@">@telNum2@</a>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-6 top-mobile-fix">
                            <div class="header-wishlist @hideSite@">
                                <a class="header-link-color link" href="/compare/">
                                    <span class="hidden-sm hidden-xs">
                                        <i class="fa fa-bar-chart-o"></i> {Сравнить} (<span id="numcompare">@numcompare@</span>)<span id="wishlist-total" ></span>
                                    </span>
                                </a>
                                @wishlist@
                            </div>
                            <div class="header-top-right">
                                @returncall@
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="header-bottom">
                <div class="container header-container-fix">
                    <div class="row">
                        <div class="col-md-12 ">
                            <form id="search_form" action="/search/" role="search" method="get" class="header-search-form">
                                <input id="search" class="form-control input-lg" name="words" maxlength="50"  placeholder="{Поиск}..." required="" type="search" data-trigger="manual" data-container="body" data-toggle="popover" data-placement="bottom" data-html="true"  data-content="">
                                <button class="" type="submit">
                                    <i class="feather iconz-search"></i>
                                </button>
                                <i class="search-close feather iconz-x"></i>
                            </form>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-2 bottom-mobile-fix">
                            <div id="logo">
                                <a href="/">
                                    <img src="@logo@" alt="" title="@name@" class="img-responsive" />
                                </a>
                            </div>
                            <button type="button" class="hidden-md hidden-lg navbar-toggle feather iconz-menu modile-cat-open offcanvas-toggle" data-toggle="offcanvas" data-target="#js-bootstrap-offcanvas">
                            </button>
                        </div>
                        <div class="col-md-6 col-lg-7 hidden-xs hidden-sm header-menu-wrapper">
                            <div class="row">
                                <ul class="nav navbar-nav main-navbar-top">
                                    <li class="catalog-menu @hideSite@">
                                        <a id="nav-catalog-dropdown-link" class="nav-catalog-dropdown-link" aria-expanded="false">{Каталог}
                                        </a>
                                        <ul class="main-navbar-list-catalog-wrapper">
                                            @leftCatal@
                                        </ul>
                                    </li>
                                    @topBrands@
                                    @topMenu@
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-7 col-xs-12 col-md-4 col-lg-3 hidden-xs hidden-sm bottom-mobile-fix">
                            <div id="cart" class="btn-group header-color @hideCatalog@">
                                <a id="cartlink"  class="btn btn-block btn-lg dropdown-toggle" href="/order/">
                                    <span id="cart-total">
                                        <i class="feather iconz-trash"></i>
                                        <span class="count" id="num">@num@</span>
                                        <span class="sub-total">
                                            <span id="sum"> @sum@</span> <span class="rubznak">@productValutaName@</span>
                                        </span>
                                    </span>
                                </a>
                                @visualcart@
                            </div>
                            <div class="header-account">
                                @usersDisp@
                            </div>
                            <div class="search-open-button">
                                <i class="feather iconz-search"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="header-area" class="header-area-background-block"></div>
            </div>
        </header>
        <!-- Header Section Ends -->
        <div class="secondary-nav">
            <div class="container">
                <span class="shop-page-main-title"></span>
                <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
                    @breadCrumbs@
                </ol>
            </div>
        </div>
        <!-- Main Container Starts -->
        <div class="middle-content shop-page  main-color-text">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-md-3 main-sidebar-left">
                        <div class="order-page-sidebar-user-block hidden-xs hidden-sm">
                            <h5 class="user-title">{Личный кабинет}</h5>
                            <ul class="user-list">
                                <li><a href="/users/">@UsersLogin@</a></li>
                                <li class="@hideCatalog@"><a href="/users/order.html">{Отследить заказ}</a></li>
                                <li class="@hideCatalog@"><a href="/users/notice.html">{Уведомления о товарах}</a></li>
                                <li><a href="/users/message.html">{Связь с менеджерами}</a></li>
                                @php if($_SESSION['UsersId']) echo '<li><a href="?logout=true">{Выйти}</a></li>'; else echo '<li><a href="#" data-toggle="modal" data-target="#userModal">{Войти}</a></li>'; php@
                            </ul>
                        </div>
                        <h3 class="side-heading hidden-xs hidden-sm @hideSite@">{Категории}</h3>
                        <ul class="list-group sidebar-nav hidden-xs hidden-sm @hideSite@">
                            @leftCatal@
                        </ul>

                        <!-- Фасетный  фильтр -->
                        <div class="hide" id="faset-filter">
                            <h3 class="side-heading filter-title">{Фильтр товаров }<a href="?" id="faset-filter-reset" data-toggle="tooltip" data-placement="top" title="{Сбросить фильтр}"><span class="glyphicon glyphicon-remove"></span></a></h3>
                            <div class="list-group filter-body-fix">
                                <div id="faset-filter-body">{Загрузка}...</div>
                                <div id="price-filter-body" class="@hideCatalog@">
                                    <h4>{Цена}</h4>
                                    <form method="get" id="price-filter-form">
                                        <div class="row">
                                            <div class="col-md-6" id="price-filter-val-min">
                                                <span>{от}</span>
                                                <input type="text" class="form-control input-sm" name="min" value="@price_min@" >
                                            </div>
                                            <div class="col-md-6" id="price-filter-val-max">
                                                <span>{до}</span>
                                                <input type="text" class="form-control input-sm" name="max" value="@price_max@">
                                            </div>
                                        </div>
                                        <p></p>
                                        <div id="slider-range" class="slider-range"></div>
                                    </form>
                                </div>
                            </div>
                        </div>
                         <!--/ Фасетный фильтр -->
                        
                        
                        <div class="menu-fix">
                            <h3 class="side-heading">{Полезная информация}</h3>
                            <div class="list-group sidebar-nav">
                                @pageCatal@
                            </div>
                            <div class="banner-block">@banersDisp@</div>
                            @leftMenu@
                        </div>
                    </div>

                    <!-- jQuery -->
                    <script src="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin'].chr(47); php@js/jquery-1.11.0.min.js"></script>
                    <script src="java/jqfunc.js"></script>

                    <div class="col-xs-12 col-md-9 middle-content-fix">
                        @DispShop@
                        @getPhotos@
                    </div>

                    <div class="col-xs-12">
                        <div class="hidden-xs banner-block">@banersDispHorizontal@</div>
                    </div>

                    <div class="col-xs-12 @php __hide('now_buying'); php@ @hideSite@">
                        <h2 class="main-page-title">@now_buying@</h2>
                        <div class="owl-carousel nowBuy">
                            @nowBuy@
                        </div>
                    </div>
                    <!-- Primary Content Ends -->
                </div>
            </div>
        </div>
        <!-- Main Container Ends -->

        <!-- toTop -->
        <div class="visible-lg visible-md">
            <a href="#" id="toTop"><span id="toTopHover"></span>{Наверх}</a>
        </div>
        <!--/ toTop -->

        <!-- Footer Section Starts -->
        <footer id="footer-area">
            <!-- Footer Links Starts -->
            <div class="footer-links">
                <!-- Container Starts -->
                <div class="container">
                    <!-- Contact Us Starts -->
                    <div class="col-md-3 col-sm-8 col-xs-12">
                        <h5>{Контакты}</h5>
                        <ul>
                            <li class="footer-map">@streetAddress@</li>
                            <li class="footer-email"><a href="mailto:@adminMail@"><i class="fa fa-envelope-o"></i> @adminMail@</a></li>
                            <li class="footer-map">
                                <div>
                                    <a href="tel:@telNum@">@telNum@</a>
                                    <br><a href="tel:@telNum2@">@telNum2@</a>
                                    <br>@workingTime@
                                </div>
                            </li>
                        </ul>
                        <div class="footer-social">
                            <!-- Социальные сети -->
                            <a class="social-button header-top-link @php __hide('vk'); php@" title="ВКонтакте" href="@vk@" target="_blank"><em class="fa fa-vk" aria-hidden="true"></em></a>
                            <a class="social-button header-top-link @php __hide('telegram'); php@" title="Telegram" href="@telegram@" target="_blank"> <em class="fa fa-telegram" aria-hidden="true"></em></a>
                            <a class="social-button header-top-link @php __hide('odnoklassniki'); php@" title="Одноклассники" href="@odnoklassniki@" target="_blank"> <em class="fa fa-odnoklassniki" aria-hidden="true"></em></a>
                            <a class="social-button header-top-link @php __hide('youtube'); php@" title="Rutube" href="@youtube@" target="_blank"><em class="fa fa-youtube-play" aria-hidden="true"></em></a>
                            <a class="social-button header-top-link @php __hide('whatsapp'); php@" title="WhatsApp" href="@whatsapp@" target="_blank"><em class="fa fa-whatsapp" aria-hidden="true"></em></a>
                            <!-- / Социальные сети -->
                        </div>
                    </div>
                    <!-- Contact Us Ends -->
                    <!-- My Account Links Starts -->
                    <div class="col-md-3 col-sm-4 col-xs-12">
                        <h5>{Личный кабинет}</h5>
                        <ul>
                            <li class="@hideCatalog@"><a href="/users/order.html">{Отследить заказ}</a></li>
                            <li class="@hideCatalog@"><a href="/users/notice.html">{Уведомления о товарах}</a></li>
                            @php if($_SESSION['UsersId']) echo '<li><a href="/users/message.html">{Связь с менеджерами}</a></li>
                            <li><a href="?logout=true">{Выйти}</a></li>'; else echo '<li><a href="#" data-toggle="modal" data-target="#userModal">{Войти}</a></li>'; php@
                        </ul>
                    </div>
                    <!-- My Account Links Ends -->
                    <!-- Customer Service Links Starts -->
                    <div class="col-md-3 col-sm-4 col-xs-12">
                        <h5>{Навигация}</h5>
                        <ul>
                            <li class="@hideCatalog@"><a href="/price/" title="{Прайс-лист}">{Прайс-лист}</a></li>
                            <li><a href="/news/" title="{Новости}">{Новости}</a></li>
                            <li><a href="/gbook/" title="{Отзывы}">{Отзывы}</a></li>
                            <li class="@hideSite@"><a href="/map/" title="{Карта сайта}">{Карта сайта}</a></li>
                            <li><a href="/forma/" title="{Форма связи}">{Форма связи}</a></li>
                        </ul>
                    </div>
                    <!-- Customer Service Links Ends -->
                    <!-- Information Links Starts -->
                    <div class="col-md-3 col-sm-4 col-xs-12">
                        <h5>{Информация}</h5>
                        <ul>
                            @bottomMenu@

                        </ul>
                    </div>
                    <!-- Information Links Ends -->
                    <div class="col-xs-12">
                        <div class="footer-layer"></div>
                        <div class="row">
                            <div class="col-md-7">
                                <div class="footer-payment-methods">
                                    <img src="images/payment_methods.png">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <form action="/search/" class="news-subscription-form">
                                    <div class="clearfix">
                                        <div class="main-page-subscrition-form-input">
                                            <input class="form-control" name="words" maxlength="50" id=""  placeholder="{Поиск}..." required="" type="search" data-trigger="manual" data-container="body" data-toggle="popover" data-placement="bottom" data-html="true"  data-content="">
                                        </div>
                                        <button type="submit"><i class="feather iconz-search"></i></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Container Ends -->
            </div>
            <!-- Footer Links Ends -->
            <!-- Copyright Area Starts -->
            <div class="copyright">
                <!-- Container Starts -->
                <div class="container">
                    <div class="pull-right">@button@</div>
                    <p itemscope itemtype="http://schema.org/Organization">&copy; <span itemprop="name">@company@</span> @year@, {Тел}: <span itemprop="telephone">@telNum@</span>, <span itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">{Адрес}: <span itemprop="streetAddress">@streetAddress@</span></span><span itemprop="email" class="hide">@adminMail@</span></p>
                </div>
                <!-- Container Ends -->
            </div>
            <!-- Copyright Area Ends -->
        </footer>
        <!-- Footer Section Ends -->

        @editor@

        <!-- Fixed mobile bar -->
        <div class="bar-padding-fix visible-xs visible-sm"> </div>
        <nav class="navbar navbar-default navbar-fixed-bottom bar bar-tab visible-xs visible-sm">
            <a class="tab-item" href="/">
                <span class="icon icon-home"></span>
                <span class="tab-label">{Домой}</span>
            </a>
            <a class="tab-item @user_active@" @user_link@ data-target="#userModal">
                <span class="icon icon-person"></span>
                <span class="tab-label">{Кабинет}</span>
            </a>
            <a class="tab-item @cart_active@ @hideSite@" href="/order/" id="bar-cart">
                <span class="icon icon-download"></span> <span class="badge badge-positive" id="mobilnum">@cart_active_num@</span>
                <span class="tab-label">{Корзина}</span>
            </a>
            <a class="tab-item" href="#" data-toggle="modal" data-target="#searchModal">
                <span class="icon icon-search"></span>
                <span class="tab-label">{Поиск}</span>
            </a>
        </nav>
        <!--/ Fixed mobile bar -->

        <!-- Notification -->
        <div id="notification" class="success-notification" style="display: none;">
            <div  class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                <span class="notification-alert"> </span>
            </div>
        </div>
        <!--/ Notification -->

        <!-- Модальное окно авторизации-->
        <div class="modal fade bs-example-modal-sm" id="userModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-sm auto-modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title">{Авторизация}</h4>
                        <span id="usersError" class="hide">@usersError@</span>
                    </div>
                    <form method="post" name="user_forma">
                        <div class="modal-body">
                            <div class="form-group">

                                <input type="email" name="login" class="form-control" placeholder="Email" required="" value="@UserLogin@">
                                <span class="glyphicon glyphicon-remove form-control-feedback hide" aria-hidden="true"></span>
                                <br>

                                <input type="password" name="password" class="form-control" placeholder="{Пароль}" required="" value="@UserPassword@">
                                <span class="glyphicon glyphicon-remove form-control-feedback hide" aria-hidden="true"></span>
                            </div>
                            <div class="flex-row">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" value="1" name="safe_users" @UserChecked@> {Запомнить}
                                    </label>
                                </div>
                                <a href="/users/sms.html" class="pass @sms_login_enabled@">SMS</a>
                                <a href="/users/sendpassword.html" class="pass">{Забыли пароль}</a>
                            </div>

                        </div>
                        <div class="modal-footer flex-row">

                            <input type="hidden" value="1" name="user_enter">
                            <button type="submit" class="btn btn-primary">{Войти}</button>
                            <a href="/users/register.html" >{Зарегистрироваться}</a>
                        </div>

                        <!-- Yandex ID -->
                        @yandexid@
                        <!-- End Yandex ID -->

                        <!-- VK ID -->
                        @vkid@
                        <!-- End VK ID -->
                    </form>
                </div>
            </div>
        </div>
        <!--/ Модальное окно авторизации-->

        <!-- Модальное окно мобильного поиска -->
        <div class="modal fade bs-example-modal-sm" id="searchModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title">{Поиск}</h4>
                    </div>
                    <div class="modal-body">
                        <form  action="/search/" role="search" method="get">
                            <div class="input-group">
                                <input name="words" maxlength="50" class="form-control" placeholder="{Искать}.." required="" type="search">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search"></span></button>
                                </span>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
        <!--/ Модальное окно мобильного поиска -->

        <!-- Согласие на использование cookie  -->
        <div class="cookie-message hide"><p></p><a href="#" class="btn btn-default btn-sm">Ok</a></div>

        <!-- JQuery Plugins  -->
        <link href="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin']; php@css/bar.css" rel="stylesheet">
        <link href="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin']; php@css/swiper.min.css" rel="stylesheet">
        <link href="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin']; php@css/font-awesome.min.css" rel="stylesheet">
        <link href="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin']; php@css/iconfont.css" rel="stylesheet">
        <link href="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin']; php@css/bootstrap.offcanvas.min.css" rel="stylesheet">
        <link href="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin']; php@css/owl.carousel.min.css" rel="stylesheet">
        <link href="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin']; php@css/jquery.bxslider.css" rel="stylesheet">
        <link href="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin']; php@css/jquery-ui.min.css" rel="stylesheet">
        <link href="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin']; php@css/bootstrap-select.min.css" rel="stylesheet">
        <link href="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin']; php@css/suggestions.min.css" rel="stylesheet">
        <script src="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin'].chr(47); php@js/bootstrap.min.js"></script>
        <script src="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin'].chr(47); php@js/owl.carousel.min.js"></script>
        <script src="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin'].chr(47); php@js/swiper.js"></script>
        <script src="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin'].chr(47); php@js/unishop.js"></script>
        <script src="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin'].chr(47); php@js/bootstrap.offcanvas.min.js"></script>
        <script src="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin'].chr(47); php@js/bootstrap-select.min.js"></script>
        <script src="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin'].chr(47); php@js/jquery.lazyloadxt.min.js"></script>
        <script src="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin']; php@/js/phpshop.js"></script>
        <script src="phpshop/locale/@php echo $_SESSION['lang']; php@/template.js"></script>
        <script src="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin'].chr(47); php@js/jquery-ui.min.js"></script>
        <script src="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin'].chr(47); php@js/jquery.ui.touch-punch.min.js"></script>
        <script src="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin'].chr(47); php@js/jquery.bxslider.min.js"></script>
        <script src="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin']; php@/js/jquery.cookie.js"></script>
        <script src="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin'].chr(47); php@js/jquery.waypoints.min.js"></script>
        <script src="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin'].chr(47); php@js/inview.min.js"></script>
        <script src="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin'].chr(47); php@js/jquery.maskedinput.min.js"></script>
        <script src="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin'].chr(47); php@js/jquery.suggestions.min.js"></script>
        <script src="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin'].chr(47); php@js/jquery.ui.touch-punch.min.js"></script>
        @visualcart_lib@