<li class="dropdown" role="presentation">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><img src="images/user.svg"> &nbsp; @UsersName@<span class="caret"></span></a>
    <ul class="dropdown-menu dropdown-menu-right" role="menu">
        <li class="dropdown-header">@UsersLogin@</li>
        <li><a href="/users/">{Настройки}</a></li>
        <li class="@hideCatalog@"><a href="/users/order.html">{Отследить заказ}</a></li>
        <li><a href="/users/message.html">{Диалоги}</a></li>
        <li class="@hideCatalog@"><a href="/users/notice.html">{Уведомления}</a></li>
        <li class="divider"></li>
        <li class="dropdown-header">{Авторизация}</li>
        <li><a href="?logout=true">{Выход}</a></li>
    </ul>
</li>