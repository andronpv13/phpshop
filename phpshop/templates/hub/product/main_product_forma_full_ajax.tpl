<div class="modal-content">
  <div class="modal-body">
    <div class="row">
      <div class="col-md-6">
        <div id="fotoload" class="product-img-modal">
                <div class="sale-icon-content">
                    @newtipIcon@
                    @specIcon@
                </div>
            
            @productFotoList@</div>
      </div>
      <div class="col-md-6">
        <div class="col-xs-12">
          <div class="row">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
          </div>
        </div>
        <div class="col-xs-12">
            <div class="row">
                <h1 class="product-name" itemprop="name"><a href="/shop/UID_@productUid@.html">@productName@</a></h1>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="row">
                <div class="product-page-price">
                    <del class="price-old">@productPriceRub@</del>
                    <span class="price-new" itemprop="price">@productPrice@</span> 
                    <span class="price-new rubznak" itemprop="priceCurrency" content="RUB">@productValutaName@</span>
                </div>
                <div class="product-page-raiting rating">
                    @rateUid@
                </div>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="row">
                <div class="prodict-page-description">
                    @productDes@
                </div>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="row">
                <span class="product-art">@productArt@</span>
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
            <div class="row modal-input-fix">
                <div class="product-page-input-number">
                    <div class="quant-main">
                        <div class="quant input-group @elementCartHide@">
                            <span class="input-group-btn">
                                <button type="button" class="btn bminus btn-default btn-default_l btn-number"  data-type="minus" data-field="quant[2]">
                                   -
                                </button>
                            </span>
                            <input type="text" name="quant[2]" class="form-control form-control_gr input-number" value="1" min="1" max="100">
                            <span class="input-group-btn">
                                <button type="button" class=" btn bplus btn-default btn-default_r btn-number" data-type="plus" data-field="quant[2]">
                                    +
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="product-page-option-wrapper">
                    @optionsDisp@
                </div>
                <div class="parrent-wrapper">
                    @productParentList@
                </div>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="row">
                <div class="product-page-button">
                    <div class="cart-button button-group cart-list-button-wrapper @elementCartHide@">
                        <button type="button" class="btn btn-cart addToCartFull" data-num="1" data-uid="@productUid@" data-cart="@productSaleReady@">
                            <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                            <span>@productSale@</span>
                        </button>                                   
                    </div>
                    <div class="cart-button button-group compare-list-button-wrapper">
                        <button type="button" class="btn btn-cart addToWishList" data-uid="@productUid@" data-title="{Отложить}" data-placement="top" data-toggle="tooltip">
                            <i class="fa fa-heart-o" aria-hidden="true"></i>
                            {Отложить}
                        </button>                                   
                    </div>
                   <div class="cart-button button-group cart-list-button-wrapper  @elementCartOptionHide@">
                        <button type="button" class="btn btn-cart addToCartFull" data-num="1" data-uid="@productUid@" data-cart="@productSaleReady@">
                            <i class="feather iconz-trash"></i>
                            <span>@productSale@</span>
                        </button>                                   
                    </div>
                    @ComStartNotice@
                    <div class="cart-button button-group compare-list-button-wrapper">
                        <a class="btn btn-cart" href="/users/notice.html?productId=@productUid@" title="@productNotice@">
                            <i class="fa fa-envelope-o" aria-hidden="true"></i>                            
                            {Уведомить}
                        </a>                                   
                    </div>
                    @ComEndNotice@ 
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>