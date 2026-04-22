<div id=allspec>
    <img src="images/shop/icon_security.gif" alt="" width="16" height="16" border="0" hspace="5" align="absmiddle"><b>{Восстановление пароля}</b>
</div>
<form method="post" name="userpas_forma">
    <table>

        <tr>
            <td>Логин:</td>
            <td><input type="text" name="login" maxlength="30"></td>
            <td><input type="button" value="{Выслать}" onclick="ChekUserSendForma()">
                <input type="hidden" value="1" name="passw_send"></td>
        </tr>

    </table>
</form>

<div  id=allspecwhite><img src="images/shop/comment.gif" alt="" width="16" height="16" border="0" hspace="5" align="absmiddle">{Укажите свой <b>емейл</b> для отправки пароля на ваш адрес электронной почты}.<br>
    @user_message@
</div>
