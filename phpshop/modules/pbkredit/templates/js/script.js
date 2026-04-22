PbKredit = function () {
    var self = this;

    self.code = '';
    self.name = '';
    self.product = {
        title: '',
        price: 0,
        quantity: 1
    };
    self.init = function (params) {
        self.code = params.code;
        self.name = params.name;
        self.product.title = params.productName;
        self.product.price = params.productPrice;

        if($('input[name="quant[2]"]').val() > 1) {
            self.product.quantity = $('input[name="quant[2]"]').val();
        }

        self.bindEvents();
    };

    self.bindEvents = function () {
       $('.pbkredit-init').on('click', function () {
           window.PBSDK.posCredit.mount('#pbkredit-container', {
               ttCode: self.code,
               ttName: self.name,
               order: [{
                   model: self.product.title,
                   price: self.product.price,
                   quantity: self.product.quantity
               }]
           });
           $('#pbkreditModal').modal('toggle');
       });
    };
};