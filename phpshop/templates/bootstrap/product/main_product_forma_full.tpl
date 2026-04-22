<div itemscope itemtype="http://schema.org/Product">
    <meta itemprop="image" content="@productImg@">
    <div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
        <meta itemprop="ratingValue" content="@productRatingValue@">
        <meta itemprop="ratingCount" content="@productRatingCount@">
    </div>
    <div class="row">
        <div class="col-md-6 col-sm-6">
            <span class="sale-icon-content">
                @specIcon@
                @newtipIcon@
                @giftIcon@
                @hitIcon@
                @promotionsIcon@
            </span>
            <div id="fotoload">
                @productFotoList@
            </div>
        </div>
        <div class="col-md-6 col-sm-6 ">
            <div class="alert alert-warning">
                <h1 itemprop="name">@productName@</h1>
                <div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                    <h2 class="text-primary @hideCatalog@">
                        <span itemprop="price" class="priceService" content="@productSchemaPrice@">@productPrice@</span> 
                        <span itemprop="priceCurrency" class="rubznak" content="RUB">@productValutaName@</span>  <span class=" price-old">@productPriceOld@</span>
                    </h2>             
                </div> 
                @ComStartNotice@
                <div class="outStock @hideCatalog@">@productOutStock@</div>
                @ComEndNotice@
                <div class="pull-right">@pricerequest@ @oneclick@</div>
               
                <p><br></p>
                <div>
                    <div class="small">@productArt@</div>
                    <div class="small @hideCatalog@" id="items">@productSklad@</div>

                </div>

                <div class="hidden-xs rating" >
                    @rateUid@
                </div>
                <br>
                @promotionInfo@
                
                @productsproperty@

                @saferouteCart@
                @productservices_list@
            </div>

            @optionsDisp@
            @productParentList@

            <div class="row" style="padding-bottom:20px">
                <div class="col-xs-5 @elementCartOptionHide@ @hideCatalog@">
                    <div class="input-group" style="max-width: 150px">
                        <input class="form-control" data-uid="@productUid@"  type="text" style="min-width:50px" maxlength="3" value="1" placeholder="1" required="" name="quant[2]">
                        <span class="input-group-btn">
                            <button class="btn btn-primary addToCartFull" data-num="1" data-uid="@productUid@">@productSale@</button>
                        </span>    
                    </div>
                </div>
                <div class="col-xs-5 @elementCartHide@ @hideCatalog@">
                    <div class="input-group" style="max-width: 150px">
                        <input class="form-control" data-uid="@productUid@"  type="text" style="min-width:50px" maxlength="3" value="1" placeholder="1" required="" name="quant[1]">
                        <span class="input-group-btn">
                            <button class="btn btn-primary addToCartFull" data-num="1" data-uid="@productUid@">@productSale@</button>
                        </span>    
                    </div>
                </div>
                @ComStartNotice@
                <div class="col-xs-5">
                    <a class="btn btn-primary" href="/users/notice.html?productId=@productUid@" title="@productNotice@">{Уведомить}</a>
                </div>
                @ComEndNotice@ 
                <div class="col-xs-3">
                    <button class="btn btn-info addToCompareList " data-uid="@productUid@">{Сравнить}</button>
                </div>
                <div class="col-xs-3 ">
                    <button class="btn btn-default addToWishList" data-uid="@productUid@">{Отложить}</button>
                </div> 
            </div>
            <a class="btn btn-default @hideCatalog@" href="/pricemail/UID_@productUid@.html">@productBestPrice@</a>

            <div class="clearfix"></div>
            
            @deliverywidget@

        </div>
    </div>
    <div class="row">
        <div role="tabpanel" class="col-xs-12">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active hidden-xs"><a href="#home" aria-controls="home" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-info-sign"></span> {Описание}</a></li>
                <li role="presentation" class="hide hidden-xs" id="settingsTab"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-list"></span> {Характеристики}</a></li>
                <li role="presentation" class="hidden-xs"><a href="#messages" id="commentLoad" data-uid="@productUid@" aria-controls="messages" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-comment"></span> {Отзывы}</a></li>
                <li role="presentation" id="filesTab" class="hide hidden-xs"><a href="#files" aria-controls="files" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-cloud-download"></span> {Файлы}</a></li>
                <li role="presentation" id="pagesTab" class="hide hidden-xs"><a href="#pages" aria-controls="pages" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-bookmark"></span> {Статьи}</a></li>
                <li role="presentation" class="visible-lg"><a href="/print/UID_@productId@.html"  target="_blank"><span class="glyphicon glyphicon-print"></span> {Печатная форма}</a></li>
            </ul>
            <p></p>
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

<!--Модальное окно таблица размеров-->
<div class="modal fade bs-example-modal-sm size-modal" id="sizeModal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Таблица размеров</h4>
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
                <h4 class="modal-title">Информация о доставке</h4>
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