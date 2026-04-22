
<ul class="account-dropdown">
	<li class="dropdown user-dropdown hidden-xs hidden-sm" role="presentation">
	    <a href="#" class="dropdown-toggle link" data-toggle="dropdown" role="button" aria-expanded="false"><i class="icons-user" title="{Войти}"></i></a>
	    <ul class="dropdown-menu dropdown-menu-right" role="menu">
	        <li><a href="/users/">{Персональные данные}</a></li>
	        <li class="@hideCatalog@"><a href="/users/order.html">{Отследить заказ}</a></li>
	        <li><a href="/users/message.html">{Связь с менеджерами}</a></li>
                <li class="@hideCatalog@"><a href="/users/notice.html">{Уведомления}</a></li>
	        <li><a href="?logout=true">{Выход}</a></li>
	    </ul>
	</li>
	<li class="nav-link">
		<a href="/users/"><i class="fa fa-user" title="{Войти}"></i> <span class="text">@UsersName@</span></a>
	</li>
</ul>