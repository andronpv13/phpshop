<div style="text-align:left;" class="paymOneEl @paymentActive@">
    <div style="float:none">
        <label>
            <input type="radio" value="@paymentId@" name="order_metod" id="order_metod" data-option="payment@paymentId@" @paymentChecked@>
            &nbsp;<img src="@paymentIcon@" title="@paymentTitle@" height="30" class="@php __hide('paymentIcon'); php@">&nbsp;@paymentTitle@
        </label>
    </div>
</div>