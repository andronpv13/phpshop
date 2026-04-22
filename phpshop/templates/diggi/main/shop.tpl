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
        <meta content="General" name="rating">
        <meta name="ROBOTS" content="ALL">
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
        <link rel="preload" href="@pathTemplate@css/swiper.min.css" as="style">
        <link rel="preload" href="@pageCss@" as="style">
        <link rel="preload" href="@pathTemplateMin@css/@diggi_theme@.css" as="style">
        <link rel="preload" href="@pathTemplateMin@css/responsive.css" as="style">
        <link rel="preload" href="@pathTemplate@css/font-awesome.min.css"  as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="//fonts.googleapis.com/css?family=Roboto+Condensed&display=swap&subset=cyrillic"  as="font" type="font/woff2" crossorigin>

        <!-- Bootstrap -->
        <link href="@pathTemplate@css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body id="body" data-dir="@ShopDir@" data-path="@php echo $GLOBALS['PHPShopNav']->objNav['path']; php@" data-id="@php echo $GLOBALS['PHPShopNav']->objNav['id']; php@" data-subpath="@php echo $GLOBALS['PHPShopNav']->objNav['name']; php@" data-token="@dadataToken@">

        <!-- Template -->

        <link href="//fonts.googleapis.com/css?family=Roboto+Condensed&display=swap&subset=cyrillic" rel="stylesheet">
        <link href="@pageCss@" rel="stylesheet">
        <link href="@pathTemplateMin@css/responsive.css" rel="stylesheet">

        <!-- Theme -->
        <link id="bootstrap_theme" data-name="@php echo $_SESSION['skin']; php@" href="@pathTemplateMin@css/@diggi_theme@.css" rel="stylesheet">

        <!-- Стикер-полоска -->
        <div class="@php __hide('sticker_top'); php@">
            <div class="top-banner @php __hide('sticker_close','cookie'); php@">
                <div class="sticker-text">@sticker_top@</div>
                <span class="close sticker-close">x</span>
            </div>
        </div>
        <!-- /Стикер-полоска -->

        <!-- Header Section Starts -->
        <header id="header-area" class="header-wrap inner">
            <!-- Header Top Starts -->
            <div class="header-top">
                <!-- Nested Container Starts -->
                <div class="container">
                    <!-- Row Starts -->
                    <div class="col-xs-12">
                        <div class="header-links header-color">
                            <ul class="nav navbar-nav pull-left">
                                <li>
                                    <a class="hidden-xs hidden-sm hidden-md link" href="/">
                                        <i class="fa fa-home" title="{Домой}"></i>
                                        <span class="hidden-sm hidden-xs">
                                            {Домой}
                                        </span>
                                    </a>                                       
                                </li>
                                @wishlist@
                                <li class="@hideSite@">
                                    <a class="hidden-xs hidden-sm link" href="/compare/">
                                        <i class="fa fa-plus" title="{Сравнить}"></i>
                                        <span class="hidden-sm hidden-xs">{Сравнить} (<span id="numcompare">@numcompare@</span>)</span>
                                    </a>
                                    <a href="/compare/" class="btn btn-main btn-sm hidden-md hidden-lg">
                                        <i class="fa fa-plus" title="{Сравнить}"></i>
                                        {Сравнить} (<span id="numcompare">@numcompare@</span>)
                                    </a>
                                </li>
                                @usersDisp@
                            </ul>
                        </div>
                    </div>
                    <!-- Logo Starts -->
                    <div class="col-md-2 col-sm-12 col-xs-12 wrapper-fix">
                        <div id="logo">
                            <a href="/" title="@name@">
                                <img src="@logo@" alt="" class="img-responsive" />
                            </a>
                        </div>
                    </div>
                    <!-- Logo Starts -->
                    <!-- Header Links Starts -->
                    <div class="col-sm-12 col-xs-12 col-md-7 text-center header-color">
                        <div class="btn-group header-valuta-disp-wrapper">
                            <div><a class="header-phone" href="tel:@telNumMobile@">@telNumMobile@</a> <br> <a class="header-phone" href="tel:@telNum2@">@telNum2@</a> </div>
                        </div>
                        <div class="returncall-wrapper header-links header-color">
                            @returncall@
                        </div>
                    </div>
                    <!-- Header Links Ends -->

                    <!-- Shopping Cart Starts -->
                    <div class="col-md-3 col-lg-3  visible-md hidden-sm hidden-xs visible-lg">
                        <div id="cart" class="btn-group pull-right header-color @hideCatalog@">
                            <a id="cartlink" type="button" data-toggle="dropdown" class="btn btn-block btn-lg dropdown-toggle" data-trigger="hover" data-container="body"  data-placement="bottom" data-html="true" data-url="/order/" href="/order/" data-content='@visualcart@'>
                                <span class="cart-title">{Корзина}</span>
                                <i class="fa fa-shopping-cart"></i>
                                <span id="cart-total"><span><span id="num">@num@</span>{шт.}</span></span>
                                <i class="fa fa-caret-down"></i>
                            </a>
                            @visualcart@
                        </div>
                    </div>
                    <!-- Shopping Cart Ends -->
                    <!-- Row Ends -->
                </div>
                <!-- Nested Container Ends -->
            </div>
            <!-- Header Top Ends -->
            <!-- Main Menu Starts -->
            <nav id="main-menu" class="navbar">
                <div class="container">
                    <!-- Nav Header Starts -->
                    <div class="navbar-header">
                        <a class="navbar-brand visible-xs pull-right" href="tel:@telNumMobile@">
                            <span class="glyphicon glyphicon-phone"></span> @telNumMobile@
                        </a>
                        <button type="button" class="btn btn-navbar navbar-toggle main-menu-button" data-toggle="collapse" data-target=".navbar-cat-collapse">
                            <span class="sr-only">Toggle Navigation</span>
                            <i class="fa fa-bars"></i>
                        </button>
                    </div>
                    <!-- Nav Header Ends -->
                    <!-- Navbar Cat collapse Starts -->
                    <div class="collapse navbar-collapse navbar-cat-collapse">
                        <div class=" header-menu-wrapper col-md-9">
                            <div class="row">
                                <ul class="nav navbar-nav main-navbar-top">
                                    <li class="main-navbar-top-catalog @hideSite@">
                                        <a href="#" id="nav-catalog-dropdown-link" class="nav-catalog-dropdown-link" aria-expanded="false">{Весь каталог}
                                        </a>
                                        <ul class="main-navbar-list-catalog-wrapper fadeIn animated">
                                            @leftCatal@
                                        </ul>
                                    </li>
                                    @topBrands@
                                    @topcatMenu@
                                    @topMenu@
                                </ul>
                            </div></div>

                        <form id="search_form" class="navbar-form navbar-right hidden-sm hidden-xs" action="/search/" role="search" method="post">
                            <div class="input-group">
                                <input class="form-control input-lg" name="words" maxlength="50" id="search"  placeholder="{Искать}..." required="" type="search" data-trigger="manual" data-container="body" data-toggle="popover" data-placement="bottom" data-html="true"  data-content="">
                                <span class="input-group-btn">
                                    <button class="btn btn-lg" type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </span>
                            </div>
                        </form>

                    </div>
                    <!-- Navbar Cat collapse Ends -->
                </div>
            </nav>
            <!-- Main Menu Ends -->
        </header>
        <!-- Header Section Ends -->

        <!-- Breadcrumb Starts -->
        <div class="breadcrumb-wrap">
            <div class="container">
                <!-- Breadcrumb Starts -->
                <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
                    @breadCrumbs@
                </ol>
                <!-- Breadcrumb Ends -->
            </div>
        </div>
        <!-- Main Container Starts -->
        <div class="main-container container shop-page">
            <!-- Nested Row Starts -->
            <div class="row">
                <!-- Sidebar Starts -->
                <div class="col-xs-12 col-md-3 sidebar-right" id="sidebar-right">
                    <div class="order-page-sidebar-user-block hidden-xs hidden-sm">
                        <h5 class="user-title">{Мой кабинет}</h5>
                        <ul class="user-list">
                            <li><a href="/users/">@UsersLogin@</a></li>
                            <li class="@hideCatalog@"><a href="/users/order.html">{Отследить заказ}</a></li>
                            <li class="@hideCatalog@"><a href="/users/notice.html">{Уведомления о товарах}</a></li>
                            <li><a href="/users/message.html">{Связь с менеджерами}</a></li>
                            @php if($_SESSION['UsersId']) echo '<li><a href="?logout=true">{Выйти}</a></li>'; php@
                        </ul>
                    </div>
                    <!-- Categories Links Starts -->
                    <div class="side-heading hidden-xs hidden-sm @php if($GLOBALS['PHPShopNav']->objNav['path']!="shop") echo "hide"; php@">{Категории}</div>
                    <ul class="list-group sidebar-nav hidden-xs hidden-sm @php if($GLOBALS['PHPShopNav']->objNav['path']!="shop") echo "hide"; php@">
                        @leftCatal@
                </ul>
                <!-- Categories Links Ends -->
                
                <!-- Фасетный фильтр -->
                <div class="hide panel panel-default" id="faset-filter">
                    <div class="faset-filter-name filter-title btn btn-default">{Фильтры}</div>
                    <div class="panel-body list-group faset-filter-block-wrapper filter-body-fix">

                        <div id="faset-filter-body">{Загрузка}...</div>

                        <div id="price-filter-body" class="@hideCatalog@">
                            <h4>{Цена}</h4>
                            <form method="get" id="price-filter-form">
                                <div class="row">
                                    <div class="col-md-6 col-xs-6" id="price-filter-val-min">
                                        {от} <input type="text" class="form-control input-sm" name="min" value="@price_min@" > 
                                    </div>
                                    <div class="col-md-6 col-xs-6" id="price-filter-val-max">
                                        {до} <input type="text" class="form-control input-sm" name="max" value="@price_max@"> 
                                    </div>
                                </div>
                            </form>

                            <div id="slider-range" class="slider-range"></div>

                        </div>
                        <a href="?" id="faset-filter-reset" class="border-btn" >{Сбросить фильтр}</a>
                    </div>
                </div>

                <!--/ Фасетный фильтр 
                <!-- jQuery -->
                <script src="@pathTemplate@/js/jquery-1.11.0.min.js"></script>
                <script src="@pathMin@java/jqfunc.js"></script>

                <div class="sidebar-fix-block  hidden-xs hidden-sm">
                    <div class="side-heading"><a href="/page/">{Блог}</a></div>
                    <div class="list-group sidebar-nav">
                        @pageCatal@
                    </div>
                    @banersDisp@
                    @leftMenu@
                    <div class="panel panel-default  hidden-xs  hidden-sm @php __hide('productlist_list'); php@ @hideSite@">
                        <div class="panel-heading">
                            <div class="panel-title">@productlist_title@</div>
                        </div>
                        <div class="panel-body">
                            <div id="productlist">
                                <table>@productlist_list@</table>
                            </div>

                        </div>
                    </div>
                    
                    <div class="panel panel-default  hidden-xs  hidden-sm @php __hide('productsimilar_list'); php@ @hideSite@">
                    <div class="panel-heading">
                        <div class="panel-title">@productsimilar_title@</div>
                    </div>
                    <div class="panel-body">
                        <div id="productlist">
                            <table>@productsimilar_list@</table>
                        </div>


                    </div>
                </div>

                    <div class="panel panel-default  hidden-xs  hidden-sm @php __hide('productlastview'); php@ @hideSite@">
                        <div class="panel-heading">
                            <div class="panel-title">{Просмотренные товары}</div>
                        </div>
                        <div class="panel-body">
                            @productlastview@
                        </div>
                    </div>
                </div>
            </div>
            <!-- Sidebar Ends -->

            <!-- Primary Content Starts -->
            <div class="col-md-9 col-xs-12 middle-content-block">
                @DispShop@ 

                <div class="col-xs-12">
                    <div class="banner-block">
                        @banersDispHorizontal@
                    </div>
                </div>
            </div>
            <!-- Primary Content Ends -->
        </div>
        <!-- Nested Row Ends -->
    </div>

    <!-- Main Container Ends -->

    <!-- toTop -->
    <div class="visible-lg visible-md">
        <a href="#" id="toTop"><span id="toTopHover"></span></a>
    </div>
    <!--/ toTop -->


    @editor@

    <!-- Footer Section Starts -->
    <footer id="footer-area">
        <!-- Footer Links Starts -->
        <div class="footer-links">
            <!-- Container Starts -->
            <div class="container">
                <!-- Information Links Starts -->
                <div class="col-md-3 col-sm-4 col-xs-12">
                    <h5>{Информация}</h5>
                    <ul>
                        @bottomMenu@
                    </ul>
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
                        <li><a href="?logout=true">{Выйти}</a></li>'; else echo '<li><a href="#" data-toggle="modal" data-target="#userModal">{Войти}</a></li>'; php@
                    </ul>
                </div>
                <!-- My Account Links Ends -->
                <!-- Customer Service Links Starts -->
                <div class="col-md-3 col-sm-4 col-xs-12">
                    <h5>{Навигация}</h5>
                    <ul>
                        <li class="@hideCaralog@"><a href="/price/" title="{Прайс-лист}">{Прайс-лист}</a></li>
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
                        <li class="footer-email">@adminMail@</li>                              
                    </ul>
                    <h4 class="lead">
                        <span>@telNum@<br>
                            @telNum2@<br>
                            @workingTime@
                        </span>
                    </h4>

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
                <p itemscope itemtype="http://schema.org/Organization">&copy; <span itemprop="name">@company@</span> @year@, {Тел}: <span itemprop="telephone">@telNum@</span>, <span itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">{Адрес}: <span itemprop="streetAddress">@streetAddress@</span></span><span itemprop="email" class="hide">@adminMail@</span></p>
            </div>
            <!-- Container Ends -->
        </div>
        <!-- Copyright Area Ends -->
    </footer>
    <!-- Footer Section Ends -->


    <!-- Fixed mobile bar -->
    <div class="bar-padding-fix visible-xs"> </div>
    <nav class="navbar navbar-default navbar-fixed-bottom bar bar-tab visible-xs visible-sm">
        <a class="tab-item active" href="/">

            <span class="tab-label">{Домой}</span>
        </a>
        <a class="tab-item @user_active@" @user_link@ data-target="#userModal">
            <span class="tab-label">{Кабинет}</span>
        </a>
        <a class="tab-item @cart_active@ @hideCatalog@" href="/order/" id="bar-cart">
            <span class="badge badge-positive" id="mobilnum">@cart_active_num@</span>
            <span class="tab-label">{Корзина}</span>
        </a>
        <a class="tab-item" href="#" data-toggle="modal" data-target="#searchModal">

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

    <!-- Модальное окно returncall-->
    <div class="modal fade bs-example-modal-sm return-call" id="returnCallModal" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">{Обратный звонок}</h4>
                </div>
                <form method="post" name="user_forma" action="@ShopDir@/returncall/">
                    <div class="modal-body">

                        <div class="form-group">

                            <input type="text" name="returncall_mod_name" class="form-control" placeholder="{Имя}" required="">
                        </div>
                        <div class="form-group">

                            <input type="text" name="returncall_mod_tel" class="form-control phone" placeholder="{Телефон}" required="">
                        </div>
                        <div class="form-group">

                            <input class="form-control" type="text" placeholder="{Время звонка}" name="returncall_mod_time_start">
                        </div>
                        <div class="form-group">

                            <textarea class="form-control" name="returncall_mod_message" placeholder="{Сообщение}"></textarea>
                        </div>
                        @returncall_captcha@
                        <p class="small"><label><input type="checkbox" value="on" name="rule" class="req" >  {Я согласен} <a href="/page/soglasie_na_obrabotku_personalnyh_dannyh.html">{на обработку моих персональных данных}</a></label></p>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="returncall_mod_send" value="1">

                        <button type="submit" class="btn btn-main">{Заказать звонок}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно мобильного поиска -->
    <div class="modal fade bs-example-modal-sm" id="searchModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">{Поиск}</h4>
                </div>
                <div class="modal-body">
                    <form  action="/search/" role="search" method="post">
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
     <link href="@pathTemplateMin@css/bar.css" rel="stylesheet">
    <link href="@pathTemplate@css/swiper.min.css" rel="stylesheet">
    <link href="@pathTemplate@css/jquery.bxslider.css" rel="stylesheet">
    <link href="@pathTemplate@css/jquery-ui.min.css" rel="stylesheet">
    <link href="@pathTemplate@css/bootstrap-select.min.css" rel="stylesheet">
   
    <link href="@pathTemplate@css/suggestions.min.css" rel="stylesheet">
    <link href="@pathTemplate@css/font-awesome.min.css" rel="stylesheet">
    <script src="@pathTemplate@/js/bootstrap.min.js"></script>
    <script src="@pathTemplate@/js/swiper.js"></script>
    <script src="@pathTemplate@/js/diggi.js"></script>
    <script src="@pathTemplate@/js/bootstrap-select.min.js"></script>
    <script src="@pathTemplate@/js/jquery.lazyloadxt.min.js"></script>
    <script src="@pathTemplate@/js/phpshop.js"></script>
    <script src="phpshop/locale/@php echo $_SESSION['lang']; php@/template.js"></script>
    <script src="@pathTemplate@/js/flipclock.min.js"></script>
    <script src="@pathTemplate@/js/jquery-ui.min.js"></script>
    <script src="@pathTemplate@/js/jquery.ui.touch-punch.min.js"></script>
    <script src="@pathTemplate@/js/jquery.bxslider.min.js"></script>
    <script src="@pathTemplate@/js/jquery.cookie.js"></script>
    <script src="@pathTemplate@/js/jquery.waypoints.min.js"></script>
    <script src="@pathTemplate@/js/inview.min.js"></script>
    <script src="@pathTemplate@/js/jquery.maskedinput.min.js"></script>
    <script src="@pathTemplate@/js/jquery.suggestions.min.js"></script>
    <script src="@pathTemplate@/js/jquery.ui.touch-punch.min.js"></script>
    @visualcart_lib@