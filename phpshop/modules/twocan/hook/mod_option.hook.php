<?php

PHPShopObj::loadClass("array");
/**
 *  ласс получени€ настроек модул€
 */
class PHPShopTwocanArray extends PHPShopArray
{

    function __construct()
    {
        $this->objType = 3;
        $this->objBase = $GLOBALS['SysValue']['base']['twocan']['twocan_system'];
        parent::__construct("login", "password", "dev_mode", "terminal", "url", "test_url", "autocharge", "exptimeout", "template","status", 'title_sub', "status_auth");
    }

    /**
     * «апись лога
     * @param string $message содержание запроса в ту или иную сторону
     * @param string $order_id номер заказа
     * @param string $status статус оплаты
     */
    function log($message, $order_id, $status, $type, $twocan_id = '')
    {

        $PHPShopOrm = new PHPShopOrm("phpshop_modules_twocan_log");
        $log = array(
            'message_new' => serialize($message),
            'order_id_new' => $order_id,
            'status_new' => $status,
            'type_new' => $type,
            'date_new' => time(),
            'twocan_id_new' => $twocan_id
        );
        $PHPShopOrm->insert($log);
    }

    /**
     * «апись/обновление заказа twocan 
     */
    function setOrder($id, $twocanid, $amount,  $status, $charged=0, $refunded=0)
    {


        $PHPShopOrm = new PHPShopOrm("phpshop_modules_twocan_orders");
        $founded = $PHPShopOrm->getOne(array('*'), array('id'=>"='".$id."'" ));
        
        $order = array(
            'id_new' => $id,
            'twocanid_new' => $twocanid,
            'amount_new' => $amount,
            'status_new' => $status,
            'charged_new' => $charged,
            'refunded_new' => $refunded,           
        );   
        
        if($founded ){
            unset($order['id_new']);
            $PHPShopOrm->update($order, array('id'=>"='".$id."'" ));
        }else{
            $PHPShopOrm->insert($order);
        }
    }

   /**
     * ќбновление статуса заказа twocan 
     */
    function updateOrderStatus($id, $status)
    {


        $PHPShopOrm = new PHPShopOrm("phpshop_modules_twocan_orders");
        
        $order = array(            
            'status_new' => $status,         
        );   
        
         $PHPShopOrm->update($order, array('id'=>"='".$id."'" ));
        
    }



    function getOrder($twocanorderid, $option){

        foreach (["test_url", 'url', "login", "password", 'terminal'] as $required) {
            if(empty($option[$required])) return [
                'result_code' => 0,
                'error' => $required . " not set"
            ];
        }
        // –ежим разработки и боевой режим
        if ($option["dev_mode"] == 0)
            $url = $option["test_url"];
        else
            $url = $option["url"];

        $headers = array(
            'Content-Type:application/json',
            'Authorization: Basic '. base64_encode($option["login"] . ":" . $option["password"]) // <---
        );
        
        $twocanCurl = curl_init();        
        $request_url = rtrim($url,'/') .  sprintf('/orders/%s?expand=custom_fields', $twocanorderid);
        curl_setopt_array($twocanCurl, array(
            CURLOPT_HTTPHEADER =>  $headers, 
            CURLOPT_URL => $request_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLINFO_HEADER_OUT => true
        ));    

        $twocanresultRAW = curl_exec($twocanCurl);       

        $info = curl_getinfo($twocanCurl);
        
        curl_close($twocanCurl); 

        
        $twocanresult = json_decode($twocanresultRAW,true);   

        $result =[
           
            'request' =>  $request_url,
            'result_code' =>  $info['http_code'],
            'status' => isset($twocanresult['orders'][0]['status'])?$twocanresult['orders'][0]['status']:'',
            'result' => $twocanresult            
        ];

        return $result;

    }

    function createOrder($twocanrequest, $option)
    {
        // –ежим разработки и боевой режим
        if ($option["dev_mode"] == 0)
            $url = $option["test_url"];
        else
            $url = $option["url"];

        foreach (["test_url", 'url', "login", "password", 'terminal'] as $required) {
            if(empty($option[$required])) return [
                'result_code' => 0,
                'error' => $required . " not set"
            ];
        }

        $headers = array(
            'Content-Type:application/json',
            'Authorization: Basic '. base64_encode($option["login"] . ":" . $option["password"]) // <---
        );
        
        $twocanCurl = curl_init();        
        
        curl_setopt_array($twocanCurl, array(
            CURLOPT_HTTPHEADER =>  $headers, 
            CURLOPT_URL => rtrim($url,'/') . '/orders/create',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($twocanrequest),
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HEADER => true,
            CURLINFO_HEADER_OUT => true
        ));        

        $twocanresultRAW = curl_exec($twocanCurl);       

        $info = curl_getinfo($twocanCurl);
        
        curl_close($twocanCurl);

        $resultheaders = substr($twocanresultRAW, 0, $info["header_size"]); //split out header
        $twocanresult = json_decode(substr($twocanresultRAW, $info["header_size"]),true);
    
        preg_match("!\r\n(?:Location|URI): *(.*?) *\r\n!", $resultheaders, $matches);
        $redirect_url = $matches[1];

        $result =[
            'request_info' =>  $info['request_header'],
            'request' =>  $twocanrequest,
            'result_code' =>  $info['http_code'],
            'redirect_url' => $redirect_url,
            'result' => $twocanresult,
            
        ];

        return $result;
    }

    function refundOrder($twocanorderid, $amount,  $option)
    {
        foreach (["test_url", 'url', "login", "password", 'terminal'] as $required) {
            if(empty($option[$required])) return [
                'result_code' => 0,
                'error' => $required . " not set"
            ];
        }
        
        // –ежим разработки и боевой режим
        if ($option["dev_mode"] == 0)
            $url = $option["test_url"];
        else
            $url = $option["url"];

        $headers = array(
            'Content-Type:application/json',
            'Authorization: Basic '. base64_encode($option["login"] . ":" . $option["password"]) // <---
        );
        
        
        $twocanCurl = curl_init();        

        $request_url = rtrim($url,'/') . sprintf('/orders/%s/refund', $twocanorderid);
        
        curl_setopt_array($twocanCurl, array(
            CURLOPT_HTTPHEADER =>  $headers, 
            CURLOPT_URL => $request_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode(['amount'=> $amount]),
            
        ));        

        $twocanresultRAW = curl_exec($twocanCurl);    

        $info = curl_getinfo($twocanCurl);
        
        $twocanresult = json_decode($twocanresultRAW,true);
        if(!$twocanresult) $twocanresult = curl_error($twocanCurl);

        curl_close($twocanCurl);
    
        $result =[
            
            'request' =>  'PUT ' . $request_url,
            'status' => isset($twocanresult['orders'][0]['status'])?$twocanresult['orders'][0]['status']:'',
            'result_code' =>  $info['http_code'],
          
            'result' => $twocanresult,
            
        ];

        return $result;
    }

    function chargeOrder($twocanorderid, $amount,  $option)
    {
        foreach (["test_url", 'url', "login", "password", 'terminal'] as $required) {
            if(empty($option[$required])) return [
                'result_code' => 0,
                'error' => $required . " not set"
            ];
        }
        
        // –ежим разработки и боевой режим
        if ($option["dev_mode"] == 0)
            $url = $option["test_url"];
        else
            $url = $option["url"];

        $headers = array(
            'Content-Type:application/json',
            'Authorization: Basic '. base64_encode($option["login"] . ":" . $option["password"]) // <---
        );
        
        
        $twocanCurl = curl_init();        

        $request_url = rtrim($url,'/') . sprintf('/orders/%s/charge', $twocanorderid);
        
        curl_setopt_array($twocanCurl, array(
            CURLOPT_HTTPHEADER =>  $headers, 
            CURLOPT_URL => $request_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode(['amount'=> $amount]),
           
        ));        

        $twocanresultRAW = curl_exec($twocanCurl);    

        $info = curl_getinfo($twocanCurl);
        
        $twocanresult = json_decode($twocanresultRAW,true);
        if(!$twocanresult) $twocanresult = curl_error($twocanCurl);

        curl_close($twocanCurl);
    
        $result =[
            
            'request' =>  'PUT ' . $request_url,
            'status' => isset($twocanresult['orders'][0]['status'])?$twocanresult['orders'][0]['status']:'',
            'result_code' =>  $info['http_code'],
          
            'result' => $twocanresult,
            
        ];

        return $result;
    }

    function reverseOrder($twocanorderid,  $option)
    {
        foreach (["test_url", 'url', "login", "password", 'terminal'] as $required) {
            if(empty($option[$required])) return [
                'result_code' => 0,
                'error' => $required . " not set"
            ];
        }
        
        // –ежим разработки и боевой режим
        if ($option["dev_mode"] == 0)
            $url = $option["test_url"];
        else
            $url = $option["url"];

        $headers = array(
            'Content-Type:application/json',
            'Authorization: Basic '. base64_encode($option["login"] . ":" . $option["password"]) // <---
        );
        
        
        $twocanCurl = curl_init();        

        $request_url = rtrim($url,'/') . sprintf('/orders/%s/reverse', $twocanorderid);
        
        curl_setopt_array($twocanCurl, array(
            CURLOPT_HTTPHEADER =>  $headers, 
            CURLOPT_URL => $request_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            
        ));        

        $twocanresultRAW = curl_exec($twocanCurl);    

        $info = curl_getinfo($twocanCurl);
        
        $twocanresult = json_decode($twocanresultRAW,true);
        if(!$twocanresult) $twocanresult = curl_error($twocanCurl);

        curl_close($twocanCurl);
    
        $result =[
            
            'request' =>  'PUT ' . $request_url,
            'status' => isset($twocanresult['orders'][0]['status'])?$twocanresult['orders'][0]['status']:'',
            'result_code' =>  $info['http_code'],
          
            'result' => $twocanresult,
            
        ];

        return $result;
    }

    
}