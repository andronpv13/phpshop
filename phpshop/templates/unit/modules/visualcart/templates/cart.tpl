<div id="visualcart_content">
    <div class="list-group" id="visualcart">
       @php if(!empty($_SESSION['cart'])) echo $GLOBALS['SysValue']['other']['visualcart_list'];  php@ 
    </div>
    <div class="text-center" id="visualcart_order" style="@visualcart_order@">
        <a class="btn btn-info" href="/order/">{ќформить заказ}</a>
    </div>
</div>