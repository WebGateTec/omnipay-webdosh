<?php

namespace Omnipay\WebDosh;
use Omnipay\WebDosh\Exceptions\WebDoshGatewayException;

class WebDoshTokenPaymentTransaction extends WebDoshTransaction
{

    private $amount;

    private $currency;

    private $token;

    private $card_verification_code;

    private $security_field_list = [
        'merchant_id',
        'token',
        'card_verification_code',
        'amount',
        'currency'
    ];
    private $references = [];

    public function __construct(Gateway $gateway,int $amount, string $currency, int $card_verification_code, string $token, array $references = [])
    {
        parent::__construct($gateway);
        try {
            if ($amount < 1)
                throw new WebDoshGatewayException("Invalid Amount", -1);
            if (!in_array($currency, WebDoshClient::$currencies))
                throw new WebDoshGatewayException("Invalid Currency", -2);
            $this->amount = $amount;
            $this->currency = $currency;
        } catch (\Exception $e) {
            throw new WebDoshGatewayException($e->getMessage(), $e->getCode());
        }
        $this->token = $token;
        $this->card_verification_code = $card_verification_code;
        $this->references = $references;
    }

    public function getPath()
    {
        return "/post2/token/card/payment";
    }

    public function getPayload()
    {
        return [
            'amount'                 => $this->amount,
            'currency'               => $this->currency,
            'token'                  => $this->token,
            'card_verification_code' => $this->card_verification_code,
        ];
    }

    public function securityFieldList()
    {
        return $this->security_field_list;
    }
}