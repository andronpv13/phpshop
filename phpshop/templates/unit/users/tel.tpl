@user_sms_error@
<form method="post" name="userpas_forma" class="form-inline">
    <div class="form-group">
        <input type="tel" name="tel" value="@php echo $_POST['tel']; php@" class="form-control" required="" placeholder="Телефон">
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-primary">{Отправить код на телефон}</button>
   </div>
</form>
<p><br></p>