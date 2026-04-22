<!-- Product Content -->
<a class="h6 text-left" href="/shop/UID_@productUid@.html" title="@productName@">
    <div class=" pb-2 mb-2 w-80">
        <div class="media">
            <div class="avatar avatar-lg mr-3">
                <img class="avatar-img" src="@productImg@" alt="@productName@">
                <sup class="avatar-status avatar-primary @php __hide('productWarehouse'); php@">@productWarehouse@</sup>
            </div>
            <div class="media-body">
                <a class="h6 text-left" href="/shop/UID_@productUid@.html" title="@productName@">@productName@</a>
                <div class="text-body font-size-1">
                    <span>{Цена}:</span>
                    <span>@productPrice@<span class="rubznak">@productValutaName@</span></span>
                </div>
            </div>
        </div>
    </div>
</a>
<!-- End Product Content -->