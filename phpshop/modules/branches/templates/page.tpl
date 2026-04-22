<link href="phpshop/modules/branches/templates/style.css" rel="stylesheet">
<div class="page-header">
    <h1 class="main-heading2">@branches_page_title@</h1>
</div>
<div class="content-bg-fix branches-page">
    <div class="btn-group">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" data-target="#" aria-expanded="false">
            <span id="currentBranchCity">@branches_current_city_name@</span> <span class="caret"></span>
        </button>
        <ul class="dropdown-menu branches-cities-list" role="menu">
            @branches_cities@
        </ul>
    </div>
    <div class="well">
        <div class="row">
            <div class="col-md-12">
                <h3 class="branches-current-city">@branches_current_city_name@</h3>
            </div>
        </div>
        <div class="row">
            @branches_branches@
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div id="branches-map-container"></div>
        </div>
    </div>
</div>
<script type="text/javascript" src="https://api-maps.yandex.ru/2.1/?apikey=@branches_yandex_key@&lang=ru_RU"></script>
<script type="text/javascript" src="phpshop/modules/branches/templates/script.js"></script>
<script>
    $(document).ready(function () {
        var BranchesModuleInstance = new BranchesModule();
        BranchesModuleInstance.init({
            branches: $.parseJSON('@branches_branches_coords@')
        });
    });
</script>