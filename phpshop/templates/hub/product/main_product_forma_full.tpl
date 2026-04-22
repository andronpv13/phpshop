<style type="text/css">
    .sidebar-right {display: none;}
    .middle-content-fix{width: 100%;}
</style>


<div class="product-page-main-block row product-number-fix" itemscope itemtype="http://schema.org/Product">
    <meta itemprop="image" content="@productImg@">
    <div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
        <meta itemprop="ratingValue" content="@productRatingValue@">
        <meta itemprop="ratingCount" content="@productRatingCount@">
    </div>
    <div class="col-xs-12 col-md-5">
        <div id="fotoload product-page-foto-wrapper">
            <div class="sale-icon-content">
                @specIcon@
                @newtipIcon@
                @hitIcon@
                @promotionsIcon@
            </div>
            @productFotoList@
        </div>
    </div>
    <div class="col-xs-12 col-md-7">
        <div class="col-xs-12">
            <div class="row">
                <h1 class="product-name page-title hide" itemprop="name">@productName@</h1>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="row">
                <div class="product-page-price @hideCatalog@" itemprop="offers" itemscope itemtype="http://schema.org/Offer">

                    <span class="price-new" itemprop="price" content="@productSchemaPrice@">@productPrice@</span> 
                    <span class="price-new rubznak" itemprop="priceCurrency" content="RUB">@productValutaName@</span> <del class="price-old">@productPriceOld@</del>
                </div>
                @ComStartNotice@
                <div class="outStock @hideCatalog@">@productOutStock@</div>
                @ComEndNotice@
                <div class="product-page-raiting rating">
                    @rateUid@
                </div>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="row">
                <div class="prodict-page-description">
                    @productContent@
                </div>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="row">
                @productArt@
            </div>
        </div>
        <div class="col-xs-12">
            <div class="row">
                <div class="product-promotions">
                    @promotionInfo@
                </div>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="row">
                <div class="odnotipListWrapper">

                </div>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="row product-page-btn-sort-fix">
                <div class="product-page-option-wrapper">
                    @optionsDisp@
                </div>
                <div class="parrent-wrapper">
                    @productParentList@
                </div>
                <div class="product-sklad @hideCatalog@" id="items">
                    @productSklad@
                </div>
                <div class="product-page-input-number @hideCatalog@">
                    <div class="quant-main">
                        <div class="quant input-group @elementCartHide@">
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
                </div>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="row">
                <div class="product-page-button">
                    <div class="cart-button button-group cart-list-button-wrapper @elementCartHide@ @hideCatalog@">
                        <button type="button" class="btn btn-cart addToCartFull" data-num="1" data-uid="@productUid@" data-cart="@productSaleReady@">
                            <i class="icons-cart" aria-hidden="true"></i>
                            <span>@productSale@</span>
                        </button>                                   
                    </div>
                    <div class="cart-button button-group compare-list-button-wrapper">
                        <button type="button" class="btn btn-cart addToWishList" data-uid="@productUid@" data-title="{Отложить}" data-placement="top" data-toggle="tooltip">
                            <i class="icons-wishlist" aria-hidden="true"></i>
                            {Отложить}
                        </button>                                   
                    </div>
                    <div class="cart-button button-group cart-list-button-wrapper  @elementCartOptionHide@ @hideCatalog@">
                        <button type="button" class="btn btn-cart addToCartFull" data-num="1" data-uid="@productUid@" data-cart="@productSaleReady@">
                            <i class="icons-cart"></i>
                            <span>@productSale@</span>
                        </button>                                   
                    </div>
                    @ComStartNotice@
                    <div class="cart-button button-group compare-list-button-wrapper @hideCatalog@">
                        <a class="btn btn-cart addToNotice" href="/users/notice.html?productId=@productUid@" title="@productNotice@">
                            <i class="fa fa-envelope-o" aria-hidden="true"></i>                            
                            Уведомить
                        </a>                                   
                    </div>
                    @ComEndNotice@ 
                </div>
            </div>
        </div>
        <div class="col-xs-12 @hideCatalog@">
            <div class="row">
                <div class="product-page-secondary-button">
                    <div class="oneclick-wrapper">
                        @oneclick@
                    </div>
                    <!-- Модуль Vkseller -->
                    <div class="mb-4 @php __hide('vkseller_link'); php@">
                        <a class="btn btn-cart" href="@vkseller_link@" target="_blank"><i class="fa fa-vk" aria-hidden="true"></i> {Купить в ВКонтакте}</a>
                    </div>

                    <!-- Модуль Ozonseller -->
                    <div class="mb-4 @php __hide('ozonseller_link'); php@">
                        <a class="btn btn-cart" href="@ozonseller_link@" target="_blank"><i class="fa fa-opera" aria-hidden="true"></i> {Купить в} OZON</a>
                    </div>

                    <!-- Модуль Wbseller -->
                    <div class="mb-4 @php __hide('wbseller_link'); php@">
                        <a class="btn btn-cart" href="@wbseller_link@" target="_blank"><i class="fa fa-wordpress" aria-hidden="true"></i> {Купить в} Wildberries</a>
                    </div>

                    <div class="pricemail-wrapper">
                        <a class="btn btn-cart" href="/pricemail/UID_@productUid@.html">@productBestPrice@</a>
                    </div>
                    
                    @deliverywidget@
                </div>
            </div>
        </div>
    </div>
    <div class="col-xs-12">
        <div role="tabpanel" class="tabpanel-wrapper">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active hidden-xs"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">{Описание}</a></li>
                <li role="presentation" class="hide hidden-xs" id="settingsTab"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">{Характеристики}</a></li>
                <li role="presentation" class="hidden-xs"><a href="#messages" id="commentLoad" data-uid="@productUid@" aria-controls="messages" role="tab" data-toggle="tab">{Отзывы}</a></li>
                <li role="presentation" id="filesTab" class="hide hidden-xs"><a href="#files" aria-controls="files" role="tab" data-toggle="tab">{Файлы}</a></li>
                <li role="presentation" id="pagesTab" class="hide hidden-xs"><a href="#pages" aria-controls="pages" role="tab" data-toggle="tab">{Статьи}</a></li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="home" itemprop="description">@productDes@</div>
                <div role="tabpanel" class="tab-pane" id="settings">  
                    <br>
                    <div class="row">
                        <div class="col-md-8">@vendorDisp@</div>
                        <div class="col-md-4">@brandUidDescription@</div>
                    </div>

                </div>
                <div role="tabpanel" class="tab-pane hidden-xs" id="messages">

                    <div id="commentList"> </div>


                    <button class="btn btn-main pull-right" onclick="$('#addComment').slideToggle();
                            $(this).hide();"><span class="glyphicon glyphicon-plus-sign"></span> {Новый комментарий}</button>

                    <div id='addComment' class="well well-sm" style='display:none;margin-top:30px;'>

                        <div class="comment-head">{Оставьте свой отзыв}</div>

                        <textarea id="message" class="commentTextarea form-control"></textarea>
                        <input type="hidden" id="commentAuthFlag" name="commentAuthFlag" value="@php if($_SESSION['UsersId']) echo 1; else echo 0; php@">
                        <br>
                        <div class="btn-group" data-toggle="buttons" style="border: none;outline: none;">
                            <label class="btn btn-success btn-sm">
                                <input type="radio" name="rate" value="1"> +1
                            </label>
                            <label class="btn btn-success btn-sm" style="border: none;outline: none;">
                                <input type="radio" name="rate" value="2"> +2
                            </label>
                            <label class="btn btn-success btn-sm" style="border: none;outline: none;">
                                <input type="radio" name="rate" value="3"> +3
                            </label>
                            <label class="btn btn-success btn-sm" style="border: none;outline: none;">
                                <input type="radio" name="rate" value="4"> +4
                            </label>
                            <label class="btn btn-success btn-sm active" style="border: none;outline: none;">
                                <input type="radio" name="rate" value="5" checked> +5
                            </label>
                            <button class="btn btn-main btn-sm pull-right" onclick="commentList('@productUid@', 'add', 1);">{Проголосовать}</button>
                        </div>

                    </div>
                </div>
                <div role="tabpanel" class="tab-pane hidden-xs" id="files">@productFiles@</div>
                <div role="tabpanel" class="tab-pane hidden-xs" id="pages">@pagetemaDisp@</div>
            </div>
        </div>
    </div>
    <div class="col-xs-12">
        <div class="product-page-social">
            <!--<a class="social-button" href="#" title="{Поделится в} Facebook"><i class="fa fa-facebook" aria-hidden="true"></i></a>-->
            <a class="social-button" href="#" title="{Поделится в} Контакте"><i class="fa fa-vk" aria-hidden="true"></i></a>
            <a class="social-button" href="#" title="{Поделится в} Одноклассники"><i class="fa fa-odnoklassniki" aria-hidden="true"></i></a>
        </div>
    </div>
</div>
<!-- Модальное окно фотогалереи -->
<div class="modal bs-example-modal" id="sliderModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>

                <h4 class="modal-title" id="myModalLabel">@productName@</h4>
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


