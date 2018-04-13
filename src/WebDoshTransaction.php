<?php

namespace Omnipay\WebDosh;

use Omnipay\Common\Message\RequestInterface;
use Omnipay\WebDosh\Exceptions\WebDoshGatewayException;

abstract class WebDoshTransaction// implements RequestInterface
{
    protected $gateway;

    public function __construct(Gateway $gateway)
    {
        $this->gateway=$gateway;
    }

    public function getGateway()
    {
        return $this->gateway;
    }

	abstract public function getPath();

	abstract public function getPayload();

	abstract public function securityFieldList();

	public function send()
    {
        try {
            return WebDoshClient::process($this);
        } catch (\ErrorException $e) {
            throw new WebDoshGatewayException($e->getMessage());
        }
    }
}