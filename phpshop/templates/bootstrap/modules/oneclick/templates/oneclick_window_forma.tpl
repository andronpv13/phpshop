<div class="modal fade bs-example-modal-sm oneclick-modal" id="oneClickModal@productUid@" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">{Быстрый заказ}</h4>
            </div>
            <form method="post" name="user_forma" action="@ShopDir@/oneclick/">
                <div class="modal-body">

                    <div class="form-group">
                       
                        <input type="text" name="oneclick_mod_name" class="form-control" placeholder="{Имя}" required="">
                    </div>
                    <div class="form-group">
                        
                        <input type="text" name="oneclick_mod_tel" class="form-control phone" placeholder="{Телефон}" required="">
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
                    <button type="button" class="btn btn-default" data-dismiss="modal">{Закрыть}</button>
                    <button type="submit" class="btn btn-primary">{Купить в 1 клик}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<a href="#" data-toggle="modal" data-target="#oneClickModal@productUid@" class="btn btn-default"> {Купить в 1 клик}</a>