<?php

class ApiHelper
{
    public $api;

    public function __construct($url, $key)
    {
        if (isset($key) && isset($url) && !empty($key) && !empty($url)) {
            $this->api = new ApiClient($url, $key);
            try {
                $response = $this->api->statusesList();
            } catch (CurlException $e) {
                Tools::logger(array('error' => $e->getMessage()), "connect", iconv('UTF-8', 'Windows-1251', 'Ошибка соединения с RetailCRM'));
            }
            if (!$response->isSuccessful()) {
                return false;
            }
        } else {
            return false;
        }
    }

    public function processCustomers($customers)
    {
        try {
            $this->api->customersUpload($customers);
        } catch (CurlException $e) {
            Tools::logger(array('error' => $e->getMessage()), "connect", iconv('UTF-8', 'Windows-1251', 'Ошибка соединения с RetailCRM'));
            return false;
        }
    }

public function processOrders($orders, $nocheck = false)
    {
        if (!$nocheck) {
            foreach ($orders as $idx => $order) {
                $customer = array();
                $customer['phones'][]['number'] = $order['phone'];
                $customer['externalId'] = $order['customerId'];
                $customer['firstName'] = $order['firstName'];
                $customer['lastName'] = $order['lastName'];
                $customer['patronymic'] = $order['patronymic'];

                if(isset($order['delivery']['address']))
                    $customer['address'] = $order['delivery']['address'];

                if (isset($order['email']))
                    $customer['email'] = $order['email'];

                $checkResult = $this->checkCustomers($customer);

                if ($checkResult === false) {
                    unset($orders[$idx]["customerId"]);
                } else {
                    $orders[$idx]["customerId"] = $checkResult;
                }
            }
        }

        $order_id = $orders[0]['externalId'];

        $splitOrders = array_chunk($orders, 50);
        foreach($splitOrders as $orders) {
            try {
                $response = $this->api->ordersUpload($orders);
                time_nanosleep(0, 250000000);
                if (!$response->isSuccessful()) {
                    if (isset($response['errors'])) {
                        Tools::logger(['request' => $orders, 'response' => $response['errors']], 'send_order', iconv('UTF-8', 'Windows-1251', 'Ошибка передачи заказа в RetailCRM'), $order_id);
                    }
                } else {
                    Tools::logger(['request' => $orders, 'response' => $response->response], 'send_order', iconv('UTF-8', 'Windows-1251', 'Заказ передан в RetailCRM'), $order_id);
                }
            } catch (CurlException $e) {
                Tools::logger(['request' => $orders, 'response' => ['error' => $e->getMessage()]], 'connect', iconv('UTF-8', 'Windows-1251', 'Ошибка соединения с RetailCRM'), $order_id);
                return false;
            }
        }
    }
    
    public function processExport($customers, $orders)
    {
        $splitCustomers = array_chunk($customers, 50);

        foreach ($splitCustomers as $chunk) {
            try {
                $this->api->customersUpload($chunk);
                time_nanosleep(0, 250000000);
            } catch (CurlException $e) {
                Tools::logger(array('error' => $e->getMessage()), "connect", iconv('UTF-8', 'Windows-1251', 'Ошибка соединения с RetailCRM'));
            }
        }

        $splitOrders = array_chunk($orders, 50);

        foreach ($orders as $key => $chunk) {
            try {
                $this->api->ordersUpload($chunk);
                time_nanosleep(0, 250000000);
            } catch (CurlException $e) {
                Tools::logger(array('error' => $e->getMessage()), "connect", iconv('UTF-8', 'Windows-1251', 'Ошибка соединения с RetailCRM'));
            }
        }
    }

public function orderHistory()
    {
        try {
            $orders = $this->api->ordersHistory(new DateTime(Tools::getDate('../logs/history.log')));
            return $orders['orders'];
        } catch (CurlException $e) {
            Tools::logger(array('error' => $e->getMessage()), "connect", iconv('UTF-8', 'Windows-1251', 'Ошибка соединения с RetailCRM'));
            return false;
        }
    }
    
    public function orderFixExternalIds($data)
    {
        try {
            return $this->api->ordersFixExternalIds($data);
        } catch (CurlException $e) {
            Tools::logger(array('error' => $e->getMessage()), "connect", iconv('UTF-8', 'Windows-1251', 'Ошибка соединения с RetailCRM'));
            return false;
        }
    }

private function checkCustomers($customer)
    {
        $result = '';

        try {
            $search = array(
                'name' => $customer['phones'][0]['number'],
                'email' => (isset($customer['email'])) ? $customer['email'] : ''
            );
            $result = $this->api->customersList($search);
        } catch (CurlException $e) {
            Tools::logger(array('error' => $e->getMessage()), "connect", iconv('UTF-8', 'Windows-1251', 'Ошибка соединения с RetailCRM'));
            return false;
        }

        if ($result->isSuccessful()) {

            if(empty($result['customers']) || count('customers') < 1) {
                try {
                    $this->api->customersEdit($customer);
                    return $customer["externalId"];
                } catch (CurlException $e) {
                    Tools::logger(array('error' => $e->getMessage()), "connect", iconv('UTF-8', 'Windows-1251', 'Ошибка соединения с RetailCRM'));
                    return false;
                }
            } else {
                return (isset($result['customers'][0]['externalId']) && !empty($result['customers'][0]['externalId'])) ? $result['customers'][0]['externalId'] : $customer["externalId"];
            }
        } else {
            Tools::logger($result->response, 'customers', iconv('UTF-8', 'Windows-1251', 'Ошибка проверки пользователя'));
            return false;
        }

    }

}
