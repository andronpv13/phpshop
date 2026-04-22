
@user_sms_error@
<form role="form" method="post" name="userpas_forma" class="form-inline">
    <div class="form-group">
        <input type="text" name="token" value="@php echo $_POST['token']; php@" class="form-control" required="" placeholder="{Код}">
    </div>
    <div class="form-group">
        <input type="hidden" value="@userTel@" name="tel">
        <button type="submit" class="btn btn-primary">{Подтвердить код}</button>
   </div>
</form>
<p><br></p>