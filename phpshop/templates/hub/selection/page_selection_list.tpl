<h1 class="page-title hide">@sortName@</h1>

<div class="catalog-description-fix" style="display: none;"> @sortDes@ </div>
<style type="text/css">.sidebar-right{display:none}.middle-content-fix{width:100%}</style>
<div class="product-filter" id="filter-selection-well">
    <div class="row">
        <div class="col-md-6">
                <div class="display" data-toggle="buttons">
                    <label class="btn btn-sm btn-sort glyphicon glyphicon-th-list @gridSetAactive@" data-toggle="tooltip" data-placement="top" title="{Товары списком}">
                        <input type="radio" name="gridChange" value="1"  autocomplete="off" data-url="?@productVendor@@php if(isset($_GET['f'])) echo '&f='.$_GET['f']; if(isset($_GET['s'])) echo  '&s='.$_GET['s']; php@&gridChange=1">
                    </label>
                    <label class="btn btn-sm btn-sort glyphicon glyphicon-th @gridSetBactive@" data-toggle="tooltip" data-placement="top" title="{Товары сеткой}">
                        <input type="radio" name="gridChange" value="2" autocomplete="off" data-url="?@productVendor@@php if(isset($_GET['f'])) echo '&f='.$_GET['f']; if(isset($_GET['s'])) echo  '&s='.$_GET['s']; php@&gridChange=2">
                    </label>

                    <label class="control-label"></label>
                </div> 

        </div>
        <div class="col-md-6 text-right">
            
            <div class="display filter-well-right-block">

                <label class="control-label"></label>
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-sm btn-sort glyphicon glyphicon-signal @sSetCactive@" data-toggle="tooltip" data-placement="top" title="{Рейтинг}">
                        <input type="radio" name="s" value="3" autocomplete="off" data-url="?@productVendor@&s=3@php if(isset($_GET['f'])) echo  '&f='.$_GET['f']; if(isset($_GET['gridChange'])) echo '&gridChange='.$_GET['gridChange']; php@">
                    </label>
                    <label class="btn btn-sm btn-sort glyphicon glyphicon-sort-by-alphabet @sSetBactive@" data-toggle="tooltip" data-placement="top" title="{Наименование}">
                        <input type="radio" name="s" value="1"  autocomplete="off" data-url="?@productVendor@&s=1@php if(isset($_GET['f'])) echo  '&f='.$_GET['f']; if(isset($_GET['gridChange'])) echo '&gridChange='.$_GET['gridChange']; php@">
                    </label>
                    <label class="btn btn-sm btn-sort glyphicon glyphicon glyphicon-sort-by-order @sSetAactive@" data-toggle="tooltip" data-placement="top" title="{Цена}">
                        <input type="radio" name="s" value="2" autocomplete="off" data-url="?@productVendor@&s=2@php if(isset($_GET['f'])) echo  '&f='.$_GET['f']; if(isset($_GET['gridChange'])) echo '&gridChange='.$_GET['gridChange']; php@">
                    </label>

                </div>    

                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-sm btn-sort glyphicon glyphicon-sort-by-attributes @fSetAactive@" data-toggle="tooltip" data-placement="top" title="{По возрастанию}">
                        <input type="radio" name="f" value="1"  autocomplete="off" data-url="?@productVendor@&f=1@php if(isset($_GET['s'])) echo  '&s='.$_GET['s']; if(isset($_GET['gridChange'])) echo '&gridChange='.$_GET['gridChange']; php@">
                    </label>
                    <label class="btn btn-sm btn-sort glyphicon glyphicon-sort-by-attributes-alt @fSetBactive@" data-toggle="tooltip" data-placement="top" title="{По убыванию}">
                        <input type="radio" name="f" value="2" autocomplete="off" data-url="?@productVendor@&f=2@php if(isset($_GET['s'])) echo  '&s='.$_GET['s']; if(isset($_GET['gridChange'])) echo '&gridChange='.$_GET['gridChange']; php@">
                    </label>
                </div>  
            </div>

        </div>
    </div> 
</div>


<div class="template-product-list products-list">@productPageDis@</div>

@productPageNav@
