<?php

namespace Omnipay\WebDosh;
use Omnipay\WebDosh\Exceptions\WebDoshGatewayException;

class WebDoshStatus extends WebDoshTransaction
{

    private $amount;
    private $transaction_id;

    private $security_field_list = [
        'merchant_id',
        'transaction_id',
        'amount',
    ];

    private $references = [];

    public function __construct(Gateway $gateway,string $transaction_id, int $amount, array $references = [])
    {
        parent::__construct($gateway);
        try {
            if ($amount < 1)
                throw new WebDoshGatewayException("Invalid Amount", -1);
            $this->amount = $amount;
        } catch (\Exception $e) {
            throw new WebDoshGatewayException($e->getMessage(), $e->getCode());
        }
        $this->transaction_id = $transaction_id;
        $this->references = $references;
    }

    public function getPath()
    {
        return "/post2/status";
    }

    public function getPayload()
    {
        return [
            'transaction_id' => $this->transaction_id,
            'amount'         => $this->amount,
        ];
    }

    public function securityFieldList()
    {
        return $this->security_field_list;
    }
}