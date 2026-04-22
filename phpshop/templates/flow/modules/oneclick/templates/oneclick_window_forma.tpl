<div class="modal fade bs-example-modal-sm oneclick-modal" id="oneClickModal@productUid@" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                <div class="h4 modal-title">{Быстрый заказ}</div>
            </div>
            <form method="post" name="ajax-form" action="@ShopDir@/oneclick/" data-modal="oneClickModal@productUid@">
                <div class="modal-body">

                    <div class="form-group">
                        <input type="text" name="oneclick_mod_name" class="form-control" placeholder="{Имя}" required="">
                    </div>
                    <div class="form-group">
                        <input type="text" name="oneclick_mod_tel" class="form-control phone" placeholder="{Телефон}" required="">
                    </div>
                    <div class="form-group">
                        <input type="email" name="oneclick_mod_mail" class="form-control" placeholder="E-mail" required="">
                    </div>
					
                    @oneclick_captcha@
                        <div class="form-group">
                            <p class="small">
                            <input type="checkbox" value="on" name="rule" class="req" required=""> 
                            {Я согласен}  <a href="/page/soglasie_na_obrabotku_personalnyh_dannyh.html">{на обработку моих персональных данных}</a>
                            </p>
                        </div>              </div>
                <div class="modal-footer">
                    <input type="hidden" name="oneclick_mod_product_id" value="@productUid@">
                    <input type="hidden" name="oneclick_mod_send" value="1">
                    <input type="hidden" name="ajax" value="1">
                    <button type="button" class="btn btn-soft-primary transition-3d-hover" data-dismiss="modal">{Закрыть}</button>
                    <button type="submit" class="btn btn-primary transition-3d-hover">{Купить в 1 клик}</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <div class="mb-4">
	      <div class="mb-4">
            <button data-toggle="modal" data-target="#oneClickModal@productUid@"  type="button" class="btn btn-block btn-soft-primary btn-pill transition-3d-hover">{Купить в 1 клик}</button><script>$(document).ready($('#oneClickModal@productUid@').appendTo($('body')));</script>
    </div>

          </div>
