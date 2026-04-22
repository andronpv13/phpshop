<div id="allspec">
    @user_error@
</div>

<!-- Card -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title">{Настройки}</h5>
    </div>

    <!-- Body -->
    <div class="card-body">

        <!-- Form -->
        <form name="users_password" method="post" class="form-horizontal">

            <!-- Form Group -->
            <div class="row form-group @hideCatalog@">
                <label for="currentPasswordLabel" class="col-sm-3 col-form-label input-label">{Статус}</label>

                <div class="col-sm-9 p-2 text-primary">
                    @user_status@
                </div>
            </div>
            <!-- End Form Group -->

            <!-- Form Group -->
            <div class="row form-group @hideCatalog@">
                <label for="currentPasswordLabel" class="col-sm-3 col-form-label input-label">{Скидка}</label>

                <div class="col-sm-9 p-2 text-primary">
                    @user_cumulative_discount@ %
                </div>
            </div>
            <!-- End Form Group -->

            <!-- Form Group -->
            <div class="row form-group @php __hide('user_bonus'); php@ @hideCatalog@">
                <label for="currentPasswordLabel" class="col-sm-3 col-form-label input-label">{Бонусы}</label>

                <div class="col-sm-9 p-2 text-primary">
                    @user_bonus@ <span class="rubznak">@productValutaName@</span>
                </div>
            </div>
            <!-- End Form Group -->

            <!-- Form Group -->
            <div class="row form-group">
                <label for="currentPasswordLabel" class="col-sm-3 col-form-label input-label">{Имя}</label>

                <div class="col-sm-9">
                    <input type="text" class="form-control" name="name_new" value="@user_name@">
                </div>
            </div>
            <!-- End Form Group -->

            <!-- Form Group -->
            <div class="row form-group">
                <label for="currentPasswordLabel" class="col-sm-3 col-form-label input-label">Email</label>

                <div class="col-sm-9">
                    <input type="email" name="login_new" class="form-control" value="@user_login@" required="">
                </div>
            </div>
            <!-- End Form Group -->

            <!-- Form Group -->
            <div class="row form-group">
                <label for="currentPasswordLabel" class="col-sm-3 col-form-label input-label">{Телефон}</label>

                <div class="col-sm-9">
                    <input type="tel" class="form-control" name="tel_new" value="@user_tel@" @sms_login_control@>
                </div>
            </div>
            <!-- End Form Group -->

            <!-- Form Group -->
            <div class="row form-group">
                <label for="currentPasswordLabel" class="col-sm-3 col-form-label input-label">{Рассылка}</label>

                <div class="col-sm-9 p-2">

                    <!-- Custom Checkbox -->
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="deleteAccountCheckbox" name="sendmail_new" value="1" @user_sendmail_checked@>
                        <label class="custom-control-label" for="deleteAccountCheckbox">{Отказаться от новостных рассылок}</label>
                    </div>
                    <!-- End Custom Checkbox -->

                </div>
            </div>
            <!-- End Form Group -->

            <!-- Form Group -->
            <div class="row form-group">
                <label for="currentPasswordLabel" class="col-sm-3 col-form-label input-label">{Пароль}</label>

                <div class="col-sm-9">
                    <input type="password" class="form-control" name="password_new" value="@user_password@" required="">
                </div>
            </div>
            <!-- End Form Group -->

            <div class="justify-content-end">
                <input type="hidden" value="1" name="update_user">
                <button type="submit" class="btn btn-primary transition-3d-hover">{Сохранить изменение}</button>
            </div>
        </form>
        <!-- End Form -->
    </div>
    <!-- End Body -->
</div>
<!-- End Card -->