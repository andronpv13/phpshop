<ol class="breadcrumb hidden-xs" itemscope itemtype="http://schema.org/BreadcrumbList">
	<li itemscope itemtype="http://schema.org/ListItem">
		<a href="/" itemprop="item">
			<span itemprop="name">{Главная}</span>
		</a>
		<meta itemprop="position" content="1" />
	</li>
    <li class="active"><b>{Ваша корзина}</b></li>
</ol>

<style type="text/css">

		.order-page-sidebar-user-block {
			display: block;
		}
		.sidebar-right .side-heading, .sidebar-right .sidebar-nav, .sidebar-right #faset-filter, .sidebar-right .panel.panel-default {
			display: none;
		}
		.main-container{padding-top: 5px;margin-top: 0px;}
</style>



		<div class="col-xs-12 col-lg-12">
			<div class="row top-button-order-row">
				<div class="pull-left">
						<h1 class="main-heading2">{Заказ} &#8470;@orderNum@</h1>
				</div>
				<div class="pull-right">
					<a href="?cart=clean" class="btn btn-main"><span class="glyphicon glyphicon-remove"></span> {Очистить корзину}</a> 
				    <a href="phpshop/forms/cart/index.html" target="_blank" class="btn btn-main hidden-xs"><span class="glyphicon glyphicon-print"></span> {Печатная форма корзины}</a>
				</div>
			</div>
			<div class="row order-row-fix">
				<div class="table-responsive order-page-table-wrapper img_fix">
			    	@orderContentCart@
			    </div>
			</div>
			<div class="row order-bottom-content">
				@orderContent@
			</div>
		</div>
