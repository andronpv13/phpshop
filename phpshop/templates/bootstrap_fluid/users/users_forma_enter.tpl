<li class="dropdown " role="presentation">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><span class="glyphicon glyphicon-user"></span> @UsersName@<span class="caret"></span></a>
    <ul class="dropdown-menu dropdown-menu-right" role="menu">
        <li class="dropdown-header">@UsersLogin@</li>
        <li><a href="/users/">{Персональные данные}</a></li>
        <li class="@hideCatalog@"><a href="/users/order.html">{Отследить заказ}</a></li>
        <li><a href="/users/message.html">{Связь с менеджерами}</a></li>
        <li><a href="/users/notice.html">{Уведомления}</a></li>
        <li class="divider"></li>
        <li class="dropdown-header">{Авторизация}</li>
        <li><a href="?logout=true">{Выход}</a></li>
    </ul>
</li>