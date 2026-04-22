<div id="visualcart_content">
    <div id="visualcart">
       @php if(!empty($_SESSION['cart'])) echo $GLOBALS['SysValue']['other']['visualcart_list'];  php@ 
    </div>
    <div class="toolbar-dropdown-group">
        <a class="btn btn-sm btn-block btn-success" href="/order/">
                {ќформить заказ}
        </a>
    </div>
</div>