<style>.left-catal {display:none}
.page-catal {display:block}
</style>
<h2 class="page-title hide">
   @pageTitle@
</h2>

@catContent@
<div class="grid main-grid page-list">
<p>@pageContent@</p></div>
<div class="clearfix"></div>
<hr class="@php __hide('pageLast'); php@">
<h3 class="@php __hide('pageLast'); php@  page-header text-left">{Интересно почитать}</h3>
<div class="grid">@pageLast@</div>
<p>@odnotipDisp@</p>
