<?php
 
/**
 * Success payment handler
 */

function success_mod_paykeeper_hook($obj, $value){
	include_once($_SERVER['DOCUMENT_ROOT'] . "/phpshop/modules/paykeeper/class/init.php");
	$PHPShopPaykeeperArray = new PHPShopPaykeeperArray();
	$mod_opts = $PHPShopPaykeeperArray->getArray();
	$sum = $_POST['sum'];
	$id = $_POST['id'];
	$orderid = $_POST['orderid'];
	$clientid = $_POST['clientid'];
	$key = $_POST['key'];
	if ($key != md5 ($id . number_format ($sum , 2 , '.' , '') . $clientid.$orderid.$mod_opts['secret']))
	{
	    echo "Error! Hash mismatch";
	    exit;
	}
	$obj->inv_id = $_POST['orderid'];
	$obj->out_summ = $_POST['sum'];
	$obj->order_metod = "paykeeper";
	$obj->write_payment();
	$obj->update_order_status();

	echo "OK ".md5($id.$mod_opts['secret']);
	//$success_function = false;
	exit;
}
$addHandler=array
        (
             'index'=>'success_mod_paykeeper_hook'
 
);
?>
