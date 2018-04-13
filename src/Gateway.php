<?php

namespace Omnipay\WebDosh;

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\CreditCard;
use Omnipay\WebDosh\Exceptions\WebDoshGatewayException;


class Gateway extends AbstractGateway
{
    function __construct($merchant_id, $secret)
    {
        $this->initialize();
    }

    public function getName()
    {
        return 'WebDosh';
    }

    public function getDefaultParameters()
    {
        return [
            'endpoint'    => WebDoshConstants::TEST_ENDPOINT,
            'merchant_id' => '',
            'secret'      => ''
        ];
    }

    public function setMerchantId($value){
        $this->setParameter('merchant_id',$value);
    }

    public function setSecret($value){
        $this->setParameter('secret',$value);
    }

    public function setEndpoint($value){
        $this->setParameter('endpoint',$value);
    }

    public function purchase($options)
    {
        try {
            if (array_key_exists('cardReference', $options)&&!empty($options['cardReference']))
                return $this->tokenPayment(array_merge($options, ['token' => $options['cardReference']]));
            return new WebDoshPaymentTransaction(
                $this,
                $options['amount'],
                $options['currency'],
                is_array($options['card'])?new CreditCard($options['card']):$options['card']
            );
        } catch (\ErrorException $e) {
            throw new WebDoshGatewayException($e->getMessage());
        }
    }

    public function createCard($options)
    {
        try {
            return new WebDoshTokenize(
                $this,
                $options['currency'],
                is_array($options['card'])?new CreditCard($options['card']):$options['card']
            );
        } catch (\ErrorException $e) {
            throw new WebDoshGatewayException($e->getMessage());
        }
    }

    public function refund($options)
    {
        try {
            return new WebDoshRefundTransaction($this,$options['transactionReference'], $options['amount']);
        } catch (\ErrorException $e) {
            throw new WebDoshGatewayException($e->getMessage());
        }
    }

    public function tokenPayment($options)
    {
        try {
            return new WebDoshTokenPaymentTransaction(
                $this,
                $options['amount'],
                $options['currency'],
                $options['cvv'],
                $options['token']
            );
        } catch (\ErrorException $e) {
            throw new WebDoshGatewayException($e->getMessage());
        }
    }

    public function status($options)
    {
        try {
            return new WebDoshStatus(
                $this,
                $options['transactionReference'],
                $options['amount']
            );
        } catch (\ErrorException $e) {
            throw new WebDoshGatewayException($e->getMessage());
        }
    }

    public function supportsAuthorize()
    {
        return false;
    }

    public function supportsCapture()
    {
        return false;
    }

    public function supportsCompleteAuthorize()
    {
        return false;
    }

    public function supportsCompletePurchase()
    {
        return false;
    }

    public function supportsDeleteCard()
    {
        return false;
    }

    public function supportsVoid()
    {
        return false;
    }

    public function supportsUpdateCard()
    {
        return false;
    }

}