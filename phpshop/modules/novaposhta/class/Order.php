<?php

include_once dirname(__DIR__) . '/class/OrderValueObject.php';

/**
 * Класс для работы с заказом.
 *
 * Class Order
 */
class Order {

    private $options;
    /** @var Request */
    private $request;
    /** @var OrderValueObject */
    private $orderValueObject;

    const PAYER_TYPE = 'Recipient'; // Плательщик, возможно в будущем вынести в настройки
    const PAYMENT_METHOD = 'Cash';
    const CARGO_TYPE = 'Cargo';
    const SERVICE = 'WarehouseWarehouse';
    const SEATS_AMOUNT = 1;
    const RECIPIENT_TYPE = 'PrivatePerson';

    /**
     * Order constructor.
     * @param array $options
     * @param Request $request
     */
    public function __construct($options, $request)
    {
        $this->options = $options;
        $this->request = $request;
        $this->orderValueObject = new OrderValueObject();
    }

    /**
     * @param $orderNumber
     * @param $weight
     * @param $cost
     */
    public function setOrder($orderNumber, $weight, $cost)
    {
        if($weight < 0.1) {
            $weight = 0.1;
        }

        $this->orderValueObject->orderNumber = $orderNumber;
        $this->orderValueObject->weight = $weight;
        $this->orderValueObject->cost = $cost;
        $this->orderValueObject->description = iconv('Windows-1251', 'UTF-8', 'Номер заказа ' . $orderNumber);
    }

    public function setSender()
    {
        $senderAddress = $this->options['sender_address'];
        if(!empty($this->options['pvz_ref'])) {
            $senderAddress = $this->options['pvz_ref'];
        }

        $this->orderValueObject->citySender = $this->options['city_sender'];
        $this->orderValueObject->sender = $this->options['sender'];
        $this->orderValueObject->senderAddress = $senderAddress;
        $this->orderValueObject->contactSender = $this->options['sender_contact'];
        $this->orderValueObject->sendersPhone = $this->options['phone'];
    }

    public function setRecipient($npProperties, $city, $recipient, $phone)
    {
        $this->orderValueObject->cityRecipient = iconv('Windows-1251', 'UTF-8', $city);
        $this->orderValueObject->recipientArea = iconv('Windows-1251', 'UTF-8', $npProperties['region']);
        $this->orderValueObject->pvz = $npProperties['pvz'];
        $this->orderValueObject->recipient = iconv('Windows-1251', 'UTF-8', $recipient);
        $this->orderValueObject->recipientsPhone = trim(str_replace(array('(', ')', '-', '+', '&#43;'), '', $phone));
    }

    /**
     * @return mixed
     */
    public function send()
    {
        return $this->request->post(Request::INTERNET_DOCUMENT_MODEL, Request::CREATE_ORDER_METHOD, array(
            'NewAddress' => 1,
            'PayerType' => self::PAYER_TYPE,
            'PaymentMethod' => self::PAYMENT_METHOD,
            'DateTime' => date('d.m.Y'),
            'CargoType' => self::CARGO_TYPE,
            'Weight' => $this->orderValueObject->weight,
            'ServiceType' => self::SERVICE,
            'SeatsAmount' => self::SEATS_AMOUNT,
            'Description' => $this->orderValueObject->description,
            'Cost' => $this->orderValueObject->cost,
            'CitySender' => $this->orderValueObject->citySender,
            'Sender' => $this->orderValueObject->sender,
            'SenderAddress' => $this->orderValueObject->senderAddress,
            'ContactSender' => $this->orderValueObject->contactSender,
            'SendersPhone' => $this->orderValueObject->sendersPhone,
            'RecipientCityName' => $this->orderValueObject->cityRecipient,
            'RecipientArea' => $this->orderValueObject->recipientArea,
            'RecipientAddressName' => $this->orderValueObject->pvz,
            'RecipientName' => $this->orderValueObject->recipient,
            'RecipientsPhone' => $this->orderValueObject->recipientsPhone,
            'RecipientType' => self::RECIPIENT_TYPE
        ), $this->orderValueObject->orderNumber);
    }
}