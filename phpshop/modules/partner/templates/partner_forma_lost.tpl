<p>@usersError@</p>
<form method="post" name="user_forma" action="/partner/" class="template-sm">
    <div class="form-group">
        <input type="email"  placeholder="E-mail" name="login" value="@php echo $_POST['login']; php@" class="form-control" required="" >
    </div>
    <input class="btn btn-primary " type="submit" name="send_user" value="{Выслать}">
</form>