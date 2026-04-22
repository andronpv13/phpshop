<ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
	<li itemscope itemtype="http://schema.org/ListItem">
		<a href="/" itemprop="item">
			<span itemprop="name">{Главная}</span>
		</a>
		<meta itemprop="position" content="1" />
	</li>
	<li itemscope itemtype="http://schema.org/ListItem">
		<a href="/users/" itemprop="item">
			<span>{Личный кабинет}</span>
		</a>
		<meta itemprop="position" content="2" />
	</li>
	<li class="active"><b>@formaTitle@</b></li>
</ol>
<div class="page-header">
    <h1 class="main-heading2">@formaTitle@</h1>
</div>


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
	.order-page-sidebar-user-block {
			display: block;
		}
		.sidebar-right .side-heading, .sidebar-right .sidebar-nav, .sidebar-right #faset-filter, .sidebar-right .panel.panel-default {
			display: none;
		}
		.main-container{padding-top: 5px;margin-top: 0px;}
</style>
<div class="user-page wrapper-fix">@formaContent@ </div>