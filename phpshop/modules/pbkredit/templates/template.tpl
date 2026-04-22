<link href="phpshop/modules/pbkredit/templates/css/style.css" rel="stylesheet">
<div class="modal fade bs-example-modal" id="pbkreditModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog pbkredit-modal">
        <div class="modal-content">
            <div class="modal-body" style="width:100%;">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                <div id="pbkredit-container"></div>
            </div>
        </div>
    </div>
</div>
<a href="#" class="pbkredit-init">
    <i class="icon icon-bag"></i>
    <strong>В кредит от @pbkredit_cost@ р / мес.</strong>
</a>
<script src="phpshop/modules/pbkredit/templates/js/script.js"></script>
<script src="https://my.pochtabank.ru/sdk/v1/pos-credit.js"></script>
<script>
    var PbKreditInstance = new PbKredit();
    var PbKreditParams = {
        'code': '@pbkredit_tt_code@',
        'name': '@pbkredit_tt_name@',
        'productName': '@pbkredit_tt_product_name@',
        'productPrice': @pbkredit_tt_product_price@
    };
    PbKreditInstance.init(PbKreditParams);
</script>