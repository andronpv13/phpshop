<div class="row">
    <div class="col-sm-12">
        <div class="alert alert-danger">
            <strong>{Внимание}!</strong> {Для корректного расчета стоимости доставки и оформления заказа в системе СДЭК, необходимо выбрать ПВЗ или город для курьерской доставки.}<br>
            {Нажмите "Выбрать адрес доставки".}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <button type="button" class="btn btn-sm btn-primary cdek-change-address">{Выбрать адрес доставки}</button>
    </div>
</div>
<script src="../modules/cdekwidget/js/cdekwidget.js"></script>
@cdek_popup@
<input type="hidden" name="cdek_order_id" value="@cdek_order_id@">
<input type="hidden" id="dop_info" name="fakefield">