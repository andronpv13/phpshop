@user_sms_error@
<form method="post" name="userpas_forma" class="form-inline">
    <div class="input-group mb-3">
        <input type="text" name="token" value="@php echo $_POST['token']; php@" class="form-control" required="" placeholder="{Код}">
        <input type="hidden" value="@userTel@" name="tel">
        <div class="input-group-append"><button type="submit" class="btn btn-primary">{Подтвердить код}</button></div>
   </div>
</form>
