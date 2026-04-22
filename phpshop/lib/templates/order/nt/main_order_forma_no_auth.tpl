<div class="checkout-content" style="display: block;">
    <div class="left">
        <br>
        <span style="color:red">@user_error@</span>
        <span class="required">*</span> <b>E-Mail:</b><br>
        <input type="text" class="req"  name="mail" value="@php echo $_POST['mail']; php@">
        <br>
        <br>
        <span class="required">*</span> <b>Ваше имя:</b><br>
        <input type="text" class="req"  name="name_new" value="@php echo $_POST['name_new']; php@">
        <br>
        <br>
    </div> 
    <div class="auth-hint">
        Если Вы - новый пользователь, то личный кабинет мы создадим за Вас и пришлём пароли на почту.<br>
        
    </div>
</div>