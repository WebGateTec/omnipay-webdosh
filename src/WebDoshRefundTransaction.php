<?php

namespace Omnipay\WebDosh;

class WebDoshRefundTransaction extends WebDoshTransaction
{

    private $security_field_list = [
        'merchant_id',
        'transaction_id',
        'amount',
    ];

    private $transaction_id;

    private $amount;

    private $references = [];

    public function __construct(Gateway $gateway,string $transaction_id, int $amount, array $references = [])
    {
        parent::__construct($gateway);
        try {
            if ($amount < 1)
                throw new \ErrorException("Invalid Amount", -1);
            $this->amount = $amount;
        } catch (\Exception $e) {
            throw new \ErrorException('WebDoshTransaction Error: ' . $e->getMessage(), $e->getCode());
        }
        $this->references = $references;
        $this->transaction_id = $transaction_id;
    }

    public function getPayload()
    {
        return [
            'amount'         => $this->amount,
            'transaction_id' => $this->transaction_id,
        ];
    }

    public function securityFieldList()
    {
        return $this->security_field_list;
    }

    public function getPath()
    {
        return "/post2/refund";
    }
}