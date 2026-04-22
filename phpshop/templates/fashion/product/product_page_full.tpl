<!-- ========== MAIN CONTENT ========== -->
<main id="content" role="main">@productPageDis@
    @odnotipDisp@
</main>
<!-- ========== END MAIN CONTENT ========== -->

<!-- Модальное окно отзыва-->
<div class="modal fade new-modal" id="reviewModal" tabindex="-1" role="dialog" aria-labelledby="{Оставить отзыв}" aria-hidden="true">
    <div class="modal-dialog small-modal" role="document">
        <div class="modal-content">

            <div class="modal-body">
                <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">x</span>
                </button>
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
                            <textarea placeholder="{Комментарий}" name="message" id="message" class="form-control"></textarea>
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
                            <button type="submit" class="btn btn-block btn-primary transition-3d-hover">{Оставить отзыв}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--/ Модальное окно отзыва-->