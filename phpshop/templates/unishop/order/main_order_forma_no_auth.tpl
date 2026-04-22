
<div class="col-xs-12 col-sm-12">
        <div class="form-group">
            <span style="color:red">@user_error@</span>
        </div>
</div>
<div class="col-xs-12 col-sm-6">
        <div class="form-group">
            <input type="email" name="mail" class="form-control req" placeholder="E-mail..." required="" value="@php echo $_POST['mail']; php@">
        </div>
        <div class="form-group">
            <input type="text" name="name_new" class="form-control req"  placeholder="Имя..." required="" value="@php echo $_POST['name_new']; php@">
        </div>
        <div class="form-group">
            <input type="tel" name="tel_new" class="form-control @sms_login_enabled@"  placeholder="{Телефон}" required="" value="@php echo $_POST['tel_new']; php@">
        </div>
</div>
<div class="col-xs-12 col-sm-6">
    <div class="alert alert-warning">
        <span class="glyphicon glyphicon-info-sign"></span> {Если Вы - новый пользователь, то личный кабинет мы создадим за Вас и пришлём пароли на почту. Если Вы не авторизованы, мы узнаем Вас по емейлу и привяжем этот заказ к Вашему аккаунту}.<br>
         <label><input type="checkbox" value="on" name="rule" class="req" required="">  {Я согласен} <a href="/page/soglasie_na_obrabotku_personalnyh_dannyh.html">{на обработку моих персональных данных}</a>
                           </label>
    </div>
</div>