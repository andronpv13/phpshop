
<div class="product-page-select">
	<div>
		<label class="control-label">@parentListSizeTitle@</label>
		<div  id="parentSize">
			@parentListSize@
		</div>
	</div>

	<div>
		<label class="control-label">@parentListColorTitle@</label>
		<div id="parentColor">
			@parentListColor@
		</div>
	</div>
</div>

<span class="hide" id="parentSizeMessage">@parentSizeMessage@</span>

<div class="btn_buy_block @elementCartOptionHide@">
	<div class="quantity">
		<label class="control-label">{Количество}</label>
		<div class="quant input-group">
			<span class="input-group-btn">
				<button type="button" class="btn btn-default_l btn-number"  data-type="minus" data-field="quant[2]">-</button>
			</span>
			<input type="text" name="quant[2]" class="form-control form-control_gr input-number" value="1" min="1" max="100">
			<span class="input-group-btn">
				<button type="button" class=" btn btn-default_r btn-number" data-type="plus" data-field="quant[2]">+</button>
			</span>
		</div>
	</div>

	<div class="cart-button-wrapper">
		<button type="button" class="btn btn-cart addToCartFull" data-num="1" data-uid="@productUid@" data-cart="@productSaleReady@">{Купить}</button>
	</div>
</div>

