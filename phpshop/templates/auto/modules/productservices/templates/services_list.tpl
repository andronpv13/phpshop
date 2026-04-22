<link href="phpshop/modules/productservices/templates/css/style.css" rel="stylesheet">
<style>
    .product-services-list input[type="checkbox"]:checked {
    border: 1px solid var(--blue);
    background-color: var(--blue);
}
</style>
<script src="phpshop/modules/productservices/js/productservices.js"></script>
<div class="product-services-list">
    <ul>
        @productservices_service@
    </ul>
</div>