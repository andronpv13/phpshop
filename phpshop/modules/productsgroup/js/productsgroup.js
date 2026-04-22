$().ready(function(){

    $(".price_main").html( $(".all_price").val() );

    $("body").on("click", ".addToCartListGroup", function(){
        var ar_group = $(this).attr("data-uid-group");
        var ar_group_new = ar_group.split("|");
        ar_group_new.forEach(function(item, i, arr) {
            if(item!="") {
                var item_new = item.split(":");
                if( $("#num_group_"+item_new[0]).val()!='' ) {
                    addToCartList(item_new[0], $("#num_group_"+item_new[0]).val());
                }
                else {                          
                    addToCartList(item_new[0], item_new[1]);
                }
            }
        });
    });


    $(".tovarDivPriceInput").change(function(){
        var sum = 0;
        $( ".tovarDivPriceInput" ).each(function() {
            if(parseInt($(this).attr('value') )>0) {
                sum += parseInt($(this).attr('data-price')) * parseInt($(this).val());
            }   
        });
        //alert(sum);
        var str = String(sum);

        price = str.replace(/(\d)(?=(\d{3})+(\D|$))/g, '$1 ');
        $(".priceGroupeR").html(price);

    });
    

});