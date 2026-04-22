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
        @verification@
        <link rel="apple-touch-icon" href="@icon@">
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
        <link rel="preload" href="@pathTemplateMin@css/@terra_theme@.css" as="style">     
        <link rel="preload" href="@pathTemplate@css/font-awesome.min.css"  as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700&amp;subset=cyrillic"  as="font" type="font/woff2" crossorigin>

        <!-- Bootstrap -->
        <link href="@pathTemplate@css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body id="body" data-dir="@ShopDir@" data-path="@php echo $GLOBALS['PHPShopNav']->objNav['path']; php@" data-id="@php echo $GLOBALS['PHPShopNav']->objNav['id']; php@" data-token="@dadataToken@">

        <!-- Theme -->
        <link id="bootstrap_theme" data-name="@php echo $_SESSION['skin']; php@" href="@pathTemplateMin@css/@terra_theme@.css" rel="stylesheet">

        <!-- Template -->
        <link href="@pathTemplate@css/animate.css" rel="stylesheet">
        <link href="@pathTemplateMin@/style.css" rel="stylesheet">
        <link href="@pathTemplateMin@css/responsive.css" rel="stylesheet">

        <!-- Стикер-полоска -->
        <div class="@php __hide('sticker_top'); php@">
            <div class="top-banner @php __hide('sticker_close','cookie'); php@">
                <div class="sticker-text">@sticker_top@</div>
                <span class="close sticker-close">x</span>
            </div>
        </div>
        <!-- /Стикер-полоска -->

        <header>
            <div class="header-top">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="header-links">
                                <div class="col-md-8 col-sm-7 header-menu-wrapper">
                                    <div class="row">
                                        <ul class="top-nav  main-top">
                                            <li class="active"><a href="/">@name@</a></li>

                                            @php
                                            if(empty(PHPShopParser::get('hideSite')))
                                            echo PHPShopParser::get('topMenu');
                                            php@


                                            <li><a href="/news/">{Новости}</a></li>
                                    </div>
                                    </ul>
                                </div>
                                <div class="col-md-4 col-sm-5 @hideSite@">
                                    <ul class="top-nav-right">
                                        <li><a href="/compare/"><i class="fa fa-sliders" aria-hidden="true"></i> {Сравнить}</a></li>
                                        @wishlist@
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div><!-- /row -->
                </div>
            </div><!-- /header-top -->

            <div class="header-middle" id="header-area">
                <div class="container">
                    <div class="row">

                        <div class="col-md-3 col-sm-3 col-xs-12">
                            <a id="logo" href="/" title="@name@"><img src="@logo@" alt="" class="img-responsive" /></a>
                        </div>

                        <div class="col-md-6 col-sm-7 col-xs-12">
                            <div class="header-contacts">
                                <a class="header-tel" href="tel:8@telNumMobile@">@telNumMobile@</a>
                                <br><a class="header-tel" href="tel:8@telNum2@">@telNum2@</a>
                                <br>@returncall@
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-2 header-middle-right hidden-xs hidden-sm @hideCatalog@">
                            <div id="cart">
                                <a id="cartlink" class="dropdown-toggle" href="/order/">
                                    <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                                    <span id="cart-total">
                                        <span class="count" id="num">@num@</span>
                                    </span>
                                </a>
                                @visualcart@
                            </div>
                            <div class="header-account ">
                                @usersDisp@
                            </div>
                            <div class="search-open-button">
                                <i class="fa fa-search"></i>
                            </div>
                        </div>

                    </div><!-- /row -->
                </div>
            </div><!-- /header-middle -->



            <nav id="main-menu" class="navbar ">
                <div class="container">
                    <div class="row">

                        <div class="col-xs-12">
                            <div class="navbar-header visible-xs">
                                <button type="button" class="btn btn-navbar navbar-toggle main-menu-button" data-toggle="collapse" data-target=".navbar-cat-collapse"><span class="sr-only">{Меню}</span><i class="fa fa-bars"></i></button>
                            </div>

                            <div class="collapse navbar-collapse navbar-cat-collapse">
                                <ul class="nav navbar-nav main-navbar-top ">

                                    @php
                                    if(!empty(PHPShopParser::get('hideSite')))
                                    echo PHPShopParser::get('topMenu');
                                    php@

                                    <li class="main-navbar-top-catalog">
                                        <a href="#" id="nav-catalog-dropdown-link" class="nav-catalog-dropdown-link"><i class="fa fa-bars"></i> {Каталог}</a>
                                        <ul class="main-navbar-list-catalog-wrapper fadeIn animated">
                                            @leftCatal@
                                        </ul>
                                    </li>
                                    @leftCatal@
                                </ul>

                            </div>
                        </div>

                    </div><!-- /row -->
                </div>
            </nav>
        </header>

        <!-- jQuery -->
        <script src="@pathTemplate@/js/jquery-1.11.0.min.js"></script>

        <div class="container">
            <div class="row">
                <div class="col-xs-12">

                    <!-- Slider Section Starts -->
                    <!-- Nested Container Starts -->
                    <!-- Carousel Starts -->
                    <div class="slider">
                        <div class="container">
                            <div class="row">
                                @imageSlider@
                            </div>
                        </div>
                    </div>

                    <!-- Carousel Ends -->
                    <!-- Nested Container Ends -->
                </div>
                <!-- Slider Section Ends -->

                <ul class="brand-list @hideSite@">@brandsList@</ul>
            </div>
        </div>
    </div>
    <div class="main-container main-page container">
        <div class="row">
            <div class="col-xs-12">
                <!-- Featured Products Starts -->
                <ul class="nav nav-tabs @hideSite@">
                    <li class="active @php __hide('specMainIcon'); php@"><a data-toggle="tab" href="#newprod">{Новинки}</a></li>
                    <li class="@php __hide('specMain'); php@"><a data-toggle="tab" href="#specprod">{Спецпредложения}</a></li>
                </ul>

                <div class="tab-content @hideSite@">
                    <div id="newprod" class="tab-pane fade in active">
                        <div class="row products-list newitems-list">
                            @specMainIcon@
                        </div>
                    </div>
                    <div id="specprod" class="tab-pane fade @hideSite@">
                        <div class="row products-list spec-list">
                            @specMain@
                        </div>
                    </div>
                </div>
                <!-- Featured Products Ends -->

                <!-- Popular Products Starts -->
                <h2 class="product-head @php __hide('nowBuy'); php@ @hideSite@">{Популярные товары}</h2>
                <div class="row products-list nowbuy-list @hideSite@">
                    @nowBuy@
                </div>

                <!-- Popular Products Ends -->
                <div class="row">
                    <div class="col-md-3 @php __hide('productDay'); php@ product-day-wrap @hideCatalog@">@productDay@</div>
                    <div class="col-md-9 catalog-table-wrapper">
                        <h2 class="product-head">@mainContentTitle@</h2>
                        <div>@mainContent@</div></div>
                </div>
                <div class="catalog-block @hideSite@">@leftCatalTable@</div>
            </div>

        </div><!-- /row -->
        <div class="top-col-banners text-center">@banersDispHorizontal@</div>
    </div>
    <!-- Main Container Ends -->

    @editor@

    <div class="copyright">
        <!-- Container Starts -->
        <div class="container">
            <div class="pull-right">@button@</div>
            <p itemscope itemtype="http://schema.org/Organization">&copy; <span itemprop="name">@company@</span> @year@, {Тел}: <span itemprop="telephone">@telNum@ @telNum2@ @workingTime@</span>, <span itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">{Адрес}: <span itemprop="streetAddress">@streetAddress@</span></span><span itemprop="email" class="hide">@adminMail@</span></p>
        </div>
        <!-- Container Ends -->
    </div>

    <!-- Footer Section Starts -->
    <footer id="footer-area">
        <div class="footer-links">
            <div class="container">
                <div class="row">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <a id="logo-footer @php __hide('logo'); php@" href="/" title="@name@"><img src="@logo@" alt="" class="img-responsive" /></a>
                    </div>

                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <h5>{Информация}</h5>
                        <ul>
                            @bottomMenu@
                        </ul>
                        <!-- Социальные сети -->
                        <ul class="social-menu list-inline">
                            <li class="list-inline-item @php __hide('vk'); php@"><a class="social-button header-top-link" title="ВКонтакте" href="@vk@" target="_blank"><em class="fa fa-vk" aria-hidden="true"></em></a></li>
                            <li class="list-inline-item @php __hide('telegram'); php@"><a class="social-button header-top-link" title="Telegram" href="@telegram@" target="_blank"> <em class="fa fa-telegram" aria-hidden="true"></em></a></li>
                            <li class="list-inline-item @php __hide('odnoklassniki'); php@"><a class="social-button header-top-link" title="Одноклассники" href="@odnoklassniki@" target="_blank"> <em class="fa fa-odnoklassniki" aria-hidden="true"></em></a></li>
                            <li class="list-inline-item @php __hide('youtube'); php@"><a class="social-button header-top-link" title="Rutube" href="@youtube@" target="_blank"><em class="fa fa-youtube-play" aria-hidden="true"></em></a></li>
                            <li class="list-inline-item  @php __hide('whatsapp'); php@"><a class="social-button header-top-link" title="WhatsApp" href="@whatsapp@" target="_blank"><em class="fa fa-whatsapp" aria-hidden="true"></em></a></li>
                        </ul>
                        <!-- / Социальные сети -->
                    </div>

                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <h5>{Мой кабинет}</h5>
                        <ul>
                            <li><a href="/users/">@UsersLogin@</a></li>
                            <li class="@hideCatalog@"><a href="/users/order.html">{Отследить заказ}</a></li>
                            <li class="@hideCatalog@"><a href="/users/notice.html">{Уведомления о товарах}</a></li>
                            <li><a href="/users/message.html">{Связь с менеджерами}</a></li>
                            @php if($_SESSION['UsersId']) echo '<li><a href="/users/wishlist.html">{Отложенные товары}</a></li>
                            <li><a href="?logout=true">{Выйти}</a></li>'; else echo '<li><a href="#" data-toggle="modal" data-target="#userModal">{Войти}</a></li>'; php@
                        </ul>
                    </div>

                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <h5>{Навигация}</h5>
                        <ul>
                            <li class="@hideCatalog@"><a href="/price/" title="Прайс-лист">{Прайс-лист}</a></li>
                            <li><a href="/news/" title="Новости">{Новости}</a></li>
                            <li><a href="/gbook/" title="Отзывы">{Отзывы}</a></li>
                            <li class="@hideSite@"><a href="/map/" title="Карта сайта">{Карта сайта}</a></li>
                            <li><a href="/forma/" title="Форма связи">{Форма связи}</a></li>
                        </ul>
                    </div>

                </div><!-- /row -->
            </div>
        </div>
    </footer>
    <!-- Footer Section Ends -->

    <!-- Fixed mobile bar -->
    <div class="bar-padding-fix visible-xs"> </div>
    <nav class="navbar navbar-default navbar-fixed-bottom bar bar-tab visible-xs visible-sm">
        <a class="tab-item active" href="/">
            <span class="icon icon-home"></span>
            <span class="tab-label">{Домой}</span>
        </a>
        <a class="tab-item @user_active@" @user_link@ data-target="#userModal">
            <span class="icon icon-person"></span>
            <span class="tab-label">{Кабинет}</span>
        </a>
        <a class="tab-item @cart_active@ @hideCatalog@" href="/order/" id="bar-cart">
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
                    <h4 class="modal-title">Поиск</h4>
                </div>
                <div class="modal-body">
                    <form  action="/search/" role="search" method="get">
                        <div class="input-group">
                            <input name="words" maxlength="50" class="form-control" placeholder="Искать.." required="" type="search">
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
        <form id="search_form_small" action="/search/" role="search" method="get" class="header-search-form">
            <input class="form-control input-lg" name="words" maxlength="50"  placeholder="{Поиск}..." required="" type="search" data-trigger="manual" data-container="body" data-toggle="popover" data-placement="bottom" data-html="true"  data-content="">
            <button class="header-search-button" type="submit">
                <i class="fa fa-search" aria-hidden="true"></i>
            </button>
        </form>
        <i class="search-close fa fa-times" aria-hidden="true"></i>
    </div>

    <!-- Согласие на использование cookie  -->
    <div class="cookie-message hide"><p></p><a href="#" class="btn btn-default btn-sm">Ok</a></div>

    <!-- JQuery Plugins  -->
    <link href="@pathTemplateMin@css/bar.css" rel="stylesheet">
    <link href="@pathTemplate@css/jquery.bxslider.css" rel="stylesheet">
    <link href="@pathTemplate@css/jquery-ui.min.css" rel="stylesheet">
    <link href="@pathTemplate@css/bootstrap-select.min.css" rel="stylesheet">
    <link href="@pathTemplate@css/suggestions.min.css" rel="stylesheet">
    <link href="@pathTemplateMin@css/slick.css" rel="stylesheet">
    <link href="@pathTemplate@css/slick-theme.css" rel="stylesheet"/>
    <link href="@pathTemplate@css/font-awesome.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700&amp;subset=cyrillic" rel="stylesheet">
    <script src="@pathTemplate@/js/bootstrap.min.js"></script>
    <script src="@pathTemplate@/js/jquery.lazyloadxt.min.js"></script>
    <script src="@pathTemplate@/js/jquery.matchHeight.js"></script>
    <script src="@pathTemplate@/js/slick.min.js"></script>
    <script src="@pathTemplate@/js/terra.js"></script>
    <script src="@pathTemplate@/js/bootstrap-select.min.js"></script>
    <script src="@pathTemplate@/js/phpshop.js"></script>
    <script src="@pathTemplate@/js/jquery-ui.min.js"></script>
    <script src="@pathMin@java/jqfunc.js"></script>
    <script src="phpshop/locale/@php echo $_SESSION['lang']; php@/template.js"></script>
    <script src="@pathTemplate@/js/flipclock.min.js"></script>
    <script src="@pathTemplate@/js/jquery.cookie.js"></script>
    <script src="@pathTemplate@/js/jquery.maskedinput.min.js"></script>
    <script src="@pathTemplate@/js/jquery.suggestions.min.js"></script>
    @visualcart_lib@