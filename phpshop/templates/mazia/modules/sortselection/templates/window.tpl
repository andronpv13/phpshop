<!-- Модальное окно SortSelection-->
<div class="modal fade " id="sortSelectionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">@sortSelectionName@</h4>
                <span id="usersError" class="hide"></span>
            </div>
            <form method="get" action="/selection/">
                <div class="modal-body">
                    @sortSelectionContent@
                </div>
                <div class="modal-footer">
                    <input class="btn btn-default" value="{Сбросить}" type="reset">
                    <input class="btn btn-primary" value="{Показать}" type="submit">
                </div>
            </form>
        </div>
    </div>
</div>
<a href="#" data-toggle="modal" data-target="#sortSelectionModal"><h2><span class="glyphicon glyphicon-check" style="padding-right:5px"></span>@sortSelectionName@</h2></a>
<!--/ Модальное окно SortSelection-->