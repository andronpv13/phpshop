<!DOCTYPE html>
<html lang="@lang@">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta charset="@charset@">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>@pageTitl@</title>
        <meta name="description" content="@pageDesc@">
        <meta name="keywords" content="@pageKeyw@">
        <meta name="copyright" content="@pageReg@">
        @verification@

        <link rel="apple-touch-icon" href="@icon@">
        <link rel="icon" href="@icon@" type="image/x-icon">
        <link rel="mask-icon" href="@icon@">
        <link rel="shortcut icon" href="@icon@">

        <!-- OpenGraph -->
        <meta property="og:title" content="@ogTitle@">
        <meta property="og:image" content="http://@serverName@@ogImage@">
        <meta property="og:url" content="http://@ogUrl@">
        <meta property="og:type" content="website">
        <meta property="og:description" content="@ogDescription@">

        <!-- Preload -->
        <link rel="preload" href="@pathTemplate@/fonts/rouble.woff" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" as="style">
        <link rel="preload" href="@pathTemplateMin@/style.css" as="style">
        <link rel="preload" href="@pathTemplate@/assets/vendor/fontawesome/css/all.min.css" as="style">
        <link rel="preload" href="@pathTemplate@/assets/vendor/hs-mega-menu/dist/hs-mega-menu.min.css" as="style">
        <link rel="preload" href="@pathTemplate@/assets/vendor/slick-carousel/slick/slick.css" as="style">
        <link rel="preload" href="@pathTemplate@css/suggestions.min.css" as="style">

        <!-- Bootstrap -->
        <link id="bootstrap_theme" data-name="@php echo $_SESSION['skin']; php@" href="@pathTemplate@css/@auto_theme@.css" rel="stylesheet">
    </head>
    <body id="body" data-dir="@ShopDir@" data-path="@php echo $GLOBALS['PHPShopNav']->objNav['path']; php@" data-id="@php echo $GLOBALS['PHPShopNav']->objNav['id']; php@" data-subpath="@php echo $GLOBALS['PHPShopNav']->objNav['name']; php@" data-token="@dadataToken@">

        <!-- Font -->
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">

        <!-- CSS Implementing Plugins -->
        <link rel="stylesheet" href="@pathTemplate@/assets/vendor/fontawesome/css/all.min.css">
        <link rel="stylesheet" href="@pathTemplate@/assets/vendor/hs-mega-menu/dist/hs-mega-menu.min.css">
        <link rel="stylesheet" href="@pathTemplate@/assets/vendor/slick-carousel/slick/slick.css">
        <link rel="stylesheet" href="@pathTemplate@css/suggestions.min.css">

        <!-- Core CSS -->
        <link rel="stylesheet" href="@pathTemplateMin@/style.css" type="text/css">

        <!-- Search Content -->
        <div id="searchSlideDown" class="hs-unfold-content dropdown-unfold search-slide-down">
            <form action="/search/" method="get">
                <!-- Input Group -->
                <div class="input-group input-group-borderless search-slide-down-input bg-white rounded mb-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                    </div>
                    <input id="searchSlideDownControl" name="words" type="search" class="form-control" placeholder="{Я ищу...}" required="" aria-label="Search Front">
                    <div class="input-group-append">
                        <a class="js-hs-search-unfold-invoker input-group-text" href="javascript:;"
                           data-hs-unfold-options='{
                           "target": "#searchSlideDown",
                           "type": "css-animation",
                           "animationIn": "fadeIn search-slide-down-show",
                           "animationOut": "fadeOutUp",
                           "cssAnimatedClass": null,
                           "hasOverlay": true,
                           "overlayClass": ".search-slide-down-bg-overlay",
                           "overlayStyles": {
                           "background": "rgba(55, 125, 255, .1)"
                           },
                           "duration": 800,
                           "hideOnScroll": false
                           }'>
                            <i class="fas fa-times" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
                <!-- End Input Group -->

                <!-- Suggestions Content -->
                <div class="rounded bg-white search-slide-down-suggestions py-3 px-5">
                    <div class="container mb-0" id="searchSlideDownContent">
                    </div>
                </div>
                <!-- End Suggestions Content -->
            </form>
        </div>
        <!-- End Search Content -->

        <!-- ========== HEADER ========== -->
        <header id="header" class="header left-aligned-navbar">

            <!-- Стикер-полоска -->
            <div class="alert alert-soft-primary text-center alert-dismissible font-size-1 fade show @php __hide('sticker_top');__hide('sticker_close','cookie'); php@" role="alert">@sticker_top@<button type="button" class="close sticker-close" data-dismiss="alert" aria-label="Close"><svg aria-hidden="true" class="mb-0" width="14" height="14" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M11.5,9.5l5-5c0.2-0.2,0.2-0.6-0.1-0.9l-1-1c-0.3-0.3-0.7-0.3-0.9-0.1l-5,5l-5-5C4.3,2.3,3.9,2.4,3.6,2.6l-1,1 C2.4,3.9,2.3,4.3,2.5,4.5l5,5l-5,5c-0.2,0.2-0.2,0.6,0.1,0.9l1,1c0.3,0.3,0.7,0.3,0.9,0.1l5-5l5,5c0.2,0.2,0.6,0.2,0.9-0.1l1-1 c0.3-0.3,0.3-0.7,0.1-0.9L11.5,9.5z"/></svg></button>
            </div>
            <!-- Конец Стикер-полоска -->


            <div class="header-section">

                <!-- Topbar -->
                <div class="container header-hide-content pt-2">
                    <div class="d-flex align-items-center">
                        <p class="small text-muted mb-0 d-none d-sm-flex">@name@</p>
                        <!-- Search -->
                        <li class="list-inline-item d-sm-block d-md-block d-lg-none ">
                            <div class="hs-unfold">

                                <a class="js-hs-search-unfold-invoker btn btn-xs btn-icon btn-ghost-secondary search-slide-down-trigger" href="javascript:;" role="button"
                                   data-hs-unfold-options='{
                                   "target": "#searchSlideDown",
                                   "type": "css-animation",
                                   "animationIn": "fadeIn search-slide-down-show",
                                   "animationOut": "fadeOutUp",
                                   "cssAnimatedClass": null,
                                   "hasOverlay": true,
                                   "overlayClass": ".search-slide-down-bg-overlay",
                                   "overlayStyles": {
                                   "background": "rgba(55, 125, 255, .1)"
                                   },
                                   "duration": 800,
                                   "hideOnScroll": false
                                   }'>
                                    <i class="fas fa-search search-slide-down-trigger-icon"></i>
                                </a>

                            </div>
                        </li>
                        <!-- End Search -->

                        @usersDisp@

                        </li>
                        <!-- End Button -->

                        <div class="ml-auto">


                            <!-- Links -->
                            <div class="nav nav-sm nav-y-0  ml-sm-auto">
                                @returncall@
                                <p class="nav-link small text-muted mb-0"><a href="tel:@telNumMobile@">@telNum@</a></p>
                                <p class="nav-link small text-muted mb-0 d-none d-sm-flex"><a href="tel:@telNum2@">@telNum2@</a></p>
                            </div>
                            <!-- End Links -->
                        </div>

                        <ul class="list-inline ml-2 mb-0">


                            <!-- Button -->
                            <!-- Shopping Cart -->
                            <li class="navbar-nav-last-item d-none d-lg-block d-sm-none d-md-none ">
                                <div class="hs-unfold @hideCatalog@">
                                    <a class="js-hs-unfold-invoker btn btn-icon" href="/order/"
                                       data-hs-unfold-options='{
                                       "target": "#shoppingCartDropdown",
                                       "type": "css-animation",
                                       "event": "hover",
                                       "hideOnScroll": "true"
                                       }'>
                                        <i class="fas fa-shopping-cart"></i>
                                        <sup class="avatar-status avatar-primary cartnum @php __hide('num'); php@">@num@</sup>
                                    </a>

                                    <div id="shoppingCartDropdown" class="hs-unfold-content dropdown-menu dropdown-menu-right dropdown-card" style="min-width: 275px;">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <span class="cartnum">@num@</span>
                                                <span class="visible-lg-inline">{товаров} {на} </span><span id="sum" class="">@sum@</span> <span
                                                    class="rubznak">@productValutaName@</span></a>
                                            </div>

                                            @visualcart@

                                            <div class="card-footer text-center @php __hide('num'); php@" id="order">
                                                <a class="btn btn-primary  transition-3d-hover px-5"  href="/order/">{Оформить заказ}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Shopping Cart -->

                                <a class="btn btn-icon @hideSite@" href="/users/wishlist.html" data-toggle="tooltip" data-placement="top" title="" tabindex="0" data-original-title="{Избранное}">
                                    <i class="fas fa-heart"></i>
                                    <sup class="avatar-status avatar-primary wishlistcount @php __hide('wishlistCount'); php@">@wishlistCount@</sup>
                                </a>
                                <a class="btn btn-icon @hideSite@" href="/compare/" data-toggle="tooltip" data-placement="top" title="" tabindex="0" data-original-title="{Сравнить}">
                                    <i class="fas fa-balance-scale"></i></i>
                                    <sup class="avatar-status avatar-primary numcompare @php __hide('numcompare'); php@">@numcompare@</sup>
                                </a>


                        </ul>
                    </div>
                </div>
                <!-- End Topbar -->

                <div id="logoAndNav" class="container">
                    <!-- Nav -->
                    <nav class="js-mega-menu navbar navbar-expand-lg">

                        <!-- Logo -->
                        <a class="navbar-brand @php __hide('logo'); php@" href="/" aria-label="" title="{Домой}">
                            <img src="@logo@" alt="">
                        </a>
                        <!-- End Logo -->

                        <!-- Shopping Cart -->
                        <a class="navbar-toggler btn btn-icon btn-sm rounded-circle @hideCatalog@" href="/order/">
                            <i class="fas fa-shopping-cart"></i>
                            <sup class="avatar-status avatar-primary cartnum @php __hide('num'); php@">@num@</sup>
                        </a>
                        <!-- End Shopping Cart -->

                        <a class="navbar-toggler btn btn-icon btn-sm rounded-circle @hideSite@" href="/users/wishlist.html" data-toggle="tooltip" data-placement="top" title="" tabindex="0" data-original-title="{Избранное}">
                            <i class="fas fa-heart"></i>
                            <sup class="avatar-status avatar-primary wishlistcount @php __hide('wishlistCount'); php@">@wishlistCount@</sup>
                        </a>
                        <a class="navbar-toggler btn btn-icon btn-sm rounded-circle @hideSite@" href="/compare/" data-toggle="tooltip" data-placement="top" title="" tabindex="0" data-original-title="{Сравнить}">
                            <i class="fas fa-balance-scale"></i></i>
                            <sup class="avatar-status avatar-primary numcompare @php __hide('numcompare'); php@">@numcompare@</sup>
                        </a>

                        <!-- Responsive Toggle Button -->
                        <button type="button" class="navbar-toggler btn btn-icon btn-sm"
                                aria-label="Toggle navigation"
                                aria-expanded="false"
                                aria-controls="navBar"
                                data-toggle="collapse"
                                data-target="#navBar">
                            <span class="navbar-toggler-default">
                                <svg width="14" height="14" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                                <path fill="currentColor" d="M17.4,6.2H0.6C0.3,6.2,0,5.9,0,5.5V4.1c0-0.4,0.3-0.7,0.6-0.7h16.9c0.3,0,0.6,0.3,0.6,0.7v1.4C18,5.9,17.7,6.2,17.4,6.2z M17.4,14.1H0.6c-0.3,0-0.6-0.3-0.6-0.7V12c0-0.4,0.3-0.7,0.6-0.7h16.9c0.3,0,0.6,0.3,0.6,0.7v1.4C18,13.7,17.7,14.1,17.4,14.1z"/>
                                </svg>
                            </span>
                            <span class="navbar-toggler-toggled">
                                <svg width="14" height="14" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                                <path fill="currentColor" d="M11.5,9.5l5-5c0.2-0.2,0.2-0.6-0.1-0.9l-1-1c-0.3-0.3-0.7-0.3-0.9-0.1l-5,5l-5-5C4.3,2.3,3.9,2.4,3.6,2.6l-1,1 C2.4,3.9,2.3,4.3,2.5,4.5l5,5l-5,5c-0.2,0.2-0.2,0.6,0.1,0.9l1,1c0.3,0.3,0.7,0.3,0.9,0.1l5-5l5,5c0.2,0.2,0.6,0.2,0.9-0.1l1-1 c0.3-0.3,0.3-0.7,0.1-0.9L11.5,9.5z"/>
                                </svg>
                            </span>
                        </button>
                        <!-- End Responsive Toggle Button -->

                        <!-- Navigation -->
                        <div id="navBar" class="collapse navbar-collapse">
                            <div class="navbar-body header-abs-top-inner">
                                <ul class="navbar-nav">
                                    @php

                                    if(PHPShopParser::get('banersDispMenu')!="")
                                    $GLOBALS['catalogmenu_col'] = 6;
                                    else $GLOBALS['catalogmenu_col'] = 12;

                                    php@

                                    <!-- Catalog -->
                                    <li class="hs-has-mega-menu navbar-nav-item @hideSite@">

                                        <a id="basicMegaMenu" class="hs-mega-menu-invoker nav-link nav-link-toggle" aria-haspopup="true" aria-expanded="false">{Каталог}</a>

                                        <!-- Nav Item - Mega Menu -->
                                        <div class="hs-mega-menu w-100 dropdown-menu" aria-labelledby="basicMegaMenu">
                                            <div class="row no-gutters">
                                                <div class="col-lg-6 @php __hide('banersDispMenu'); php@">

                                                    <!-- Banner Image -->
                                                    @banersDispMenu@
                                                    <!-- End Banner Image -->

                                                </div>
                                                <div class="col-lg-@php echo $GLOBALS['catalogmenu_col']; php@">
                                                    <div class="row mega-menu-body">
                                                        @topCatal@
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- End Nav Item - Mega Menu -->
                                    </li>
                                    <!-- End Catalog -->

                                    <!-- Brand -->
                                    <li class="hs-has-sub-menu navbar-nav-item @php __hide('topBrands'); php@ @hideSite@">
                                        <a id="blogMegaMenu" class="hs-mega-menu-invoker nav-link nav-link-toggle " href="javascript:;" aria-haspopup="true" aria-expanded="false" aria-labelledby="blogSubMenu">{Бренды}</a>

                                        <div id="blogSubMenu" class="hs-sub-menu dropdown-menu" aria-labelledby="blogMegaMenu" style="min-width: 230px;">
                                            @topBrands@
                                        </div>

                                    </li>
                                    <!-- End Brand -->

                                    <!-- Catalog  Menu-->
                                    @topcatMenu@
                                    <!-- End Catalog  Menu-->

                                    <!-- Menu -->
                                    @php
                                    if(empty(PHPShopParser::get('hideSite')) and !empty(PHPShopParser::get('topMenu'))){
                                    $GLOBALS['search_placeholder']='Артикул или наименование';
                                    echo '<li class="hs-has-sub-menu navbar-nav-item">
                                        <a id="blogMegaMenu" class="hs-mega-menu-invoker nav-link nav-link-toggle " href="javascript:;" aria-haspopup="true" aria-expanded="false" aria-labelledby="blogSubMenu">{Навигация}</a>

                                        <div id="blogSubMenu" class="hs-sub-menu dropdown-menu" aria-labelledby="blogMegaMenu" style="min-width: 230px;">
                                            @topMenu@
                                        </div>

                                    </li>';
                                    }
                                    else {
                                    $GLOBALS['search_placeholder']='Я ищу...';
                                    echo '<li class="navbar-nav-item">@topMenu@</li>';
                                    }
                                    php@
                                    <!-- End Menu-->

                                    <div class="col-md-6 navbar-nav-last-item d-none d-lg-block d-sm-none d-md-none">
                                        <form class="input-group" action="/search/" method="get">
                                            <input type="search" name="words" class="form-control form-control-sm" placeholder="{@php echo $GLOBALS['search_placeholder'];  php@}" aria-label="{Артикул, наименование}">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search search-slide-down-trigger-icon"></i></button>
                                            </div>
                                        </form>
                                    </div>

                                </ul>
                            </div>
                        </div>
                        <!-- End Navigation -->
                    </nav>
                    <!-- End Nav -->
                </div>
            </div>
        </header>
        <!-- ========== END HEADER ========== -->

        <!-- ========== MAIN CONTENT ========== -->
        <main id="content" role="main">
            <!-- Images Carousel Section -->
            <div class="fluid @php __hide('imageSlider'); php@">
                <div class="js-slick-carousel slick slick-equal-height"
                     data-hs-slick-carousel-options='{
                     "autoplay": true,
                     "autoplaySpeed": 5000,

                     "prevArrow": "<span class=\"fas fa-arrow-left slick-arrow slick-arrow-soft-white slick-arrow-left slick-arrow-centered-y rounded-circle ml-sm-2 ml-xl-4\"></span>",
                     "nextArrow": "<span class=\"fas fa-arrow-right slick-arrow slick-arrow-soft-white slick-arrow-right slick-arrow-centered-y rounded-circle mr-sm-2 mr-xl-4\"></span>",
                     "draggable": false
                     }'>
                    @imageSlider@
                </div>
            </div>
            <!-- End Images Carousel Section -->

            <!-- Selection Section -->
            <div class="sortselectionmain @hideSite@">
                @sortselection@
            </div>
            <!-- End Selection Section -->

            <!-- Features Section -->
            <div class="bg-soft-dark">
                <div class="container">
                    <div class="row justify-content-around">
                        <div class="col-md-4 mb-7 mb-md-0 space-2 @php __hide('sticker_slogan1'); php@">
                            <!-- Contacts -->
                            @sticker_slogan1@
                            <!-- End Contacts -->
                        </div>

                        <div class="col-md-4 mb-7 mb-md-0 space-2 @php __hide('sticker_slogan2'); php@">
                            <!-- Contacts -->
                            @sticker_slogan2@
                            <!-- End Contacts -->
                        </div>

                        <div class="col-md-4 mb-7 mb-md-0 space-2 @php __hide('sticker_slogan3'); php@">
                            <!-- Contacts -->
                            @sticker_slogan3@
                            <!-- End Contacts -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Features Section -->


            <!-- Categories Section -->
            <div class="container space-2 space-lg-2 @hideSite@">
                <!-- Вывод отмеченных каталогов -->
                <div class="row mb-2 @php __hide('leftCatalTable'); php@ justify-content-around">
                    @leftCatalTable@
                </div>
                <!-- Конец отмеченных каталогов -->
            </div>
            <!-- End Categories Section -->

            <!-- Promo Section -->
            <div class="container space-2 space-lg-2 border-top @php __hide('productDay'); php@ @hideSite@">
                <div class="row">
                    <div class="col-lg-6 col-md-6 mb-lg-0">
                        @productDay@
                    </div>

                    <div class="col-lg-6 col-md-6 transition-3d-hover">
                        <!-- Баннер -->
                        @banersDisp@
                        <!-- Конец баннера -->
                    </div>
                </div>
            </div>
            <!-- End Promo Section -->

            <!-- Спецпредложения -->
            <div class="container space-2 space-lg-2 @php __hide('specMain'); php@ border-top @hideSite@">

                <!-- Title -->
                <div class="w-md-80 w-lg-40 text-center mx-md-auto mb-5 mb-md-9">
                    <h2>{Спецпредложения}</h2>
                </div>
                <!-- End Title -->

                <!-- Вывод товаров -->

                <!-- Slick Carousel -->
                <div class="js-slick-carousel slick z-index-2 mx-md-auto mb-5 mb-md-9" data-hs-slick-carousel-options='{
                     "slidesToShow": 6,
                     "slidesToScroll": 3,
                     "autoplay": true,
                     "autoplaySpeed": 5000,
                     "dots": true,
                     "dotsClass": "slick-pagination",
                     "responsive": [{
                     "breakpoint": 1200,
                     "settings": {
                     "slidesToShow": 5
                     }
                     }, {
                     "breakpoint": 992,
                     "settings": {
                     "slidesToShow": 4
                     }
                     }, {
                     "breakpoint": 768,
                     "settings": {
                     "slidesToShow": 3
                     }
                     }, {
                     "breakpoint": 554,
                     "settings": {
                     "slidesToShow": 2,
                     "slidesToScroll": 2

                     }
                     }]
                     }'>

                    @specMain@
                </div>
                <!-- End Slick Carousel -->


                <div class="text-center @hideSite@">
                    <a class="btn btn-primary transition-3d-hover px-5" href="/spec/">{Посмотреть все товары}</a>
                </div>
            </div>
            <!-- End Спецпредложения -->


            <!-- News Section -->
            <div class="space-2 container space-bottom-2 @php __hide('miniNews'); php@ border-top border-bottom">

                <div class="row mb-3">
                    @miniNews@
                </div>


                <div class="text-center">
                    <a class="btn btn-primary  transition-3d-hover px-5" href="/news/">{Посмотреть все новости}</a>
                </div>
            </div>
            <!-- End News Section -->




            <!-- Title -->
            <div class="w-md-80 w-lg-60 text-center mx-md-auto space-2 @php __hide('mainContent'); php@">
                <h2>@mainContentTitle@</h2>
                <p>@mainContent@</p>
            </div>
            <!-- End Title -->
            <!-- Баннер -->
            <div class="@php __hide('banersDispHorizontal'); php@">
                @banersDispHorizontal@
            </div>
            <!-- / Баннер -->
        </main>
        <!-- ========== END MAIN CONTENT ========== -->

        <!-- ========== FOOTER ========== -->
        <footer class="bg-dark">
            <div class="container">
                <div class="row justify-content-lg-between space-top-2 space-bottom-lg-1">
                    <div class="col-lg-3 mb-5">
                        <div class="d-flex align-items-start flex-column">
                            <a class="w-100 mb-3 mb-lg-auto" href="/" title="@name@">
                                <img class="brand" src="@logo@" alt="@name@">
                            </a>
                            <ul class="nav nav-sm nav-x-0 flex-column" itemscope itemtype="http://schema.org/Organization">
                                <li class="nav-item small text-muted mb-0">&copy; <span itemprop="name">@company@</span>, @year@</li>
                                <a href="tel:@telNum@"><li class="nav-item small text-muted mb-0"><span itemprop="telephone">@telNum@</span></li></a>
                                <a href="tel:@telNum2@"><li class="nav-item small text-muted mb-0"><span itemprop="telephone">@telNum2@</span></li></a>
                                <li class="nav-item small text-muted mb-0">@workingTime@</li>
                                <li class="nav-item small text-muted mb-0" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress"><span itemprop="streetAddress">@streetAddress@</span></li>
                                <li class="nav-item">

                                    <!-- Социальные сети -->
                                    <ul class="list-inline">
                                        <li class="list-inline-item @php __hide('vk'); php@"><a class="btn btn-xs btn-icon btn-soft-primary" title="ВКонтакте" href="@vk@" target="_blank"><em class="fab fa-vk" aria-hidden="true"></em></a></li>
                                        <li class="list-inline-item @php __hide('telegram'); php@"><a class="btn btn-xs btn-icon btn-soft-primary" title="Telegram" href="@telegram@" target="_blank"> <em class="fab fa-telegram" aria-hidden="true"></em></a></li>
                                        <li class="list-inline-item @php __hide('odnoklassniki'); php@"><a class="btn btn-xs btn-icon btn-soft-primary" title="Одноклассники" href="@odnoklassniki@" target="_blank"> <em class="fab fa-odnoklassniki" aria-hidden="true"></em></a></li>
                                        <li class="list-inline-item @php __hide('youtube'); php@"><a class="btn btn-xs btn-icon btn-soft-primary" title="Youtube" href="@youtube@" target="_blank"><em class="fab fa-youtube" aria-hidden="true"></em></a></li>
                                        <li class="list-inline-item  @php __hide('whatsapp'); php@"><a class="btn btn-xs btn-icon btn-soft-primary" title="WhatsApp" href="@whatsapp@" target="_blank"><em class="fab fa-whatsapp" aria-hidden="true"></em></a></li>
                                    </ul>
                                    <!-- / Социальные сети -->
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-6 col-md-4 col-lg-3 ml-lg-auto mb-5 mb-lg-0">

                        <!-- Nav Link -->
                        <ul class="nav nav-sm nav-x-0 flex-column">
                            <li class="nav-item @hideCatalog@"><a class="text-muted nav-link" href="/price/">{Прайс-лист}</a></li>
                            <li class="nav-item  @php __hide('miniNews'); php@"><a class="text-muted nav-link" href="/news/">{Новости}</a></li>
                            <li class="nav-item"><a class="text-muted nav-link" href="/gbook/">{Отзывы}</a></li>
                            <li class="nav-item"><a class="text-muted nav-link" href="/forma/">{Форма связи}</a></li>
                        </ul>
                        <!-- End Nav Link -->
                    </div>

                    <div class="col-6 col-md-4 col-lg-3 ml-lg-auto mb-5 mb-lg-0 d-none d-sm-block">

                        <!-- Nav Link -->
                        <ul class="nav nav-sm nav-x-0 flex-column">
                            @php if($_SESSION['UsersId']) echo '
                            <li class="nav-item"><a class="text-muted nav-link" href="/users/">{Настройки}</a></li>
                            <li class="nav-item @hideCatalog@"><a class="text-muted nav-link" href="/users/order.html">{Отследить заказ}</a></li>
                            <li class="nav-item"><a class="text-muted nav-link" href="/users/message.html">{Связь с менеджерами}</a></li>
                            <li class="nav-item"><a class="text-muted nav-link" href="?logout=true">{Выйти}</a></li>
                            ';
                            else echo '
                            <li class="nav-item"><a class="text-muted js-hs-unfold-invoker nav-link" href="javascript:;" data-toggle="modal" data-target="#signupModal">{Войти}</a></li>
                            <li class="nav-item"><a class="text-muted nav-link" href="/users/">{Зарегистрироваться}</a></li>
                            ';
                            php@

                        </ul>
                        <!-- End Nav Link -->
                    </div>

                    <div class="col-6 col-md-4 col-lg-3 mb-5 mb-lg-0 @php __hide('bottomMenu'); php@">

                        <!-- Nav Link -->
                        <ul class="nav nav-sm nav-x-0 flex-column">
                            <span class="text-muted">@bottomMenu@</span>
                        </ul>
                        <!-- End Nav Link -->
                    </div>

                </div>

            </div>
        </footer>
        <!-- ========== END FOOTER ========== -->

        <!-- ========== SECONDARY CONTENTS ========== -->

        <!-- ReturnCall Modal -->
        <div class="modal fade bs-example-modal-sm return-call" id="returnCallModal" tabindex="-1" role="dialog"  aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title">@returnCallName@</h4>
                    </div>
                    <form method="post" name="ajax-form" action="@ShopDir@/returncall/" data-modal="returnCallModal">
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
                            <div class="form-group">
                                <p class="small">
                                    <input type="checkbox" value="on" name="rule" class="req" > 
                                    {Я согласен}  <a href="/page/soglasie_na_obrabotku_personalnyh_dannyh.html">{на обработку моих персональных данных}</a>
                                </p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="returncall_mod_send" value="1">
                            <input type="hidden" name="ajax" value="1">
                            <button type="button" class="btn btn-soft-primary" data-dismiss="modal">{Закрыть}</button>
                            <button type="submit" class="btn btn-primary">{Заказать звонок}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- ReturnCall Modal -->

        <!-- Sign Up Modal -->
        <div class="modal fade" id="signupModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <!-- Header -->
                    <div class="modal-close">
                        <button type="button" class="btn btn-icon btn-sm btn-ghost-secondary" data-dismiss="modal" aria-label="Close">
                            <svg width="10" height="10" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                            <path fill="currentColor" d="M11.5,9.5l5-5c0.2-0.2,0.2-0.6-0.1-0.9l-1-1c-0.3-0.3-0.7-0.3-0.9-0.1l-5,5l-5-5C4.3,2.3,3.9,2.4,3.6,2.6l-1,1 C2.4,3.9,2.3,4.3,2.5,4.5l5,5l-5,5c-0.2,0.2-0.2,0.6,0.1,0.9l1,1c0.3,0.3,0.7,0.3,0.9,0.1l5-5l5,5c0.2,0.2,0.6,0.2,0.9-0.1l1-1 c0.3-0.3,0.3-0.7,0.1-0.9L11.5,9.5z"/>
                            </svg>
                        </button>
                    </div>
                    <!-- End Header -->

                    <!-- Body -->
                    <div class="modal-body p-sm-5">

                        <!-- Sign in -->
                        <div id="signinModalForm">
                            <div class="text-center mb-5">
                                <h2>{Авторизация}</h2>
                                <p id="usersError" class="text-danger">@usersError@</p>
                                <p>{У вас еще нет учетной записи}?
                                    <a href="/users/register.html">{Зарегистрироваться}</a>
                                </p>
                            </div>
                            
                            <a class="js-animation-link btn btn-block btn-primary mb-2" href="#"
                               data-hs-show-animation-options='{
                               "targetSelector": "#signinWithEmailModalForm",
                               "groupName": "idForm"
                               }'>{Войти по }Email</a>
                            <a class="btn btn-block btn-soft-primary mb-2 @sms_login_enabled@" href="/users/sms.html">{Войти по номеру телефона}</a>

                            <!-- Yandex ID -->
                            @yandexid@
                            <!-- End Yandex ID -->

                            <!-- VK ID -->
                            @vkid@
                            <!-- End VK ID -->

                        </div>
                        <!-- End Sign in -->

                        <!-- Sign in with Modal -->
                        <div id="signinWithEmailModalForm" style="display: none; opacity: 0;">
                            <div class="text-center mb-5">
                                <h2>{Авторизация}</h2>
                                <p>{У вас еще нет учетной записи}?
                                    <a href="/users/register.html">{Зарегистрироваться}</a>
                                </p>
                            </div>

                            <form class="js-validate" method="post" name="user_forma">

                                <!-- Form Group -->
                                <div class="js-form-message form-group">
                                    <label class="input-label" for="signinModalFormSrEmail">Email</label>
                                    <input type="email" class="form-control" name="login" id="signinModalFormSrEmail" placeholder="email@address.ru" aria-label="email@address.ru" required data-msg="{Пожалуйста, введите действительный адрес электронной почты}">
                                </div>
                                <!-- End Form Group -->


                                <!-- Form Group -->
                                <div class="js-form-message form-group">
                                    <label class="input-label" for="signinModalFormSrPassword">
                                        <span class="d-flex justify-content-between align-items-center">
                                            {Пароль}
                                            <a class="js-animation-link link-underline text-muted" href="javascript:;"
                                               data-hs-show-animation-options='{
                                               "targetSelector": "#forgotPasswordModalForm",
                                               "groupName": "idForm"
                                               }'>{Забыли пароль}?</a>
                                        </span>
                                    </label>
                                    <input type="password" class="form-control" name="password" id="signinModalFormSrPassword" placeholder="{не менее 4 символов}" aria-label="{не менее 4 символов}" required data-msg="{Пожалуйста, введите действительный адрес электронной почты}">

                                </div>
                                <!-- End Form Group -->

                                <div class="js-form-message form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" value="1" name="safe_users" @UserChecked@ id="safe_users">
                                        <label class="custom-control-label text-lh-lg" for="safe_users">{Запомнить}</label>
                                    </div>

                                </div>

                                <input type="hidden" value="1" name="user_enter">
                                <button type="submit" class="btn btn-block btn-primary">{Войти}</button>

                        </div>
                        <!-- End Sign in with Modal -->
                        </form>


                        <form method="post" name="userpas_forma" class="js-validate" action="/users/sendpassword.html">
                            <!-- Forgot Password -->
                            <div id="forgotPasswordModalForm" style="display: none; opacity: 0;">
                                <div class="text-center mb-5">
                                    <h2>{Забыли пароль}?</h2>
                                    <p>{Введите адрес электронной почты, который вы использовали при регистрации, и мы вышлем вам инструкции по сбросу пароля}.</p>
                                </div>

                                <!-- Form Group -->

                                <div class="js-form-message form-group">
                                    <label class="input-label" for="resetPasswordSrEmail" tabindex="0">
                                        <span class="d-flex justify-content-between align-items-center">
                                            Email
                                            <a class="js-animation-link d-flex align-items-center link-underline text-muted" href="javascript:;"
                                               data-hs-show-animation-options='{
                                               "targetSelector": "#signinModalForm",
                                               "groupName": "idForm"
                                               }'>
                                                <i class="fas fa-angle-left mr-2"></i>{Вернуться назад}
                                            </a>
                                        </span>
                                    </label>
                                    <input type="email" class="form-control" name="login" id="resetPasswordSrEmail" tabindex="1" placeholder="email@address.ru" aria-label="{Пожалуйста, введите действительный адрес электронной почты}" required data-msg="{Пожалуйста, введите действительный адрес электронной почты}">
                                </div>
                                <!-- End Form Group -->

                                <input type="hidden" value="1" name="passw_send">
                                <button type="submit" class="btn btn-block btn-primary">{Выслать пароль}</button>
                            </div>
                            <!-- End Forgot Password -->
                        </form>
                    </div>
                    <!-- End Body -->


                </div>
            </div>
        </div>
        <!-- End Sign Up Modal -->
        <!-- ========== END SECONDARY CONTENTS ========== -->

        <!-- Go to Top -->
        <a class="js-go-to go-to position-fixed" href="javascript:;" style="visibility: hidden;"
           data-hs-go-to-options='{
           "offsetTop": 700,
           "position": {
           "init": {
           "left": 15
           },
           "show": {
           "bottom": 15
           },
           "hide": {
           "bottom": -15
           }
           }
           }'>
            <i class="fas fa-angle-up"></i>
        </a>
        <!-- End Go to Top -->

        <!-- Notification -->
        <div id="notification" class="success-notification" style="display:none">
            <div class="alert alert-primary alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">x</span>
                </button>
                <span class="notification-alert"> </span>
            </div>
        </div>
        <!--/ Notification -->

        <!-- Cookie Alert -->
        <div class="container position-fixed bottom-0 right-0 left-0 z-index-4 cookie-message d-none">
            <div class="alert bg-white w-lg-80 border shadow-sm mx-auto" role="alert">
                <h5 class="text-dark">{Конфиденциальность}</h5>
                <p class="small"></p>

                <div class="row align-items-sm-center">
                    <div class="col-sm-12 text-sm-right">
                        <a type="button" class="btn btn-sm btn-primary transition-3d-hover" data-dismiss="alert" aria-label="Close">{Принять}</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Cookie Alert -->

        <!-- Модальное окно Спасибо-->
        <div id="thanks-box" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Заголовок модального окна -->
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                        <div class="h4 modal-title">{Сообщение}</div>
                    </div>
                    <!-- Основное содержимое модального окна -->
                    <div class="modal-body">

                    </div>
                </div>
            </div>
        </div>
        <!--/ Модальное окно Спасибо-->

        <!-- JS Global Compulsory  -->
        <script src="@pathTemplate@/assets/vendor/jquery/dist/jquery.min.js"></script>
        <script src="@pathTemplate@/assets/vendor/jquery-migrate/dist/jquery-migrate.min.js"></script>
        <script src="@pathTemplate@/assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

        @editor@

        <!-- JS Implementing Plugins -->
        <script src="@pathTemplate@/assets/vendor/hs-header/dist/hs-header.min.js"></script>
        <script src="@pathTemplate@/assets/vendor/hs-go-to/dist/hs-go-to.min.js"></script>
        <script src="@pathTemplate@/assets/vendor/hs-unfold/dist/hs-unfold.min.js"></script>
        <script src="@pathTemplate@/assets/vendor/hs-mega-menu/dist/hs-mega-menu.min.js"></script>
        <script src="@pathTemplate@/assets/vendor/hs-show-animation/dist/hs-show-animation.min.js"></script>
        <script src="@pathTemplate@/assets/vendor/jquery-validation/dist/jquery.validate.min.js"></script>
        <script src="@pathTemplate@/assets/vendor/slick-carousel/slick/slick.min.js"></script>
        <script src="@pathTemplate@/js/jquery.suggestions.min.js"></script>

        <!-- JS Front -->
        <script src="@pathTemplate@/assets/js/theme.min.js"></script>

        <!-- Core JS -->
        <script src="@pathTemplateMin@/js/phpshop.js"></script>
        <script src="@pathMin@java/jqfunc.js"></script>
        <script src="phpshop/locale/@php echo $_SESSION['lang']; php@/template.js"></script>
        <script src="@pathTemplate@/js/jquery.cookie.min.js"></script>
        <script src="@pathTemplate@/js/jquery.maskedinput.min.js"></script>

        <!-- JS Plugins Init. -->
        <script src="@pathTemplateMin@/js/flow.js"></script>

        @visualcart_lib@
        <div class="d-none d-sm-block">