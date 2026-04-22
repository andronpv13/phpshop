<form method="post" name="userpas_forma" class="form-inline">
    <div class="input-group mb-3">
        <input type="email" name="login" value="@php echo $_POST['login']; php@" class="form-control" required="" placeholder="E-mail">
        <div class="input-group-append">
            <input type="hidden" value="1" name="passw_send">
            <button type="submit" class="btn btn-primary">{Выслать пароль}</button>
        </div>
    </div>
</form>