<!-- Product -->
<div itemscope itemtype="http://schema.org/Product">
    <meta itemprop="image" content="@productImg@">
    <div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
        <meta itemprop="ratingValue" content="@productRatingValue@">
        <meta itemprop="ratingCount" content="@productRatingCount@">
    </div>
    <div class="row product-info product-page-wrapper" >
        <!-- Left Starts -->
        <div class="col-sm-7 images-block">

            <div id="fotoload">
                @productFotoList@
            </div>
            <span class="sale-icon-content">
                @specIcon@
                @newtipIcon@
                @giftIcon@
                @hitIcon@
                @promotionsIcon@
            </span>			
        </div>
        <!-- Left Ends -->
        <!-- Right Starts -->
        <div class="col-sm-5 product-details">
            <!-- Product Name Starts -->
            <h1 itemprop="name">@productName@</h1>
            <!-- Product Name Ends -->
            <hr>
            <!-- Manufacturer Starts -->
            <ul class="list-unstyled manufacturer product-page-list">
                <li>
                    @productArt@
                </li>
                <li id="items" class="@hideCatalog@">
                    @productSklad@
                </li>
                <li>
                    <div class="rating">
                        @rateUid@
                    </div>
                </li>
                <li>@promotionInfo@</li>
                <li>@oneclick@</li>
                
                <!-- Модуль Vkseller -->
                <li class="@php __hide('vkseller_link'); php@">
                    <a class="btn btn-cart" href="@vkseller_link@" target="_blank"><i class="fa fa-vk" aria-hidden="true"></i> {Купить в ВКонтакте}</a>
                </li>

                <!-- Модуль Ozonseller -->
                <li class="@php __hide('ozonseller_link'); php@">
                    <a class="btn btn-cart" href="@ozonseller_link@" target="_blank"><i class="fa fa-opera" aria-hidden="true"></i> {Купить в} OZON</a>
                </li>

                <!-- Модуль Wbseller -->
                <li class="@php __hide('wbseller_link'); php@">
                    <a class="btn btn-cart" href="@wbseller_link@" target="_blank"><i class="fa fa-wordpress" aria-hidden="true"></i> {Купить в} Wildberries</a>
                </li>
                <li class="@hideCatalog@"><a href="/pricemail/UID_@productUid@.html">@productBestPrice@</a></li>
            </ul>
            <!-- Manufacturer Ends -->
            <hr>
            <!-- Price Starts -->
            <div class="price @hideCatalog@" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                <span class="price-head">{Цена}:</span>
                <span class="price-new priceService" itemprop="price" content="@productSchemaPrice@">@productPrice@</span> 
                <span class="price-new rubznak" itemprop="priceCurrency" content="RUB">@productValutaName@</span>
                <span class="price-old">@productPriceOld@</span>
            </div>
            @ComStartNotice@
            <div class="outStock @hideCatalog@">@productOutStock@</div>
            @ComEndNotice@
            <!-- Price Ends -->
            <hr class="@hideCatalog@">
            <!-- Available Options Starts -->

            <div class="options fix-wrapper">

                <div class="product-page-option-wrapper">
                    @optionsDisp@
                </div>
                @productParentList@

                @productservices_list@

                <label class="control-label text-uppercase @elementCartHide@ @hideCatalog@">{Количество}</label>
                <div class="quant input-group @elementCartHide@ @hideCatalog@">
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-default btn-default_l btn-number"  data-type="minus" data-field="quant[2]">
                            <span class="glyphicon glyphicon-minus"></span>
                        </button>
                    </span>
                    <input type="text" name="quant[2]" class="form-control form-control_gr input-number" value="1" min="1" max="100">
                    <span class="input-group-btn">
                        <button type="button" class=" btn btn-default btn-default_r btn-number" data-type="plus" data-field="quant[2]">
                            <span class="glyphicon glyphicon-plus"></span>
                        </button>
                    </span>
                </div>
                <p></p>
                <div class="cart-button button-group cart-list-button-wrapper @elementCartHide@ @hideCatalog@">
                    <button type="button" class="btn btn-cart addToCartFull" data-num="1" data-uid="@productUid@" data-cart="@productSaleReady@">
                        <i class="fa fa-shopping-cart"></i>                                 
                        <span>@productSale@</span>
                    </button>                                   
                </div>
                <div class="cart-button button-group cart-list-button-wrapper  @elementCartOptionHide@ @hideCatalog@">
                    <button type="button" class="btn btn-cart addToCartFull" data-num="1" data-uid="@productUid@" data-cart="@productSaleReady@">
                        <i class="fa fa-shopping-cart"></i>                                 
                        <span>@productSale@</span>
                    </button>                                   
                </div>
                <div class="cart-button button-group compare-list-button-wrapper">
                    <button type="button" class="btn btn-cart addToWishList" data-uid="@productUid@" data-title="{Отложить}" data-placement="top" data-toggle="tooltip">
                        <i class="fa fa-heart" aria-hidden="true"></i>                            
                        {Отложить}
                    </button>                                   
                </div>
                <div class="cart-button button-group compare-list-button-wrapper">
                    <button type="button" class="btn btn-cart addToCompareList" data-uid="@productUid@" data-title="{Сравнить}" data-placement="top" data-toggle="tooltip">
                        <i class="fa fa-refresh" aria-hidden="true"></i>                            
                        {Сравнить}
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
                
                @deliverywidget@

            </div>

        </div>
        <!-- Right Ends -->
    </div>
    <!-- product Info Ends -->

    <!-- Product Description Starts -->
    <div class="product-info-box ">
        <h4 class="heading">{Описание}</h4>
        <div class="content panel-smart" itemprop="description">@productDes@</div>
    </div>
    <!-- Product Description Ends -->

    <!-- Additional Information Starts -->
    <div class="product-info-box @php __hide('vendorDisp'); php@">
        <h4 class="heading">{Характеристики}</h4>
        <div class="content panel-smart">
            @vendorDisp@
        </div>
    </div>
    <!-- Additional Information Ends -->

    <!-- Reviews Information Starts -->
    <div class="product-info-box">
        <h4 class="heading">{Отзывы}</h4>
        <div class="content panel-smart">
            <div id="commentList"></div>
            <button class="btn btn-info btn-show-comment-add-block" onclick="$('#addComment').slideToggle();
                    $(this).hide();"><span class="glyphicon glyphicon-plus-sign"></span> {Новый комментарий}</button>
            <div id='addComment' class="well well-sm" style='display:none;margin-top:30px;'>
                <div class="comment-head">{Оставьте свой отзыв}</div>
                <textarea id="message" class="commentTexttextarea form-control"></textarea>
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
        <script type="text/javascript">
            $(document).ready(function () {
                commentList('@productUid@', 'list');
            });
        </script>
    </div>
    <!-- Reviews Information Ends -->

    <!-- Files Information Starts -->
    @productFilesStart@
    <div class="product-info-box">
        <h4 class="heading">{Файлы}</h4>
        <div class="content panel-smart">
            @productFiles@
        </div>
    </div>
    @productFilesEnd@
    <!-- Files Information Ends -->

    <!-- Articles Information Starts -->
    <div class="product-info-box @php __hide('pagetemaDisp'); php@">
        <h4 class="heading">{Статьи}</h4>
        <div class="content panel-smart">
            @pagetemaDisp@
        </div>
    </div>
    <!-- Articles Information Ends -->

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
</div>