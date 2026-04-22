<div id="allspec">
    @user_error@
</div>
<p><br></p>
<form name="users_password" method="post" class="form-horizontal">

    <div class="form-group hidden-lg @hideCatalog@">
        <div class="col-xs-4">
            <a class="btn btn-info" href="/users/order.html"><span class="glyphicon glyphicon-shopping-cart"></span> {Мои заказы}</a>
        </div>
    </div>

    <div class="form-group @hideCatalog@">
        <label class="col-xs-12 col-sm-2 control-label">{Статус}</label>
        <div class="col-xs-4">
            <span class="btn btn-default"><span class="glyphicon glyphicon-user"></span> @user_status@</span>
        </div>
    </div>

    <div class="form-group @hideCatalog@">
        <label class="col-xs-12 col-sm-2 control-label">{Скидка}</label>
        <div class="col-xs-4">
            <span class="btn btn-warning">@user_cumulative_discount@ %</span>
        </div>
    </div>
    <div class="form-group @php __hide('user_bonus'); php@ @hideCatalog@">
        <label class="col-sm-2 col-xs-12 control-label">{Бонусы}</label>
        <div class="col-xs-4">
            <span class="btn btn-warning">@user_bonus@ <span class="rubznak">@productValutaName@</span></span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-2 control-label">Email</label>
        <div class="col-xs-4">
            <input type="email" class="form-control input-width-fix" value="@user_login@" required="">
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-2 control-label">{Телефон}</label>
        <div class="col-xs-8 col-md-4">
            <input type="tel" class="form-control" name="tel_new" value="@user_tel@" @sms_login_control@>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label visible-lg">{Рассылка}</label>
        <div class="checkbox col-xs-10">
            <label>
                <input type="checkbox" name="sendmail_new" value="1" @user_sendmail_checked@> {Отказаться от новостных рассылок}
            </label>
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-12 col-sm-2 control-label">{Пароль}</label>
        <div class="col-xs-4">
            <input type="password" class="form-control input-width-fix" name="password_new" value="@user_password@" required="">
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-2 control-label"></label>
        <div class="col-xs-4">
            <input type="hidden" value="1" name="update_user">
            <button type="submit"  class="btn btn-primary">{Сохранить изменение}</button>

        </div>
          <div class="col-xs-12 hidden-lg">
              <br><a class="btn btn-default" href="?logout=true">{Выход}</a>
        </div>
    </div>
</form>
