<?php
/**
 * PayKeeper payment handler
 * @author PayKeeper Software
 * @version 1.0
 * @package PHPShopPayment
 */
include_once($_SERVER['DOCUMENT_ROOT'] . "/phpshop/modules/paykeeper/class/init.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/phpshop/modules/paykeeper/class/paykeeper.class.php");

$PHPShopPaykeeperArray = new PHPShopPaykeeperArray();
$mod_opts = $PHPShopPaykeeperArray->getArray();
//print_r($mod_opts);
$mrh_ouid = explode("-", $_POST['ouid']); 
$inv_id = $mrh_ouid[0] . "" . $mrh_ouid[1];
$out_summ = $GLOBALS['SysValue']['other']['total']; 
$charset = $GLOBALS['SysValue']['other']['charset'];
$btn_val = "Оплатить";
$form_header = "Сейчас Вы перейдете на страницу оплаты...";


$pk_obj = new PaykeeperPayment();

$client_id =$_POST['fio_new'];

if ($charset=='windows-1251'){
    $client_id = iconv('windows-1251','UTF-8' ,$client_id);  
    $form_header =iconv('UTF-8','windows-1251' ,$form_header);
    $btn_val = iconv( 'UTF-8','windows-1251',$btn_val);
}

$pk_obj->setOrderParams(
    //sum
    $out_summ,
    //clientid
    $client_id,
    //orderid
    $inv_id,
    //client_email
    $_POST['mail_new'],
    //client_phone
    $_POST['tel_new'],
    //service_name
    '',
    //payment form url
    $mod_opts['form_url'],
    //secret key
    $mod_opts['secret']
);

$cart_data = $obj->PHPShopCart->getArray();

$item_index = 0;

foreach ($cart_data as $item) {
    $tax_rate = 0;
    $taxes = array("tax" => "none", "tax_sum" => 0);
    $name = $item["name"];
    $tax_amount =0;
    if ($obj->PHPShopSystem->getParam('nds') != 0) {
    $tax_rate = $obj->PHPShopSystem->getParam('nds');
    }
     // $tax_amount = $item['price']*($tax_rate/100);

    $price = floatval($item['price']+$tax_amount);
    $quantity = floatval($item['num']);
    if ($quantity == 1 && $pk_obj->single_item_index < 0)
        $pk_obj->single_item_index = $item_index;
    if ($quantity > 1 && $pk_obj->more_then_one_item_index < 0)
        $pk_obj->more_then_one_item_index = $item_index;
    $sum = $price*$quantity;
    $taxes = $pk_obj->setTaxes($tax_rate);
    $pk_obj->updateFiscalCart($pk_obj->getPaymentFormType(),
        $name, $price, $quantity, $sum, $taxes["tax"]);
    $item_index++;                        
}

$delivery_tax_rate = 0;
$delivery_price = 0;
if (isset($obj -> PHPShopDelivery -> objRow['price'])){
    $delivery_price = $obj -> PHPShopDelivery -> objRow['price'];
}
if ($delivery_price!=0) {
    $delivery_taxes = array("tax" => "none", "tax_sum" => 0);
    $delivery_tax_amount = 0;            
    if (isset($obj->PHPShopDelivery -> objRow['ofd_nds'])){
    $delivery_tax_rate = $obj ->PHPShopDelivery -> objRow['ofd_nds'];
    $delivery_taxes = $pk_obj->setTaxes($delivery_tax_rate,true);     
    }
    // $delivery_tax_amount = $delivery_price*($delivery_tax_rate/100);
    $pk_obj->setShippingPrice(floatval($delivery_price+$delivery_tax_amount));                       
    if (isset($obj ->PHPShopDelivery -> objRow['city'])){
        $delivery_name = $obj ->PHPShopDelivery -> objRow['city'];
    }
    if (!$pk_obj->checkDeliveryIncluded($pk_obj->getShippingPrice(), $delivery_name)
        && $pk_obj->getShippingPrice() > 0) {
            $pk_obj->setUseDelivery(); //for precision correct check
            $pk_obj->updateFiscalCart($pk_obj->getPaymentFormType(), $delivery_name,
            $pk_obj->getShippingPrice(), 1, $pk_obj->getShippingPrice(), $delivery_taxes["tax"]);
                $pk_obj->delivery_index = count($pk_obj->getFiscalCart())-1;
    }
}
        //set discounts
        if ($mod_opts['forced_discount_check'] == 1){
            $pk_obj->setDiscounts(true);
        }

                //handle possible precision problem
                $pk_obj->correctPrecision();
                $cart_encoded = array();
                foreach ($pk_obj->getFiscalCart() as $product) {
                    $product_ar = array();
                    foreach ($product as $key => $value) {
                        $enc = mb_detect_encoding($value, 'UTF-8, windows-1251', true);
                        $product_ar[$key] = ($enc == "UTF-8") ? $value : iconv('windows-1251', "UTF-8", $value);
                    }

                    $cart_encoded[] = $product_ar;
                }
                $fiscal_cart_encoded = json_encode($cart_encoded);                
                if ($pk_obj->getPaymentFormType() == "create") { //create form
                    $to_hash = number_format($pk_obj->getOrderTotal(), 2, ".", "") .
                               $pk_obj->getOrderParams("clientid")     .
                               $pk_obj->getOrderParams("orderid")      .
                               $pk_obj->getOrderParams("service_name") .
                               $pk_obj->getOrderParams("client_email") .
                               $pk_obj->getOrderParams("client_phone") .
                               $pk_obj->getOrderParams("secret_key");
                    $sign = hash('sha256' , $to_hash);

                    $form = '
                        <h3>"' . $form_header . '"</h3> 
                        <form name="payment" id="pay_form" action="'.$pk_obj->getOrderParams("form_url").'"  accept-charset="windows-1251" method ="post">
                        <input type="hidden" name="sum" value = "'.$pk_obj->getOrderTotal().'"/>
                        <input type="hidden" name="orderid" value = "'.$pk_obj->getOrderParams("orderid").'"/>
                        <input type="hidden" name="clientid" value = "'. $pk_obj->getOrderParams("clientid") .'"/>
                        <input type="hidden" name="client_email" value = "'.$pk_obj->getOrderParams("client_email").'"/>
                        <input type="hidden" name="client_phone" value = "'.$pk_obj->getOrderParams("client_phone").'"/>
                        <input type="hidden" name="service_name" value = "'.$pk_obj->getOrderParams("service_name").'"/>
                        <input type="hidden" name="cart" value = \''.htmlentities($fiscal_cart_encoded,ENT_QUOTES).'\' />
                        <input type="hidden" name="sign" value = "'.$sign.'"/>
                        <input type="submit" id="button-confirm" value="'.$btn_val.'"/>
                        </form>

                        <script>
                            $("#pay_form").submit();
                        </script>';  

                        $obj->set('orderMesage', $form.'<br><br>');
                       
                           
                }   else { //order form
                        $payment_parameters = array(
                            "clientid"=>mb_convert_encoding($pk_obj->getOrderParams("clientid"), 'UTF-8'),
                            "orderid"=>$pk_obj->getOrderParams('orderid'), 
                            "sum"=>$pk_obj->getOrderTotal(), 
                            "phone"=>$pk_obj->getOrderParams("phone"), 
                            "client_email"=>$pk_obj->getOrderParams("client_email"), 
                            "cart"=>$fiscal_cart_encoded);
                        $query = http_build_query($payment_parameters);
                        $query_options = array("http"=>array(
                            "method"=>"POST",
                            "header"=>"Content-type: application/x-www-form-urlencoded",
                            "content"=>$query
                            ));
                        $context = stream_context_create($query_options);
                        $err_num = $err_text = NULL;
                        if( function_exists( "curl_init" )) { //using curl
                            $CR = curl_init();
                            curl_setopt($CR, CURLOPT_URL, $pk_obj->getOrderParams("form_url"));
                            curl_setopt($CR, CURLOPT_POST, 1);
                            curl_setopt($CR, CURLOPT_FAILONERROR, true); 
                            curl_setopt($CR, CURLOPT_POSTFIELDS, $query);
                            curl_setopt($CR, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($CR, CURLOPT_SSL_VERIFYPEER, 0);
                            $result = curl_exec( $CR );
                            $error = curl_error( $CR );
                            if( !empty( $error )) {
                                $form = "<br/><span class=message>"."INTERNAL ERROR:".$error."</span>";
                                return false;
                            }
                            else {
                                $form = $result;
                            }
                            curl_close($CR);
                        }
                        else { //using file_get_contents
                            if (!ini_get('allow_url_fopen')) {
                                $form_html = "<br/><span class=message>"."INTERNAL ERROR: Option allow_url_fopen is not set in php.ini"."</span>";
                            }
                            else {
            
                                $form = file_get_contents($pk_obj->getOrderParams("form_url"), false, $context);
                            }
                        }
                        $obj->set('orderMesage', $form.'<br><br>');
                    }
                if ($form  == "") {
                    $form = '<h3>Error of payment: </h3><p>$err_num: '.htmlspecialchars($err_text).'</p>';
                }
?>
