<?php

namespace Omnipay\WebDosh;

use Omnipay\Common\CreditCard;

class WebDoshPaymentTransaction extends WebDoshTransaction
{
    private $security_field_list = [
        'merchant_id',
        'card_number',
        'card_expiry_month',
        'card_expiry_year',
        'card_verification_code',
        'amount',
        'currency'
    ];

    private $card;

    private $amount;

    private $currency;

    private $references = [];

    public function __construct(Gateway $gateway,int $amount, string $currency, CreditCard $card, array $references = [])
    {
        parent::__construct($gateway);
        try {
            if ($amount < 1)
                throw new \ErrorException("Invalid Amount", -1);
            if (!in_array($currency, WebDoshClient::$currencies))
                throw new \ErrorException("Invalid Currency", -2);
            $this->amount = $amount;
            $this->currency = $currency;
            $this->references = $references;
        } catch (\Exception $e) {
            throw new \ErrorException('WebDoshTransaction Error: ' . $e->getMessage(), $e->getCode());
        }
        $this->card = $card;
    }

    public function getPayload()
    {
        return [
            'amount'                 => $this->amount,
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

    public function getPath()
    {
        return "/post2/payment";
    }
}