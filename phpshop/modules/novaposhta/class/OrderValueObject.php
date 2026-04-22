<?php

class OrderValueObject {

    // order
    public $orderNumber;
    public $weight;
    public $description;
    public $cost;

    // sender
    public $citySender;
    public $sender;
    public $senderAddress;
    public $contactSender;
    public $sendersPhone;

    // recipient
    public $cityRecipient;
    public $recipientArea;
    public $recipientAreaRegion;
    public $recipientAddressName;
    public $recipient;
    public $recipientsPhone;
    public $pvz;
}