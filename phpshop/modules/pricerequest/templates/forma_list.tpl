<div class="modal fade bs-example-modal-sm oneclick-modal" id="pricerequestModal@productUid@" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">@pricerequest_name@</h4>
            </div>
            <form method="post" name="user_forma" action="@ShopDir@/pricerequest/">
                <div class="modal-body">

                    <div class="form-group">
                        <input type="text" name="pricerequest_mod_name" class="form-control" placeholder="{ФИО}" required="">
                    </div>
                     <div class="form-group">
                        <input type="email" name="pricerequest_mod_mail" class="form-control" placeholder="E-mail" required="">
                    </div>
                    <div class="form-group">
                        <input type="text" name="tel" class="form-control phone" placeholder="{Телефон}">
                    </div>
                     <div class="form-group">
                         <textarea name="pricerequest_mod_message" class="form-control" placeholder="{Комментарий}"></textarea>
                    </div>
                    <div class="form-group">
                        <p class="small">
                            <input type="checkbox" value="on" name="rule" class="req" required=""> 
                            {Я согласен}  <a href="/page/soglasie_na_obrabotku_personalnyh_dannyh.html">{на обработку моих персональных данных}</a>
                        </p>
                    </div>              </div>
                <div class="modal-footer">
                    <input type="hidden" name="pricerequest_mod_product_id" value="@productUid@">
                    <input type="hidden" name="pricerequest_mod_send" value="1">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{Закрыть}</button>
                    <button type="submit" class="btn btn-primary">@pricerequest_name@</button>
                </div>
            </form>
        </div>
    </div>
</div>
<a href="#" data-toggle="modal" data-target="#pricerequestModal@productUid@" class="btn btn-default btn-sm btn-block"> @pricerequest_name@</a>