
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
<div class="quant-parrent hide">
    <label class="control-label text-uppercase @elementCartOptionHide@">{Количество}</label>
    <div class="quant input-group @elementCartOptionHide@">
        <span class="input-group-btn">
            <button type="button" class="btn btn-default btn-default_l btn-number"  data-type="minus" data-field="quant[2]">
                <span class="feather iconz-minus"></span>
            </button>
        </span>
        <input type="text" name="quant[2]" class="form-control form-control_gr input-number" value="1" min="1" max="100">
        <span class="input-group-btn">
            <button type="button" class=" btn btn-default btn-default_r btn-number" data-type="plus" data-field="quant[2]">
                <span class="feather iconz-plus"></span>
            </button>
        </span>
    </div>
</div>