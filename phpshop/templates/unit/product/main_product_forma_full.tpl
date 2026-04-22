<style>
    .left-content, .content-product {display:none!important}
    .center-block{width:100%; padding-left:0}
    .last-see-product{display:block}
    .head-block{min-height:0}
    .bar-padding-fix {
        height: 90px !important;
    }
    @media(max-width:767px){.breadcrumb{display:none!important}}
</style>
<div class="main-product airSticky_stop-block" itemscope itemtype="http://schema.org/Product">
    <meta itemprop="image" content="@productImgBigFoto@">
    <div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
        <meta itemprop="ratingValue" content="@productRatingValue@">
        <meta itemprop="ratingCount" content="@productRatingCount@">
    </div>
    <div class="">
        <div class=" d-flex align-items-center justify-content-between main-product-name ">
            <h1 itemprop="name" class="">@productName@</h1>
            <div class="">@brandUidDescription@</div>
        </div>
        <div class=" d-flex align-items-start justify-content-between flex-wrap">
            <div class="col-xs-12 col-md-5">
                <div class="sale-icon-content">
                    @specIcon@
                    @newtipIcon@
                    @giftIcon@
                    @hitIcon@
                    @promotionsIcon@
                </div>
                <div id="fotoload">
                    @productFotoList@
                </div>
            </div>
            <div class="col-6">
                <div class="airSticky">
                    <div class="d-flex align-items-center justify-content-between info-block">
                        <div class="">@productArt@</div>
                        <div class="d-flex align-items-center justify-content-between">   <div class="rating-amount"><a href="#messages">{Отзывы}: @avgRateNum@ </a></div>  <div class="hidden-xs rating">
                                @rateUid@
                            </div>

                        </div>
                        <div class="d-flex align-items-center justify-content-between">     
                            <button class=" addToCompareList " data-uid="@productUid@"><span class="icons icons-dgreen icons-small icons-compare"></span></button>
                            <button class=" addToWishList " data-uid="@productUid@"><span class="icons icons-dgreen icons-small icons-wishlist"></span></button>
                        </div>
                    </div>
                    <div class="d-flex align-items-start justify-content-between price-block @hideCatalog@">
                        <div itemprop="offers" itemscope itemtype="http://schema.org/Offer">

                            <div class="product-price">
                                <div class="d-flex align-items-center justify-content-between @php __hide('productPriceOld'); php@">   <div class=" price-old  ">  @productPriceOld@</div>&nbsp;&nbsp;&nbsp; <span class="">
                                        @specIcon@</span></div>
                                <div class="price-new"  ><span itemprop="price" class="priceService" content="@productSchemaPrice@">@productPrice@</span> 
                                    <span itemprop="priceCurrency" class="rubznak" content="RUB">@productValutaName@</span></div>

                            </div>

                            <button class="best-btn" data-toggle="modal" data-target="#bestPriceModal"><span class="icons icons-comment"></span>@productBestPrice@</button>
                        </div> 
                        <div>    <div class="sklad" id="items">@productSklad@</div>
                            @ComStartNotice@
                            <div class="outStock">@productOutStock@</div>
                            @ComEndNotice@</div>
                    </div>
                    <div>

                        @promotionInfo@

                        @saferouteCart@
                        @productservices_list@
                    </div>
                    <div class="option-block">
                        @optionsDisp@
                        @productParentList@
                    </div>
                    <div class="d-flex align-items-start justify-content-start flex-wrap shop-panel @hideCatalog@" style="padding-bottom:30px">
                        <div class="input-group addToCart @elementCartHide@">
                            <div class="quant-main">
                                <div class="quant input-group ">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn btn-default btn-default_l btn-number"  data-type="minus" data-field="quant[2]">
                                            -
                                        </button>
                                    </span>
                                    <input type="text" name="quant[2]" class="form-control form-control_gr input-number" value="1" min="1" max="100">
                                    <span class="input-group-btn">
                                        <button type="button" class=" btn btn-default btn-default_r btn-number" data-type="plus" data-field="quant[2]">
                                            +
                                        </button>
                                    </span>
                                </div>
                            </div>
                            <button class=" addToCartFull " data-num="1" data-uid="@productUid@">
                                @unitProductSale@
                            </button>
                        </div>  <div class="input-group addToCart @elementCartOptionHide@">
                            <div class="quant-main">
                                <div class="quant input-group ">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn btn-default btn-default_l btn-number"  data-type="minus" data-field="quant[2]">
                                            -
                                        </button>
                                    </span>
                                    <input type="text" name="quant[2]" class="form-control form-control_gr input-number" value="1" min="1" max="100">
                                    <span class="input-group-btn">
                                        <button type="button" class=" btn btn-default btn-default_r btn-number" data-type="plus" data-field="quant[2]">
                                            +
                                        </button>
                                    </span>
                                </div>
                            </div>
                            <button class=" addToCartFull " data-num="1" data-uid="@productUid@">
                                @unitProductSale@
                            </button>
                        </div>
                        @ComStartNotice@
                        <a href="#" class="notice-btn" title="@productNotice@" class="btn btn-primary noticeBtn one" data-product-id="@productUid@">
                            {Уведомить}<span class="icons icons-notice"></span>
                        </a> @ComEndNotice@
                        <div class="one-click-block">@oneclick@</div>
                    </div>
                    <div class="pay-sticker @hideCatalog@">
                        {Мы принимаем к оплате}:<br> @sticker_pay@
                    </div>
                    <a href="" data-toggle="modal" data-target="#forma" class="link d-flex align-items-center "><span class="icons icons-info"></span>{Задать вопрос по продукту}</a>
                    <div class="clearfix"></div>
                    
                    @deliverywidget@
                </div>
            </div>
        </div>
    </div>
    <div class="">
        <div role="tabpanel" class="col-6">
            <!-- Nav tabs -->@panorama360@
            <div class="main-tabs">
                <ul class="nav nav-tabs product-tabs">
                    <li role="presentation" class=" hidden-xs @php __hide('productDes'); php@"><a href="#home" aria-controls="home"> {Описание}</a></li>
                    <li role="presentation" class="hidden-xs @php __hide('vendorDisp'); php@" id="settingsTab"><a href="#settings" aria-controls="settings" role="tab"> {Характеристики}</a></li>
                    <li role="presentation" class="hidden-xs"><a href="#messages" id="commentLoad" data-uid="@productUid@" aria-controls="messages" role="tab"> {Отзывы}</a></li>
                    @productFilesStart@<li role="presentation" class="hidden-xs"><a href="#files" aria-controls="files" role="tab"> {Файлы}</a></li>@productFilesEnd@
                    <li  id="pagesTab" class=" @php __hide('pagetemaDisp'); php@ hidden-xs"><a href="#pages" > {Обзоры}</a></li>
                </ul>
                <p></p>

                <div class="tab-content">
                    <div class=" active @php __hide('productDes'); php@" id="home" itemprop="description" role="tabpanel">
                        <div class="tab-name">{Описание товара}</div>
                        @productDes@ 
                    </div>
                    <div role="tabpanel" class="@php __hide('vendorDisp'); php@" id="settings" role="tabpanel">
                        <div class="tab-name">{Характеристики}</div>
                        <div class="row">
                            <div class="col-md-8 tab-content">@vendorDisp@</div>

                        </div>

                    </div>
                    <div role="tabpanel" id="messages">
                        <div class="tab-name">{Отзывы}</div>
                        <div id="commentList"></div>
                        <div class="comment-more hide-click">{Показать еще}</div>
                        <br>
                        <br>
                        <a href="" class="otz" data-toggle="modal" data-target="#reviewModal">{Оставить отзыв}</a>
                    </div>
                    @productFilesStart@
                    <div role="tabpanel" id="files">
                        <div class="tab-name">{Файлы}</div>
                        @productFiles@
                    </div>
                    @productFilesEnd@
                    <div role="tabpanel" id="pages" class="@php __hide('pagetemaDisp'); php@">
                        <div class="tab-name">{Обзоры}</div>
                        <div class="tab-content">@pagetemaDisp@</div>
                    </div>
                </div> 
            </div>
        </div>
    </div>
</div>
@productsgroup_list@

<!-- Модальное окно фотогалереи -->
<div class="modal bs-example-modal" id="sliderModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                <div class="h4 modal-title" id="myModalLabel">@productName@</div>
            </div>
            <div class="modal-body">
                @productFotoListBig@
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{Закрыть}</button>
            </div>
        </div>
    </div>
</div>
<!--/ Модальное окно фотогалереи -->

<!--Модальное окно таблица размеров-->
<div class="modal fade bs-example-modal-sm size-modal" id="sizeModal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                <div class="h4 modal-title">{Таблица размеров}</div>
            </div>
            <form method="post" name="user_forma_size_delivery" action="@ShopDir@/returncall/">
                <div class="modal-body">
                    @productOption1@
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-default" data-dismiss="modal">{Закрыть}</button>

                </div>
            </form>
        </div>
    </div>
</div>
<!--Модальное окно таблица размеров-->
<!--Модальное окно информация о доставке-->
<div class="modal fade bs-example-modal-sm size-modal" id="shipModal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                <div class="h4 modal-title">{Информация о доставке}</div>
            </div>
            <form method="post" name="user_forma_size_delivery" action="@ShopDir@/returncall/">
                <div class="modal-body">

                    @productOption2@

                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-default" data-dismiss="modal">{Закрыть}</button>

                </div>
            </form>
        </div>
    </div>
</div>
<!--Модальное окно информация о доставке-->

<div class="modal fade new-modal" id="reviewModal" tabindex="-1" role="dialog" aria-labelledby="{Оставить отзыв}" aria-hidden="true">
    <div class="modal-dialog small-modal" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="h4 modal-title" id="exampleModalLabel" class="d-flex">{Оставить отзыв}
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <h4><a href="/shop/UID_@productUid@.html" title="@productNameClean@">@productName@</a></h4>
                <div class="col-md-12">
                    <div class="row">
                        <div class="image">
                            <a href="/shop/UID_@productUid@.html" title="@productNameClean@">
                                @productSliderOneImage@
                            </a>
                        </div>
                    </div>
                </div>
                <form id="addComment" method="post" name="ajax-form" action="phpshop/ajax/review.php" data-modal="reviewModal">
                    <h4>{Оцените товар}</h4>
                    <div class="btn-group rating-group" data-toggle="buttons">
                        <label class="btn ">
                            <input type="radio" name="rate" value="1">
                        </label>
                        <label class="btn ">
                            <input type="radio" name="rate" value="2">
                        </label>
                        <label class="btn ">
                            <input type="radio" name="rate" value="3">
                        </label>
                        <label class="btn ">
                            <input type="radio" name="rate" value="4">
                        </label>
                        <label class="btn ">
                            <input type="radio" name="rate" value="5" checked>
                        </label>
                    </div>
                    <div class="form-group">
                        <div class=""></div>
                        <div class="">
                            <textarea placeholder="{Комментарий}" name="message" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="">
                        </div>
                        <div class="">
                            <input placeholder="{Имя}" type="text" name="name_new" value="" class="form-control" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="">
                        </div>
                        <div class="">
                            <input placeholder="E-mail" type="email" name="mail" value="" class="form-control" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="">
                        </div>
                        <div class="">
                            @review_captcha@
                        </div>
                    </div>
                    <p class="small"><label><input name="rule" value="1" required=""  type="checkbox">
                            @rule@</label></p>
                    <div class="form-group">
                        <div class=""></div>
                        <div class="">
                            <input type="hidden" name="send_price_link" value="ok">
                            <input type="hidden" name="ajax" value="1">
                            <input type="hidden" name="productId" value="@productUid@">
                            <button type="submit" class="btn btn-main">{Оставить отзыв}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade new-modal" id="bestPriceModal" tabindex="-1" role="dialog" aria-labelledby="{Пожаловаться на цену}" aria-hidden="true">
    <div class="modal-dialog small-modal" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="h4 modal-title" id="exampleModalLabel" class="d-flex">{Пожаловаться на цену} <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button></div >
            </div>
            <div class="modal-body">
                <h4><a href="/shop/UID_@productUid@.html" title="@productNameClean@">@productName@</a></h4>
                <div class="col-md-6">
                    <div class="row">
                        <div class="image">
                            <a href="/shop/UID_@productUid@.html" title="@productNameClean@">
                                @productSliderOneImage@
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="caption">
                            <div class="price">
                                <span class="price-new">@productPrice@ <span
                                        class="rubznak">@productValutaName@</span></span>
                                <span class="price-old">@productPriceOld@</span>

                            </div>
                            @ComStartNotice@
                            <div class="outStock">@productOutStock@</div>
                            @ComEndNotice@
                        </div>
                    </div>
                </div>
                <form method="post" name="ajax-form" action="phpshop/ajax/pricemail.php" data-modal="bestPriceModal">
                    <div class="form-group">
                        <div class="">
                        </div>
                        <div class="">
                            <input placeholder="{Ссылка на товар с меньшей ценой}" type="text" name="link_to_page" class="form-control" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="">
                        </div>
                        <div class="">
                            <input placeholder="{Имя}" type="text" name="name_person" class="form-control" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="">
                        </div>
                        <div class="">
                            <input placeholder="E-mail" type="email" name="mail" class="form-control" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="">
                        </div>
                        <div class="">
                            <input placeholder="{Телефон}" type="text" name="tel_name" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="">
                        </div>
                        <div class="">
                            <textarea placeholder="{Дополнительная информация}" name="adr_name" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="">
                        </div>
                        <div class="">
                            @captcha@
                        </div>
                    </div>
                    <p class="small"><label><input name="rule" value="1" required=""  type="checkbox">@rule@</label></p>
                    <div class="form-group">
                        <div class=""></div>
                        <div class="">
                            <input type="hidden" name="send_price_link" value="ok">
                            <input type="hidden" name="ajax" value="1">
                            <input type="hidden" name="product_id" value="@productUid@">
                            <button type="submit" class="btn btn-main">{Пожаловаться на цену}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade new-modal" id="forma" tabindex="-1" role="dialog" aria-labelledby="{Задать вопрос}" aria-hidden="true">
    <div class="modal-dialog small-modal" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="h4 modal-title" id="exampleModalLabel" class="d-flex">{Задать вопрос по продукту} <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <div class="h4"><a href="/shop/UID_@productUid@.html" title="@productNameClean@">@productName@</a></div>
                <br>
                <form method="post" name="ajax-form" data-modal="forma" action="/forma/">
                    <div class="form-group">
                        <input type="text" name="tema" placeholder="{Заголовок}"  value="@php  echo $_POST['tema']; php@" class="form-control" id="exampleInputEmail1"  required="">
                    </div>
                    <div class="form-group">
                        <input type="text" name="name" placeholder="{Имя}" value="@php  echo $_POST['name']; php@" class="form-control" id="exampleInputEmail1"  required="">
                    </div>
                    <div class="form-group">
                        <input type="email" name="mail" placeholder="E-mail"  value="@php  echo $_POST['mail']; php@" class="form-control" id="exampleInputEmail1">
                    </div>
                    <div class="form-group">
                        <input type="text" name="tel" placeholder="{Телефон}" value="@php  echo $_POST['tel']; php@" class="form-control" id="exampleInputEmail1">
                    </div>
                    <div class="form-group">
                        <textarea name="content" class="form-control" placeholder="{Сообщение}" required="">@php  echo $_POST['content']; php@</textarea>
                    </div>
                    <div class="form-group">
                        <p class="small">
                            <input type="checkbox" value="on" name="rule" class="req" > 
                            {Я согласен}  <a href="/page/soglasie_na_obrabotku_personalnyh_dannyh.html">{на обработку моих персональных данных}</a>
                        </p>
                    </div>    <div class="form-group">
                        @forma_captcha@
                        <br/>
                        <span class="">
                            <input type="hidden" name="send" value="1">
                            <input type="hidden" name="ajax" value="1">
                            <button type="submit" class="btn btn-main">{Отправить сообщение}</button>
                        </span>

                    </div>
                </form>    
            </div>
        </div>
    </div>
</div>