<ol class="breadcrumb hidden-xs" itemscope itemtype="http://schema.org/BreadcrumbList">
    <li itemscope itemtype="http://schema.org/ListItem">
        <a href="/" itemprop="item">
            <span itemprop="name">{Главная}</span>
        </a>
        <meta itemprop="position" content="1" />
    </li>
    <li class="active">@sortName@</li>
</ol>

<div class="page-header">
    <h1>@sortName@</h1>
</div>



<div> @sortDes@ </div>

<div class="product-filter" id="filter-selection-well">
    <div class="row">
        <div class="col-md-6 col-sm-6  col-xs-12">
            <div class="display d-flex">
                {Сначала}: <div class="filter-menu-wrapper">
                    <div class="btn-group filter-menu" data-toggle="buttons">

                        <label class="btn btn-sm btn-sort @fSetCactive@">
                            <input type="radio" name="s" value="=3" data-url="?@productVendor@s=3" @fSetCchecked@> {Популярные} 
                        </label>

                        <label class="btn btn-sm btn-sort @fSetBactive@" >
                            <input type="radio" name="s" value="2&f=2" data-url="?@productVendor@s=2&f=2" @fSetBchecked@> {Дорогие}
                        </label>
                        <label class="btn btn-sm btn-sort @fSetAactive@" >
                            <input type="radio" name="s" value="2&f=1" data-url="?@productVendor@s=2&f=1" @fSetAchecked@> {Дешевые}
                        </label>
                    </div>
                </div>
            </div>
        </div> 
 <div class="col-md-6 hidden-xs text-right">
           

           <div class="display" data-toggle="buttons">
                <label class="btn btn-sm fal fa-bars btn-sort @gridSetAactive@" data-toggle="tooltip" data-placement="top" title="{Товары списком}">
                    <input type="radio" name="gridChange" value="1"  autocomplete="off" data-url="?@productVendor@@php if(isset($_GET['f'])) echo '&f='.$_GET['f']; if(isset($_GET['s'])) echo  '&s='.$_GET['s']; php@&gridChange=1">
                </label>
                <label class="btn btn-sm fal fa-th btn-sort glyphicon glyphicon-th @gridSetBactive@" data-toggle="tooltip" data-placement="top" title="{Товары сеткой}">
                    <input type="radio" name="gridChange" value="2" autocomplete="off" data-url="?@productVendor@@php if(isset($_GET['f'])) echo '&f='.$_GET['f']; if(isset($_GET['s'])) echo  '&s='.$_GET['s']; php@&gridChange=2">
                </label>
            </div> 

        </div>

      
    </div> 
    <a name="sort"></a>  
</div>
<div class="template-product-list">

@productPageDis@
</div>
<div class="clearfix"></div>
@productPageNav@
