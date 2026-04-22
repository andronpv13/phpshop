<div id="allspec"> @user_error@ </div>
<form name="users_password" method="post">
  <table style="padding-top: 10px;padding-bottom: 10px">
    <tr>
      <td style="width:180px;">E-mail:</td>
      <td style="width:10px;"></td>
      <td><input type="text" name="login_new" value="@user_login@" style="width:250px;" >
        <img src="images/shop/flag_green.gif" alt="" width="16" height="16"  hspace="5" align="absmiddle"></td>
    </tr>
    <tr>
      <td>{Пароль}:</td>
      <td  style="width:10px;"></td>
      <td><input type="Password" name="password_new" style="width:250px;" value="@user_password@">
        <img src="images/shop/flag_green.gif" alt="" width="16" height="16" hspace="5" align="absmiddle"> <br>
        <span id="password" style="display: none;">
        <input type="Password" name="password_new2" style="width:250px;" value="">
        ({Повторите пароль})</span> </td>
    </tr>
    <tr>
      <td></td>
      <td  style="width:10px;"></td>
      <td style="height:45px;"><input type="checkbox" id="password_chek" value="1" name="password_chek" onclick="DispPasDiv()">
        {Изменить авторизацию}&nbsp;&nbsp;&nbsp;
        <input type="hidden" value="1" name="update_password">
        <input type="button" value="Изменить" onclick="UpdateUserPassword()">
      </td>
    </tr>
  </table>
</form>
<form name="users_data" method="post">
  <input type="hidden" name="name_new" value="@user_name@">
  <input type="hidden" name="mail_new" value="@user_mail@">
</form>
