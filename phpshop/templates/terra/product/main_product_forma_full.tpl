<style>.middle-content-block{width:100%}
    .left-menu{display:none!important}
</style>

<!-- Product -->
<div itemscope itemtype="http://schema.org/Product">
    <meta itemprop="image" content="@productImg@">
    <div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
        <meta itemprop="ratingValue" content="@productRatingValue@">
        <meta itemprop="ratingCount" content="@productRatingCount@">
    </div>
    <div class="row product-info product-page-wrapper">

        <div class="col-sm-6 images-block">
            <div id="fotoload">
                @productFotoList@
                <span class="sale-icon-content">
                    @specIcon@
                    @newtipIcon@
                    @giftIcon@
                    @hitIcon@
                    @promotionsIcon@
                </span>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="product-details">
                <h1 itemprop="name">@productName@</h1>

                <div class="product-manufacturer-logo-block brand-url">
                    @brandUidDescription@

                    <div class="rating">@rateUid@</div>
                </div>    

                <div class="row @hideCatalog@" >
                    <div class="col-sm-12 col-md-6 col-xs-12">
                        <div class="price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                            <span class="price-new priceService" itemprop="price" content="@productSchemaPrice@">@productPrice@</span> 
                            <span class="price-new rubznak" itemprop="priceCurrency" content="RUB">@productValutaName@</span>
                            <span class="price-old">@productPriceOld@</span>
                        </div>@ComStartNotice@
                        <div class="outStock">@productOutStock@</div>
                        @ComEndNotice@
                    </div><div class="col-sm-6 col-md-6 col-xs-12">
                        @oneclick@
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 col-sm-12 col-xs-12 pull-right">
                        <div class="button-group-cw">
                            <button class="btn btn-compare addToCompareList" data-uid="@productUid@"><i class="fa fa-sliders" aria-hidden="true"></i> {Сравнить}</button>                            
                            <button class="btn btn-wishlist addToWishList" data-uid="@productUid@"><i class="fa fa-heart-o" aria-hidden="true"></i> {Отложить}</button>
                        </div>
                        @ComStartNotice@
                        <div class="cart-button button-group compare-list-button-wrapper @hideCatalog@">
                            <a class="btn btn-cart" href="/users/notice.html?productId=@productUid@" title="@productNotice@">
                                <i class="fa fa-envelope-o" aria-hidden="true"></i> {Уведомить}
                            </a>                                   
                        </div>
                        @ComEndNotice@ 
                        
                        
                    </div>
                    <div class="col-md-6 col-sm-12 col-xs-12">
                        <div class="product-features features">
                            @vendorDisp@
                        </div>
                        <div class="product-features articul">
                            @productArt@
                        </div>
                        <div class="product-features promotion">
                            @promotionInfo@
                        </div>
                    </div>
                </div>
                @productservices_list@
                <div class="options">
                    <div class="product-page-option-wrapper">
                        @optionsDisp@
                    </div>
                </div><!-- /options -->

                <div class="subtypes">
                    @productParentList@
                </div>

                <div class="btn_buy_block @elementCartHide@ @hideCatalog@">
                    <div class="quantity">
                        <label class="label-quantity ">{Количество}</label>
                        <div class="quant input-group">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default_l btn-number"  data-type="minus" data-field="quant[2]">–</button>
                            </span>
                            <input type="text" name="quant[2]" class="form-control form-control_gr input-number" value="1" min="1" max="100">
                            <span class="input-group-btn">
                                <button type="button" class=" btn btn-default_r btn-number" data-type="plus" data-field="quant[2]">+</button>
                            </span>
                        </div>
                    </div>
                    <div class="cart-button-wrapper">
                        <button type="button" class="btn btn-cart addToCartFull" data-num="1" data-uid="@productUid@" data-cart="@productSaleReady@">Купить</button>
                    </div>
                </div>

                <div class="available @hideCatalog@" id="items">@productSklad@</div>
                
                @deliverywidget@

                <div class="product-tabs">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#proddesc">{Описание}</a></li>
                        <li><a data-toggle="tab" href="#prodrev">{Отзывы}</a></li>
                        @productFilesStart@
                        <li><a data-toggle="tab" href="#prodfile">{Файлы}</a></li>
                        @productFilesEnd@
                        <li class="@php __hide('pagetemaDisp'); php@"><a data-toggle="tab" href="#prodpage">{Статьи}</a></li>
                    </ul>

                    <div class="tab-content">
                        <div id="proddesc" class="tab-pane fade in active">
                            <div class="content" itemprop="description">
                                @productDes@
                            </div>
                        </div>
                        <div id="prodrev" class="tab-pane fade">
                            <div class="content">
                                <div id="commentList"></div>
                                <button class="btn btn-info btn-show-comment-add-block" onclick="$('#addComment').slideToggle();
                                        $(this).hide();"><span class="glyphicon glyphicon-plus-sign"></span> {Новый комментарий}</button>

                                <div id='addComment' class="well well-sm" style='display:none;margin-top:30px;'>
                                    <div class="comment-head">{Оставьте свой отзыв}</div>
                                    <textarea id="message" class="commentTexttextarea form-control"></textarea>
                                    <input type="hidden" id="commentAuthFlag" name="commentAuthFlag" value="@php if($_SESSION['UsersId']) echo 1; else echo 0; php@">
                                    <br>
                                    <div class="btn-group" data-toggle="buttons">
                                        <label class="btn btn-success btn-sm"><input type="radio" name="rate" value="1"> +1</label>
                                        <label class="btn btn-success btn-sm"><input type="radio" name="rate" value="2"> +2</label>
                                        <label class="btn btn-success btn-sm"><input type="radio" name="rate" value="3"> +3</label>
                                        <label class="btn btn-success btn-sm"><input type="radio" name="rate" value="4"> +4</label>
                                        <label class="btn btn-success btn-sm active"><input type="radio" name="rate" value="5" checked> +5</label>
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
                        <div id="prodfile" class="tab-pane fade">
                            <div class="content" itemprop="description">
                                @productFiles@
                            </div>
                        </div>
                        <div id="prodpage" class="tab-pane fade">
                            <div class="content" itemprop="description">
                                @pagetemaDisp@
                            </div>
                        </div>
                    </div>

                </div>

            </div><!-- /product-details --> 
        </div>

    </div><!-- /product-info -->
    @productsgroup_list@
</div>