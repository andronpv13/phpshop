<div class="page-header hidden-xs">
    <h1 class="main-heading2">{Расширенный поиск}</h1>
</div>

<div class="well">
    <form  action="/search/">

        <div class="input-group">
            <input name="words" maxlength="50" class="form-control" placeholder="{Искать}.." required="" type="search" value="@searchString@">
            <div class="input-group-btn">
                <button type="submit" class="btn btn-info" tabindex="-1"><span class="glyphicon glyphicon-search"></span></button>
            </div>
        </div>
        <span id="helpBlock @hideCatalog@" class="help-block">

            <input type="hidden" value="0" name="cat" id="cat">

            <div class="btn-group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" data-target="#" aria-expanded="false">
                    <span id="catSearchSelect">{@currentSearchCat@}</span> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu" class="cat-menu-search">
                    @searchPageCategory@
                </ul>
            </div>
            <div class="btn-group @hideSearchType@" data-toggle="buttons">
                <label class="btn btn-default btn-sm @searchSetCactive@">
                    <input type="radio" name="pole" value="1" autocomplete="off" @searchSetC@> {Наименование}
                </label>
                <label class="btn btn-default btn-sm @searchSetDactive@">
                    <input type="radio" name="pole" value="2" autocomplete="off" @searchSetD@> {Учитывать все}
                </label>
            </div>
        </span>
    </form>
</div>
<div class="search-misspell">@searchMisspell@</div>
@productPageDis@

@searchPageNav@
