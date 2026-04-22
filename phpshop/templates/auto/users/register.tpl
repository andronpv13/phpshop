<div id="usersError" class="d-none">@usersError@</div>
<form method="post" name="user_forma_register" class="template-sm col-lg-5 col-md-6 col-sm-7 col-xs-12">
    <span id="user_error">@user_error@</span>
    <div class="form-group">
        <input type="text"  placeholder="{Имя}" name="name_new" value="@php echo $_POST['name_new']; php@"  class="form-control" required="" >
    </div>
    <div class="form-group">
        <input type="email"  placeholder="E-mail" name="login_new" value="@php echo $_POST['login_new']; php@" class="form-control" required="" >
    </div>
    <div class="form-group">
        <input type="tel" name="tel_new" placeholder="{Телефон}"  value="@php echo $_POST['tel_new']; php@" class="form-control" @sms_login_control@>
    </div>
    <div class="form-group">
        <input placeholder="{Пароль}" type="password" name="password_new"  class="form-control"  required="" >
    </div>
    <div class="form-group" id="check_pass">
        <input type="password" placeholder="{Повторите пароль}" name="password_new2"  class="form-control" required="">
        <span class="glyphicon glyphicon-remove form-control-feedback hide" aria-hidden="true"></span>
    </div>

    <div>
        @captcha@
    </div>
    <br>
    <div class="form-group">
        <p class="small">
            <input type="checkbox" value="on" name="rule" class="req"  required> 
            {Я согласен}  <a href="/page/soglasie_na_obrabotku_personalnyh_dannyh.html">{на обработку моих персональных данных}</a>
        </p>
    </div>
    <p>
        <input type="hidden" value="1" name="add_user">
        <button type="reset" class="btn btn-soft-primary mb-2">{Очистить}</button>
        <button type="submit" class="btn btn-primary transition-3d-hover">{Регистрация пользователя}</button>
    </p>

</form>