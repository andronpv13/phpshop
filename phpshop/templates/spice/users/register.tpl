<section class="registration-area user-page wrapper-fix">
    <div class="col-sm-12">
        <div class="row">
            <!-- Registration Block Starts -->
            <div class="panel panel-smart">
                <div class="panel-heading">
                    <h3 class="panel-title">{Персональная информация}</h3>
                </div>
                <div class="panel-body">
                    <!-- Registration Form Starts -->
                    <form method="post" name="user_forma_register" >
                        <span id="user_error">@user_error@</span>
                        <div class="form-group">
                            <div class="">
                                <input placeholder="{Имя}" type="text"  name="name_new" value="@php echo $_POST['name_new']; php@"  class="form-control" required="" >
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="">
                                <input placeholder="E-mail" type="email" name="login_new" value="@php echo $_POST['login_new']; php@" class="form-control" required="" >
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="">
                                <input type="tel" name="tel_new" placeholder="{Телефон}"  value="@php echo $_POST['tel_new']; php@" class="form-control" @sms_login_control@>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="">
                                <input placeholder="{Пароль}" type="password" name="password_new"  class="form-control"  required="" >
                            </div>
                        </div>
                        <div class="form-group" id="check_pass">
                            <div class="">
                                <input placeholder="{Повторите пароль}" type="password" name="password_new2"  class="form-control" required="">
                                <span class="glyphicon glyphicon-remove form-control-feedback hide" aria-hidden="true"></span>
                            </div>
                            
                        </div>
                        
                        <div class="form-group">
                            <div class="">
                               @captcha@
                            </div>
                        </div>
<p class="small"><label><input type="checkbox" value="on" name="rule" class="req"  required>  {Я согласен} <a href="/page/soglasie_na_obrabotku_personalnyh_dannyh.html">{на обработку моих персональных данных}</a></label></p>
                        <div class="form-group">
                            <div class="">
                                <input type="hidden" value="1" name="add_user">
                                <button type="reset" class="btn btn-main">{Очистить}</button>
                                <button type="submit" class="btn btn-main">{Регистрация пользователя}</button>
                            </div>
                        </div>
                    </form>
                    <!-- Registration Form Starts -->
                </div>
            </div>
            <!-- Registration Block Ends -->
        </div>
    </div>
</section>