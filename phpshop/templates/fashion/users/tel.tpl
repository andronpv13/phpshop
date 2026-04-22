@user_sms_error@
<form method="post" name="userpas_forma" class="form-inline">
    <div class="input-group mb-3">
        <input type="tel" name="tel" value="@php echo $_POST['tel']; php@" class="form-control" required="" placeholder="Телефон">
        <div class="input-group-append"><button type="submit" class="btn btn-primary">{Отправить код}</button></div>
   </div>
</form>
