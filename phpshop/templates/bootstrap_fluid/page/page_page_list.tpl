<ol class="breadcrumb hidden-xs" itemscope itemtype="http://schema.org/BreadcrumbList">
    @breadCrumbs@
</ol>
<div class="clearfix"></div>
<div class="page-header ">
    <h2>@pageTitle@</h2>
</div>

@catContent@
<div class="grid main-grid page-list">
<p>@pageContent@</p></div>
<div class="clearfix"></div>
<hr class="@php __hide('pageLast'); php@">
<h3 class="@php __hide('pageLast'); php@  ">{Интересно почитать}</h3>
<br>
<div class="grid row">@pageLast@</div>
<div class="page-header">
    <h3>@productOdnotip@</h3>
</div>
<div class="odnotip">
@productOdnotipList@
</div>


