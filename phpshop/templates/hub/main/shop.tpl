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
        <link rel="preload" href="@pathTemplate@css/bootstrap.min.css" as="style">
        <link rel="preload" href="@pathTemplateMin@/style.css" as="style">
        <link rel="preload" href="@pathTemplateMin@css/@hub_theme@.css" as="style">     
        <link rel="preload" href="@pathTemplate@css/font-awesome.min.css" as="font" type="font/woff2" crossorigin>

        <!-- Bootstrap -->
        <link href="@pathTemplate@css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body id="body" data-dir="@ShopDir@" data-path="@php echo $GLOBALS['PHPShopNav']->objNav['path']; php@" data-id="@php echo $GLOBALS['PHPShopNav']->objNav['id']; php@" data-subpath="@php echo $GLOBALS['PHPShopNav']->objNav['name']; php@" data-token="@dadataToken@">

        <!-- Template -->
        <link href="@pathTemplate@css/swiper.min.css" rel="stylesheet">
        <link href="@pathTemplateMin@/style.css" rel="stylesheet">

        <!-- Theme -->
        <link id="bootstrap_theme" data-name="@php echo $_SESSION['skin']; php@" href="@pathTemplateMin@css/@hub_theme@.css" rel="stylesheet">  

        <!-- Стикер-полоска -->
        <div class="@php __hide('sticker_top'); php@">
            <div class="top-banner @php __hide('sticker_close','cookie'); php@">
                <div class="sticker-text">@sticker_top@</div>
                <span class="close sticker-close">x</span>
            </div>
        </div>
        <!-- /Стикер-полоска -->

        <!-- Header Section Starts -->
        <header>
            <div class="header-top">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12 col-sm-9 col-md-8 top-mobile-fix">
                            <a class="header-top-link header-link-contact header-link-color" href="tel:@telNum@">@telNum@</a> <a class=" header-top-link header-link-contact header-link-color" href="tel:@telNum2@">@telNum2@</a>
                            <span class="header-company-name header-link-color">
                                @name@
                            </span>
                            <span class="header-returncall-wrapper">
                                @returncall@
                            </span>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-4 top-mobile-fix @hideSite@">
                            <div class="row">
                                <div class="header-top-dropdown hidden-xs hidden-md">
                                    <!--
                                        <div style="display: none">
                                            @valutaDisp@
                                        </div>
                                    -->
                                </div>
                                <div class="header-wishlist">
                                    <a class="header-link-color link" href="/compare/">
                                        <span class=""> {Сравнить} (<span id="numcompare">@numcompare@</span>)<span id="wishlist-total" ></span>
                                        </span>
                                    </a>
                                    @wishlist@
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="header-bottom">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-8 col-sm-8 col-md-2 bottom-mobile-fix @php __hide('logo'); php@">
                            <div class="row">
                                <div id="logo">
                                    <a href="/">
                                        <img src="@logo@" alt="" class="img-responsive" /></a>
                                </div>
                            </div>
                        </div>
                        <nav class="navbar-default hidden-md hidden-lg" id="navigation main-menu">
                            <div class="container nav-bar-menu-header">
                                <div class="navbar-header">
                                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                                        <span class="sr-only">Toggle navigation</span>
                                        <span class="icon-bar"></span>
                                        <span class="icon-bar"></span>
                                        <span class="icon-bar"></span>
                                    </button>

                                </div>

                                <div id="navbar" class="navbar-collapse collapse hidden-md hidden-lg">
                                    <ul class="nav navbar-nav">
                                        <li class="active visible-lg"><a href="/" title="{Домой}"><span class="glyphicon glyphicon-home"></span></a></li>

                                        <!-- dropdown catalog menu -->
                                        <li id="catalog-dropdown" class="visible-lg visible-md visible-sm @hideSite@">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">{Каталог} <b class="caret"></b></a>        
                                            <ul class="dropdown-menu mega-menu">
                                                @leftCatal@
                                            </ul>
                                        </li>
                                        <!-- dropdown catalog menu mobile-->
                                        <li class="dropdown visible-xs@hideSite@">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{Каталог} <span class="caret"></span></a>
                                            <ul class="dropdown-menu" role="menu">
                                                @menuCatal@
                                            </ul>
                                        </li>

                                        @topMenu@
                                        <li class="visible-xs @hideSite@"><a href="/users/wishlist.html">{Отложенные товары}</a></li>
                                        <li class="visible-xs"><a href="/news/">{Новости}</a></li>
                                        <li class="visible-xs"><a href="/gbook/">{Отзывы}</a></li>
                                        <li class="visible-xs @hideCatalog@"><a href="/price/">{Прайс-лист}</a></li>
                                        <li class="visible-xs @hideSite@"><a href="/map/">{Карта сайта}</a></li>
                                    </ul>

                                </div><!--/.nav-collapse -->
                            </div>
                        </nav>
                        <div class="col-md-8 hidden-xs hidden-sm header-menu-wrapper">
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
                                    @topcatMenu@
                                    @topMenu@
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-7 col-xs-12 col-md-2 hidden-xs hidden-sm header-text-right bottom-mobile-fix">
                            <div id="cart" class="btn-group @hideCatalog@">
                                <a href="/order/" id="cartlink" type="button"  class="btn-cartlink " data-trigger="hover" data-container="body"  data-placement="bottom" data-html="true" data-url="/order/" data-content='@visualcart@'>
                                    <i class="iconz-cart"></i>
                                </a>
                                <div class="cart-number" id="cartlink" type="button" data-toggle="dropdown" class="btn-cartlink dropdown-toggle" data-trigger="click" data-container="body"  data-placement="bottom" data-html="true" data-url="/order/" data-content='@visualcart@'>
                                    <span id="num1">
                                        @num@
                                    </span>
                                </div>
                                @visualcart@
                            </div>
                            <div class="header-account">
                                @usersDisp@
                            </div>
                            <div class="search-open-button">
                                <i class="icons-search"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Header Section Ends -->
        <div class="breadcrumbs-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-sm-7 col-md-8">
                        <div class="row">
                            <h2 class="shop-page-main-title"></h2>
                            <div class="catalog-description-text"></div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-5 col-md-4">
                        <div class="row">
                            <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
                                @breadCrumbs@
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Main Container Starts -->
        <div class="main-container container shop-page">
            <!-- Nested Row Starts -->
            <div class="row">
                <!-- Sidebar Starts -->
                <div class="col-xs-12 col-md-3 sidebar-right">
                    <div class="order-page-sidebar-user-block hidden-xs hidden-sm">
                        <div class="user-title">{Личный кабинет}</div>
                        <ul class="user-list">
                            <li><a href="/users/">@UsersLogin@</a></li>
                            <li class="@hideCatalog@"><a href="/users/order.html">{Отследить заказ}</a></li>
                            <li class="@hideCatalog@"><a href="/users/notice.html">{Уведомления о товарах}</a></li>
                            <li><a href="/users/message.html">{Связь с менеджерами}</a></li>
                            @php if($_SESSION['UsersId']) echo '<li><a href="?logout=true">{Выйти}</a></li>'; php@
                        </ul>
                    </div>
                    <!-- Categories Links Starts -->
                    <div class="@php __hide('leftCatal'); php@ left-catal @hideSite@">
                        <div class="side-heading hidden-xs hidden-sm">{Категории}</div>
                        <ul class="list-group sidebar-nav hidden-xs hidden-sm">
                            @leftCatal@
                        </ul>
                    </div>
                    <div class="@php __hide('pageCatal'); php@ page-catal">
                        <div class="side-heading hidden-xs hidden-sm">{Полезная информация}</div>
                        <div class="list-group sidebar-nav">
                            @pageCatal@
                            @banersDisp@
                        </div>  
                    </div> 
                    <!-- Categories Links Ends -->

                </div>
                <!-- Sidebar Ends -->

                <!-- jQuery -->
                <script src="@pathTemplate@/js/jquery-1.11.0.min.js"></script>
                <script src="@pathMin@java/jqfunc.js"></script>

                <!-- Primary Content Starts -->
                <div class="col-md-9 col-xs-12 middle-content-fix">
                    <div class="row">
                        @DispShop@
                        @getPhotos@
                    </div>
                </div>
                <!-- Primary Content Ends -->
            </div>
            <!-- Nested Row Ends -->
        </div>
        <section class="hidden-xs main-page-banner container ">
            <div class="top-col-banners row">@banersDispHorizontal@</div>
        </section>
        <!-- Main Container Ends -->
        <section class="new-arrivals @php __hide('specMainIcon'); php@ @hideSite@">
            <div class="container">
                <div class="row">
                    <h4 class="product-head page-header"><a href="/newtip/" title="{Все новинки}">{Новинки}</a></h4>
                    <div class="owl-carousel spec-main-icon">
                        @specMainIcon@
                    </div>
                </div>
            </div>
        </section>

        <section class="brands-slider @php __hide('topBrands'); php@ @hideSite@">
            <div class="container">
                <div class="top-brands-wrapper">
                    <ul class="owl-carousel top-brands">
                        @brandsList@
                    </ul>
                </div>
            </div>
        </section>
        <section class="nowBuyWrapper @php __hide('now_buying'); php@">
            <div class="container">
                <div class="row">
                    <h4 class="product-head page-header">@now_buying@</h4>
                    <div class="owl-carousel nowBuy">
                        @nowBuy@
                    </div>
                </div>
            </div>
        </section>
        <!-- toTop -->
        <div class="visible-lg visible-md">
            <a href="#" id="toTop"><span id="toTopHover"></span>{Наверх}</a>
        </div>
        <!--/ toTop -->
        <!-- Top brands -->
        <!-- Footer Section Starts -->
        <footer id="footer-field">
            <!-- Footer Links Starts -->
            <div class="footer-link">
                <!-- Container Starts -->
                <div class="container">
                    <!-- Information Links Starts -->
                    <div class="col-md-3 col-sm-4 col-xs-12">
                        <h5>{Информация}</h5>
                        <ul>
                            @bottomMenu@

                        </ul>
                        <!-- Социальные сети -->
                        <ul class="social-menu list-inline">
                            <li class="list-inline-item @php __hide('vk'); php@"><a class="social-button header-top-link" title="ВКонтакте" href="@vk@" target="_blank"><em class="fa fa-vk" aria-hidden="true">.</em></a></li>
                            <li class="list-inline-item @php __hide('telegram'); php@"><a class="social-button header-top-link" title="Telegram" href="@telegram@" target="_blank"> <em class="fa fa-telegram" aria-hidden="true">.</em></a></li>
                            <li class="list-inline-item @php __hide('odnoklassniki'); php@"><a class="social-button header-top-link" title="Одноклассники" href="@odnoklassniki@" target="_blank"> <em class="fa fa-odnoklassniki" aria-hidden="true">.</em></a></li>
                            <li class="list-inline-item @php __hide('youtube'); php@"><a class="social-button header-top-link" title="Rutube" href="@youtube@" target="_blank"><em class="fa fa-youtube-play" aria-hidden="true">.</em></a></li>
                            <li class="list-inline-item  @php __hide('whatsapp'); php@"><a class="social-button header-top-link" title="WhatsApp" href="@whatsapp@" target="_blank"><em class="fa fa-whatsapp" aria-hidden="true">.</em></a></li>
                        </ul>
                        <!-- / Социальные сети -->
                    </div>
                    <!-- Information Links Ends -->
                    <!-- My Account Links Starts -->
                    <div class="col-md-3 col-sm-4 col-xs-12">
                        <h5>{Личный кабинет}</h5>
                        <ul>
                            <li><a href="/users/">@UsersLogin@</a></li>
                            <li class="@hideCatalog@"><a href="/users/order.html">{Отследить заказ}</a></li>
                            <li class="@hideCatalog@"><a href="/users/notice.html">{Уведомления о товарах}</a></li>
                            @php if($_SESSION['UsersId']) echo '<li><a href="/users/message.html">{Связь с менеджерами}</a></li>
                            <li><a href="?logout=true">{Выйти}</a></li>';else echo '<li><a href="#" data-toggle="modal" data-target="#userModal">{Войти}</a></li>'; php@
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
                    <!-- Contact Us Starts -->
                    <div class="col-md-3 col-sm-8 col-xs-12">
                        <h5>{Контакты}</h5>
                        <ul>
                            <li class="footer-map">@streetAddress@</li>
                            <li class="footer-map">@workingTime@</li>
                            <li class="footer-map">@telNum@</li>
                            <li class="footer-map">@telNum2@</li>

                            <li class="footer-email"><a href="mailto:@adminMail@">@adminMail@</a></li>                              
                        </ul>
                        <div class="form-group">
                            <form id="search_form" action="/search/" role="search" method="get" class="footer-search-form">
                                <input class="form-search-footer form-control input-lg" name="words" maxlength="50"  placeholder="{Поиск}..." required="" type="search" data-trigger="manual" data-container="body" data-toggle="popover" data-placement="bottom" data-html="true"  data-content="">
                                <button class="footer-search-button" type="submit">
                                    <i class="fa fa-search" aria-hidden="true"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <!-- Contact Us Ends -->
                </div>
                <!-- Container Ends -->
            </div>
            <!-- Footer Links Ends -->
            <!-- Copyright Area Starts -->
            <div class="copyright">
                <!-- Container Starts -->
                <div class="container">
                    <div class="pull-right">@button@</div>
                    <p itemscope itemtype="http://schema.org/Organization">© <span itemprop="name">@company@</span> @year@, {Тел}: <span itemprop="telephone">@telNum@</span>, <span itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">{Адрес}: <span itemprop="streetAddress">@streetAddress@</span></span><span itemprop="email" class="hide">@adminMail@</span></p>
                </div>
                <!-- Container Ends -->
            </div>
            <!-- Copyright Area Ends -->
        </footer>
        <!-- Footer Section Ends -->
        <div class="modal product-number-fix fade bs-example-modal-sm" id="modalProductView" tabindex="-1" role="dialog"  aria-hidden="true">
            <div class="modal-dialog modal-lg fastViewContent"></div> 
        </div>
        @editor@

        <!-- Fixed mobile bar -->
        <div class=""> </div>
        <nav class="navbar navbar-default navbar-fixed-bottom bar bar-tab">
            <div class="container">
                <div class="nav-user">
                    @usersDisp@
                </div>
                <div class="search-fixed-block hidden-md hidden-lg">
                    <a class="tab-item" href="#" data-toggle="modal" data-target="#searchModal">
                        <span class="icon icon-search"></span>
                    </a>
                </div>
                <div class="wishlist-block @hideSite@">
                    @wishlist@
                </div>

                <div class="cart-block @hideCatalog@">
                    <a href="/order/">
                        <i class="icons-cart"></i>
                        <span class="text fix">{Моя корзина}</span>
                        <span id="num3" class="">@num@</span>
                        <span class="sum1-wrapper text">
                            <span id="sum2">@sum@</span>
                            <span class="rubznak">@productValutaName@</span>
                        </span>
                    </a>
                </div>
            </div>
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
                            <button type="submit" class="btn btn-main">{Войти}</button>
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


        <div class="search-big-block">
            <form id="search_form" action="/search/" role="search" method="get" class="header-search-form">
                <input class="form-control input-lg" name="words" maxlength="50" id=""  placeholder="{Поиск}..." required="" type="search" data-trigger="manual" data-container="body" data-toggle="popover" data-placement="bottom" data-html="true"  data-content="">
                <button class="header-search-button" type="submit">
                    <i class="fa fa-search" aria-hidden="true"></i>
                </button>
            </form>
            <i class="search-close fa fa-times" aria-hidden="true"></i>
        </div>

        <!-- Согласие на использование cookie  -->
        <div class="cookie-message hide"><p></p><a href="#" class="btn btn-default btn-sm">Ok</a></div>

        <!-- JQuery Plugins  -->
        <link href="@pathTemplate@css/font-awesome.min.css" rel="stylesheet">
        <link href="@pathTemplateMin@css/icon.css" rel="stylesheet">
        <link href="@pathTemplate@css/jquery.bxslider.css" rel="stylesheet">
        <link href="@pathTemplate@css/jquery-ui.min.css" rel="stylesheet">
        <link href="@pathTemplate@css/bootstrap-select.min.css" rel="stylesheet">
        <link href="@pathTemplate@css/owl.carousel.min.css" rel="stylesheet">
        <link href="@pathTemplateMin@css/bar.css" rel="stylesheet">
        <link href="@pathTemplate@css/suggestions.min.css" rel="stylesheet">
        <script src="@pathTemplate@/js/bootstrap.min.js"></script>
        <script src="@pathTemplate@/js/jquery.lazyloadxt.min.js"></script>
        <script src="@pathTemplate@/js/swiper.js"></script>
        <script src="@pathTemplate@/js/hub.js"></script>
        <script src="@pathTemplate@/js/bootstrap-select.min.js"></script>
        <script src="@pathTemplate@/js/jquery.bxslider.min.js"></script>        
        <script src="@pathTemplate@/js/owl.carousel.min.js"></script>
        <script src="@pathTemplate@/js/phpshop.js"></script>
        <script src="phpshop/locale/@php echo $_SESSION['lang']; php@/template.js"></script>
        <script src="@pathTemplate@/js/jquery-ui.min.js"></script>
        <script src="@pathTemplate@/js/jquery.ui.touch-punch.min.js"></script>
        <script src="@pathTemplate@/js/jquery.cookie.js"></script>
        <script src="@pathTemplate@/js/jquery.waypoints.min.js"></script>
        <script src="@pathTemplate@/js/inview.min.js"></script>
        <script src="@pathTemplate@/js/jquery.maskedinput.min.js"></script>
        <script src="@pathTemplate@/js/jquery.suggestions.min.js"></script>
        @visualcart_lib@