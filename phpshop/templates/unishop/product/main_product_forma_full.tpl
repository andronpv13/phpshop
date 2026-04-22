<!-- Product -->
<style type="text/css">
    .main-sidebar-left {display: none;}
    .middle-content-fix{width: 100%;}
</style>
<div class="product-page-main-block row" itemscope itemtype="http://schema.org/Product">
    <meta itemprop="image" content="@productImg@">
    <div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
        <meta itemprop="ratingValue" content="@productRatingValue@">
        <meta itemprop="ratingCount" content="@productRatingCount@">
    </div>
    <div class="col-xs-12 col-md-5">
        <div id="fotoload">
            <div class="sale-icon-content">
                @specIcon@
                @newtipIcon@
                @giftIcon@
                @hitIcon@
                @promotionsIcon@
            </div>
            @productFotoList@
        </div>
    </div>
    <div class="col-xs-12 col-md-7">
        <div class="col-xs-12">
            <div class="row">
                <div class="rating">
                    @rateUid@
                </div>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="row">
                <h1 class="product-name page-title" itemprop="name">@productName@</h1>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="row">
            </div>
        </div>
        <div class="col-xs-12 @hideCatalog@">
            <div class="row">
                <div class="product-page-price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                    <span class="price-new priceService" itemprop="price" content="@productSchemaPrice@">@productPrice@</span><span class="price-new rubznak" itemprop="priceCurrency" content="RUB">@productValutaName@</span>  <del class="price-old">@productPriceOld@</del>
                </div>
                @ComStartNotice@
                <div class="outStock">@productOutStock@</div>
                @ComEndNotice@

            </div>
        </div>
        <div class="col-xs-12">
            <div class="row">
                <span class="product-art">@productArt@ </span>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="row">
                <div class="product-smalldescription">
                    @productContent@
                </div>
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
            <div class="row">
                @productservices_list@
            </div>
        </div>
        <div class="col-xs-12">
            <div class="row">
                <div class="product-page-option-wrapper">
                    @optionsDisp@
                </div>
                <div class="parrent-wrapper">
                    @productParentList@
                </div>
            </div>
        </div>
        <div class="col-xs-12 @hideCatalog@">
            <div class="row">
                <span class="product-sklad" id="items"> @productSklad@</span>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="row">
                <div class="product-page-input-number @hideCatalog@">
                    <div class="quant-main">
                        <label class="control-label text-uppercase @elementCartHide@">{Количество}</label>
                        <div class="quant input-group @elementCartHide@">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default btn-default_l btn-number"  data-type="minus" data-field="quant[2]">
                                    <span class="feather iconz-minus"></span>
                                </button>
                            </span>
                            <input type="text" name="quant[2]" class="form-control form-control_gr input-number" value="1" min="1" max="100">
                            <span class="input-group-btn">
                                <button type="button" class=" btn btn-default btn-default_r btn-number" data-type="plus" data-field="quant[2]">
                                    <span class="feather iconz-plus"></span>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="product-page-button">
                    <div class="cart-button button-group compare-list-button-wrapper">
                        <button class="btn btn-wishlist addToCompareList hidden-md" data-uid="@productUid@" data-title="{Сравнить}" data-placement="top" data-toggle="tooltip"><i class="fa fa-bar-chart-o"></i></button>
                        <button type="button" class="btn btn-cart addToWishList" data-uid="@productUid@" data-title="{Отложить}" data-placement="top" data-toggle="tooltip">
                            <i class="feather iconz-heart"></i>
                        </button>                                   
                    </div>
                    <div class="cart-button button-group cart-list-button-wrapper @elementCartHide@ @hideCatalog@">
                        <button type="button" class="btn btn-cart addToCartFull" data-num="1" data-uid="@productUid@" data-cart="@productSaleReady@">
                            <i class="feather iconz-trash"></i>
                            <span>@productSale@</span>
                        </button>                                   
                    </div>
                    <div class="cart-button button-group cart-list-button-wrapper  @elementCartOptionHide@ @hideCatalog@">
                        <button type="button" class="btn btn-cart addToCartFull" data-num="1" data-uid="@productUid@" data-cart="@productSaleReady@">
                            <i class="feather iconz-trash"></i>
                            <span>@productSale@</span>
                        </button>                                   
                    </div>
                    @ComStartNotice@
                    <div class="cart-button button-group compare-list-button-wrapper @hideCatalog@">
                        <a class="btn btn-cart" href="/users/notice.html?productId=@productUid@" title="@productNotice@">
                            <i class="fa fa-envelope-o" aria-hidden="true"></i>                            
                            {Уведомить}
                        </a>                                   
                    </div>
                    @ComEndNotice@ 
                </div>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="row">
                <div class="product-page-secondary-button">
                    <div class="oneclick-wrapper">
                        @oneclick@
                    </div>
                    <div class="pricemail-wrapper @hideCatalog@">
                        <a class="btn btn-cart" href="/pricemail/UID_@productUid@.html">@productBestPrice@</a>
                    </div>
                </div>
                
                @deliverywidget@
            </div>
        </div>
    </div>
    <div class="col-xs-12">
        <div role="tabpanel" class="tabpanel-wrapper">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active "><a href="#home" aria-controls="home" role="tab" data-toggle="tab">{Описание}</a></li>
                <li role="presentation" class=" @php __hide('vendorDisp'); php@" id="settingsTab"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">{Характеристики}</a></li>
                <li role="presentation" class=""><a href="#messages" id="commentLoad" data-uid="@productUid@" aria-controls="messages" role="tab" data-toggle="tab">{Отзывы}</a></li>
                @productFilesStart@
                <li role="presentation" id="filesTab" ><a href="#files" aria-controls="files" role="tab" data-toggle="tab">{Файлы}</a></li>
                @productFilesEnd@
                <li role="presentation" id="pagesTab" class="@php __hide('pagetemaDisp'); php@"><a href="#pages" aria-controls="pages" role="tab" data-toggle="tab">{Статьи}</a></li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="home" itemprop="description">@productDes@</div>
                <div role="tabpanel" class="tab-pane" id="settings">  
                    <div class="row">
                        <div class="col-md-8">@vendorDisp@</div>
                        <div class="col-md-4">@brandUidDescription@</div>
                    </div>

                </div>
                <div role="tabpanel" class="tab-pane hidden-xs" id="messages">

                    <div id="commentList"> </div>


                    <button class="btn btn-info pull-right" onclick="$('#addComment').slideToggle();
                            $(this).hide();"><span class="glyphicon glyphicon-plus-sign"></span> {Новый комментарий}</button>

                    <div id='addComment' class="well well-sm" style='display:none;margin-top:30px;'>

                        <div class="comment-head">{Оставьте свой отзыв}</div>

                        <textarea id="message" class="commentTextarea form-control"></textarea>
                        <input type="hidden" id="commentAuthFlag" name="commentAuthFlag" value="@php if($_SESSION['UsersId']) echo 1; else echo 0; php@">
                        <br>
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-success btn-sm">
                                <input type="radio" name="rate" value="1"> +1
                            </label>
                            <label class="btn btn-success btn-sm">
                                <input type="radio" name="rate" value="2"> +2
                            </label>
                            <label class="btn btn-success btn-sm">
                                <input type="radio" name="rate" value="3"> +3
                            </label>
                            <label class="btn btn-success btn-sm">
                                <input type="radio" name="rate" value="4"> +4
                            </label>
                            <label class="btn btn-success btn-sm active">
                                <input type="radio" name="rate" value="5" checked> +5
                            </label>
                            <button class="btn btn-info btn-sm pull-right" onclick="commentList('@productUid@', 'add', 1);">{Проголосовать}</button>
                        </div>

                    </div>
                </div>
                <div role="tabpanel" class="tab-pane hidden-xs" id="files">@productFiles@</div>
                <div role="tabpanel" class="tab-pane hidden-xs" id="pages">@pagetemaDisp@</div>
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