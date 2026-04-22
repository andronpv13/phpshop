<script src="https://widget.pochta.ru/courier/widget/widget.js"></script>
<script src="https://widget.pochta.ru/map/widget/widget.js"></script>
<div class="modal fade bs-example-modal" id="pochtaModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">{Доставка}</h4>
            </div>
            <div class="modal-body" style="width:100%;">
                <div id="pochta-frame" style="height: 600px"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="pochta-close">{Закрыть}</button>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="pochta_widget_id" value="@pochta_widget_id@">
<input type="hidden" name="pochta_courier_widget_id" value="@pochta_courier_widget_id@">
<input type="hidden" name="pochta_weight" value="@pochta_weight@">
<input type="hidden" name="pochta_ins_value" value="@pochta_ins_value@">
<input type="hidden" name="pochta_order_lines" value='@pochta_order_lines@'>
<script src="phpshop/modules/pochta/templates/pochta.js"></script>