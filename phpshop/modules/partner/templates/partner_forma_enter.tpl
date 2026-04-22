<form method="post" action="@ShopDir@/partner/" class="form-horizontal" role="form">
    <div class="form-group">
        <label class="col-xs-12 col-sm-2 control-label">{Баланс}</label>
        <div class="col-xs-4">
            <span class="btn btn-default"> @userMoney@</span>
        </div>
    </div>
     <div class="form-group">
        <label class="col-xs-12 col-sm-2 control-label">{Имя}</label>
        <div class="col-xs-8 col-md-4">
            <input type="text" class="form-control" name="name" value="@userName@" required="">
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-2 control-label">Email</label>
        <div class="col-xs-8 col-md-4">
            <input type="email" class="form-control" name="login" value="@userMail@" required="">
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-2 control-label">{Реквизиты}</label>
        <div class="col-xs-8 col-md-4">
            <textarea class="form-control" name="content">@userContent@</textarea>
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-2 control-label">{Пароль}</label>
        <div class="col-xs-8 col-md-4">
            <input type="password" class="form-control" name="password" value="@userPassword@" required="">
        </div>
    </div>
     <div class="form-group">
        <label class="col-xs-12 col-sm-2 control-label">{Ссылка}</label>
        <div class="col-xs-8 col-md-4">
            <code>http://@serverName@@ShopDir@/?partner=@partnerId@</code>
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-2 control-label">HTML {код}</label>
        <div class="col-xs-8 col-md-4">
            <textarea class="form-control"><a href="http://@serverName@@ShopDir@/?partner=@partnerId@" title="@name@" target="_blank">@name@</a></textarea>
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-2 control-label"></label>
        <div class="col-xs-8 col-md-4">
            <input type="submit" class="btn btn-default" name="exit_user" value="{Выход}">
            <input type="submit"  class="btn btn-primary pull-right" name="update_user" value="{Сохранить изменение}">
        </div>
    </div>
</form>