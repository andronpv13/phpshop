
    <h1 class="page-title hide">@formaTitle@</h1>


<style type="text/css">
	table {
	max-width: 465px;
    margin: 0 auto;
    width: 100%;
	}
	table input.form-control,
	table textarea.form-control {
		width: 100% !important;
	}
	.sidebar-right .side-heading, .sidebar-right .sidebar-nav, .sidebar-right #faset-filter, .sidebar-right .panel.panel-default {
			display: none;
	}
	.middle-content-fix{padding-top: 0}
	.registration-area .panel-smart{padding-top: 1px}
	.main-container{padding-top: 0px;margin-top: 0;}
	.main-container > .row {
			margin-left: 0;
			margin-right: 0;
			padding-top: 25px;
			background-color: #fff;margin-top: 0px;
	}
	.order-page-sidebar-user-block,
	.sidebar-right .user-title.side-heading {
		display: block;
	}
</style>
<div class="mobile-user-button hidden-md hidden-lg">
	<ul>
		<li><a class="btn btn-main" href="/users/order.html">{Отследить заказ}</a></li>
	    <li><a class="btn btn-main" href="/users/notice.html">{Уведомления о товарах}</a></li>
	    <li><a class="btn btn-main" href="/users/message.html">{Связь с менеджерами}</a></li>
	    @php if($_SESSION['UsersId']) echo '<li><a class="btn btn-main" href="?logout=true">{Выйти}</a></li>'; php@
	</ul>
</div>
@formaContent@ 