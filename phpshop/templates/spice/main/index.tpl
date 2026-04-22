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
        <link rel="preload" href="@pathTemplateMin@css/@spice_theme@.css" as="style">
        <link rel="preload" href="@pathTemplateMin@css/responsive.css" as="style">
        <link rel="preload" href="@pathTemplate@css/font-awesome.min.css"  as="font" type="font/woff2" crossorigin>

        <!-- Bootstrap -->
        <link href="@pathTemplate@css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body id="body" data-dir="@ShopDir@" data-path="@php echo $GLOBALS['PHPShopNav']->objNav['path']; php@" data-id="@php echo $GLOBALS['PHPShopNav']->objNav['id']; php@" data-token="@dadataToken@">

        <!-- Template -->
        <link href="@pageCss@" rel="stylesheet">

        <!-- Theme -->
        <link id="bootstrap_theme" data-name="@php echo $_SESSION['skin']; php@" href="@pathTemplateMin@css/@spice_theme@.css" rel="stylesheet">


        <!-- Header Section Starts -->
        <header id="header-area">
            <!-- Header Top Starts -->
            <div class="header-top">
                <!-- Стикер-полоска -->
                <div class="@php __hide('sticker_top'); php@">
                    <div class="top-banner @php __hide('sticker_close','cookie'); php@">
                        <div class="sticker-text">@sticker_top@</div>
                        <span class="close sticker-close">x</span>
                    </div>
                </div>
                <!-- /Стикер-полоска -->


                <!-- Nested Container Starts -->
                <div class="container">

                    <!-- Row Starts -->
                    <div class="row">
                        <!-- Header Links Starts -->
                        <div class="col-sm-12 col-xs-12 col-md-8">
                            <div class="header-links">
                                <ul class="nav navbar-nav pull-left header-color">
                                    <li>
                                        <a class="hidden-xs hidden-sm link" href="/">
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
                        <!-- Header Links Ends -->
                        <!-- Currency & Languages Starts -->
                        <div class="col-sm-4 col-md-4 hidden-xs hidden-sm">
                            <div class="pull-right">                           
                                <!-- Currency Starts -->
                                <div class="btn-group header-valuta-disp-wrapper header-color">
                                    <div><a href="tel:@telNumMobile@">@telNumMobile@</a> <a href="tel:@telNum2@">@telNum2@</a></div>
                                </div>
                                <!-- Currency Ends -->                      
                            </div>
                        </div>
                        <!-- Currency & Languages Ends -->
                    </div>
                    <!-- Row Ends -->
                </div>
                <!-- Nested Container Ends -->
            </div>
            <!-- Header Top Ends -->

            <div class="container">
                <!-- Main Header Starts -->
                <div class="main-header">
                    <!-- Row Starts -->
                    <div class="row">
                        <div class="col-md-12 hidden-xs hidden-sm">
                            <div class="returncall-wrapper returncall-desctop header-links pull-right header-color">
                                @returncall@
                                @workingTime@
                            </div>
                        </div>
                        <!-- Logo Starts -->
                        <div class="col-md-6 wrapper-fix">
                            <div id="logo">
                                <a href="/" title="@name@">
                                    <img src="@logo@" alt="" class="img-responsive" /></a>
                            </div>
                            <div class="returncall-wrapper hidden-md hidden-lg header-color">
                                @returncall@
                            </div>
                        </div>
                        <!-- Logo Starts -->

                        @php
                        if(empty(PHPShopParser::get('hideCatalog')))
                        $GLOBALS['search_col']=3;
                        else $GLOBALS['search_col']=6;
                        php@

                        <!-- Search Starts -->
                        <div class="col-md-@php echo $GLOBALS['search_col']; php@ hidden-xs">
                            <form id="search_form" action="/search/" role="search" method="get" class="header-color">
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
                        <!-- Search Ends -->                        
                        <!-- Shopping Cart Starts -->
                        <div class="col-md-3 hidden-xs hidden-sm @hideCatalog@">
                            <div id="cart" class="btn-group btn-block header-color ">
                                <a href="/order/" id="cartlink"   class="btn btn-block btn-lg " data-trigger="click" data-container="body"  data-placement="bottom" data-html="true" data-url="/order/" data-content='@visualcart@'>
                                    <i class="fa fa-shopping-cart"></i>
                                    <span>{Корзина}:</span> 
                                    <span id="cart-total"><span><span id="num">@num@</span>{шт.} - </span><span id="sum"> @sum@</span> <span class="rubznak">@productValutaName@</span></span>

                                </a>
                                @visualcart@
                            </div>
                        </div>
                        <!-- Shopping Cart Ends -->
                    </div>
                    <!-- Row Ends -->
                </div>
                <!-- Main Header Ends -->
                <!-- Main Menu Starts -->
                <nav id="main-menu" class="navbar">
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
                        <div class="row">
                            <ul class="nav navbar-nav main-navbar-top">
                                <li class="main-navbar-top-catalog @hideSite@">
                                    <a href="javascript:void(0);" id="nav-catalog-dropdown-link" class="nav-catalog-dropdown-link" aria-expanded="false">{Каталог}
                                    </a>
                                    <ul class="main-navbar-list-catalog-wrapper fadeIn animated">
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
                    <!-- Navbar Cat collapse Ends -->
                </nav>
                <!-- Main Menu Ends -->
            </div>
        </header>
        <!-- Header Section Ends -->

        <!-- Slider Section Starts -->
        <!-- Nested Container Starts -->
        <!-- Carousel Starts -->
        <div class="container">
            <div class="slider">
                <div class="row">
                    @imageSlider@
                </div>
            </div>
        </div>
        <div class="clearfix"></div>

        <!-- Slider Section Ends -->
        <div class="container @hideSite@">
            <div class="catalog-block">@leftCatalTable@</div>
        </div>


        <!-- Main Container Starts -->
        <div class="main-container container main-page">
            <!-- Featured Products Starts -->
            <div class="page-header visible-lg visible-md product-head">
                <h2>@mainContentTitle@</h2>
            </div>
            <div>@mainContent@</div>
            <!-- Banners Starts -->
            <div class="top-col-banners">@banersDispHorizontal@</div>

            <!-- Banners Ends -->    

            <!-- Latest Products Starts -->
            <section class="products-list @php __hide('specMain'); php@ @hideSite@">         
                <!-- Heading Starts -->
                <h2 class="product-head page-header"><a href="/spec/" title="{Все спецпредложения}">{Спецпредложения}</a></h2>
                <!-- Heading Ends -->
                <!-- Products Row Starts -->
                <div class="row new-product-list">
                    @specMain@
                </div>
                <!-- Products Row Ends -->
            </section>
            <!-- Latest Products Ends -->


        </div>
        <!-- Main Container Ends -->


        <!-- toTop -->
        <div class="visible-lg visible-md">
            <a href="#" id="toTop"><span id="toTopHover"></span>{Наверх}</a>
        </div>
        <!--/ toTop -->

        <!-- jQuery -->
        <script src="@pathTemplate@/js/jquery-1.11.0.min.js"></script>
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
                            <li><a href="?logout=true">{Выйти}</a></li>';
                            else echo '<li><a href="#" data-toggle="modal" data-target="#userModal">{Войти}</a></li>';
                            php@
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
                            <li class="footer-email"><a href="mailto:@adminMail@">@adminMail@</a></li>                              
                        </ul>
                        <h4 class="lead">
                            @telNum@
                            <br>@telNum2@
                            <br>@workingTime@
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

        <!-- Согласие на использование cookie  -->
        <div class="cookie-message hide"><p></p><a href="#" class="btn btn-default btn-sm">Ok</a></div>

        <!-- JQuery Plugins  -->

        <script src="@pathMin@java/jqfunc.js"></script>
        <link href="@pathTemplateMin@css/bar.css" rel="stylesheet">
        <link href="@pathTemplate@css/swiper.min.css" rel="stylesheet">
        <link href="@pathTemplateMin@css/responsive.css" rel="stylesheet">
        <link href="@pathTemplate@css/jquery.bxslider.css" rel="stylesheet">
        <link href="@pathTemplate@css/jquery-ui.min.css" rel="stylesheet">
        <link href="@pathTemplate@css/bootstrap-select.min.css" rel="stylesheet">
        <link href="@pathTemplate@css/suggestions.min.css" rel="stylesheet">
        <link href="@pathTemplate@css/font-awesome.min.css" rel="stylesheet">
        <script src="@pathTemplate@/js/bootstrap.min.js"></script>
        <script src="@pathTemplate@/js/swiper.js"></script>
        <script src="@pathTemplate@/js/spice.js"></script>
        <script src="@pathTemplate@/js/bootstrap-select.min.js"></script>
        <script src="@pathTemplate@/js/jquery.lazyloadxt.min.js"></script>
        <script src="@pathTemplate@/js/phpshop.js"></script>
        <script src="phpshop/locale/@php echo $_SESSION['lang']; php@/template.js"></script>
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