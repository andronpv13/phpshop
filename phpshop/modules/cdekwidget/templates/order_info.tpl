<table class="table table-bordered">
    <tbody>
    <tr>
        <td>{Статус заказа}</td>
        <td>@cdek_status@</td>
    </tr>
    @cdek_errors@
    <tr>
        <td>{Статус оплаты}</td>
        <td>@cdek_payment_status@</td>
    </tr>
    <tr>
        <td>{Способ доставки}</td>
        <td>@cdek_delivery_info_type@</td>
    </tr>
    <tr>
        <td>{Информация о доставке}</td>
        <td>@cdek_delivery_info@</td>
    </tr>
    </tbody>
</table>
<div class="row" style="padding-bottom: 20px;">
    <div class="col-sm-12" style="@cdek_hide_actions@">
        <button type="button" class="btn btn-sm btn-primary cdek-change-address">{Изменить}</button>
        <button type="button" class="btn btn-sm btn-success cdek-send">{Отправить заказ в систему СДЭК}</button>
    </div>
</div>

<table class="table table-bordered" style="@cdek_statuses_hidden@">
    <thead>
    <tr>
        <th>{Статус заказа}</th>
        <th>{Дата}</th>
    </tr>
    </thead>
    <tbody>
    @cdek_statuses@
    </tbody>
</table>

<input type="hidden" name="cdek_order_id" value="@cdek_order_id@">
<input type="hidden" id="dop_info" name="fakefield">