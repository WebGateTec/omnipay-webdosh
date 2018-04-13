<?php

namespace Omnipay\WebDosh;

use Omnipay\Common\CreditCard;

class WebDoshTokenize extends WebDoshTransaction
{
    private $security_field_list = [
        'merchant_id',
        'card_number',
        'card_expiry_month',
        'card_expiry_year',
        'card_verification_code',
        'currency'
    ];
    private $card;
    private $currency;

    public function __construct(Gateway $gateway,string $currency, CreditCard $card)
    {
        parent::__construct($gateway);
        $this->currency = $currency;
        $this->card = $card;
    }

    public function getPath()
    {
        return "/post2/tokenize";
    }

    public function getPayload()
    {
        return [
            'card_number'            => $this->card->getNumber(),
            'card_holder'            => $this->card->getBillingName(),
            'card_expiry_month'      => $this->card->getExpiryMonth(),
            'card_expiry_year'       => $this->card->getExpiryYear(),
            'card_verification_code' => $this->card->getCvv(),
            'payment_method'         => strtoupper($this->card->getBrand()),
            'currency'               => $this->currency,
        ];
    }

    public function securityFieldList()
    {
        return $this->security_field_list;
    }
}