<h1 class="h2 page-title d-none">{Расширенный поиск}</h1> 


<div class="">
    <form  action="/search/">

        <div class="input-group">
            <input name="words" maxlength="50" class="form-control" placeholder="{Искать}.." required="" type="search" value="@searchString@">
            <div class="input-group-append">
                <button type="submit" class="btn btn-primary">{Поиск}</button>
            </div>
        </div>

        <div id="helpBlock" class="space-top-1 space-bottom-1 @hideSite@">

            <!-- Select -->
            <select class="js-custom-select custom-select" name="cat" size="1" style="opacity: 0;"
                    data-hs-select2-options='{
                    "minimumResultsForSearch": "Infinity",
                    "width": "500"
                    }'>
                @searchPageCategory@
            </select>
            <!-- End Select -->


            <div class="btn-group btn-group-toggle @hideSearchType@" data-toggle="buttons">
                <label class="btn btn-soft-primary @searchSetCactive@">
                    <input type="radio" name="pole" value="1" autocomplete="off" @searchSetC@> {Наименование}
                </label>
                <label class="btn btn-soft-primary @searchSetDactive@">
                    <input type="radio" name="pole" value="2" autocomplete="off" @searchSetD@> {Учитывать все}
                </label>
            </div>
        </div>
        <br>
    </form>
</div>
<div class="search-misspell">@searchMisspell@</div>
<div class="template-product-list row">

    @productPageDis@

</div>
<div class="clearfix"></div>
@searchPageNav@
