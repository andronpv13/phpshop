<h1 class="h2 d-none d-sm-block">{Расширенный поиск}</h1> 

<div class="">
    <form  action="/search/" >

        <div class="input-group">
            <input name="words" maxlength="50" class="form-control form-control-lg" placeholder="{Искать}.." required="" type="search" value="@searchString@">
            <div class="input-group-append">
                <button type="submit" class="btn black-hover-btn">{Поиск}</button>
            </div>
        </div>

        <div class="input-group mt-3 @hideSearchType@">

            <select class="form-control" name="cat" id="cat">
               @searchPageCategory@
            </select>
            
            <div class="btn-group btn-group-toggle  @hideSearchType@" data-toggle="buttons">

                <label class="btn btn-radio black-hover-btn @searchSetCactive@">
                    <input type="radio" name="pole" value="1" autocomplete="off" @searchSetC@> {Наименование}
                </label>
                <label class="btn btn-radio black-hover-btn @searchSetDactive@">
                    <input type="radio" name="pole" value="2" autocomplete="off" @searchSetD@> {Учитывать все}
                </label>
            </div>
        </div>

    </form>
</div>
<div class="search-misspell">@searchMisspell@</div>
<div class="template-product-list row mt-5">

    @productPageDis@

</div>
<div class="clearfix"></div>
@searchPageNav@
