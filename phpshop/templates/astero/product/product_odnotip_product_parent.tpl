
<div>
    <label class="control-label text-uppercase">@parentListSizeTitle@</label>
    <div  id="parentSize">
        @parentListSize@
    </div>
</div>

<div>
    <label class="control-label text-uppercase">@parentListColorTitle@</label>
    <div id="parentColor">
        @parentListColor@
    </div>
</div>

<span class="hide" id="parentSizeMessage">@parentSizeMessage@</span>

<label class="control-label text-uppercase @elementCartOptionHide@ @hideCatalog@">{Количество}</label>
<div class="quant input-group @elementCartOptionHide@ @hideCatalog@">
    <span class="input-group-btn">
        <button type="button" class="btn btn-default btn-default_l btn-number"  data-type="minus" data-field="quant[2]">
            <span class="glyphicon glyphicon-minus"></span>
        </button>
    </span>
    <input type="text" name="quant[2]" class="form-control form-control_gr input-number" value="1" min="1" max="100">
    <span class="input-group-btn">
        <button type="button" class=" btn btn-default btn-default_r btn-number" data-type="plus" data-field="quant[2]">
            <span class="glyphicon glyphicon-plus"></span>
        </button>
    </span>
</div>