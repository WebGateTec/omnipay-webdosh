<?php

namespace Omnipay\WebDosh;

use Omnipay\Common\Message\AbstractResponse;

class WebDoshNotOkResponse extends AbstractResponse
{

    public function __construct(WebDoshTransaction $request, array $data)
    {
        $this->request = $request;
        $this->data = $data;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function isSuccessful()
    {
        return false;
    }

    public function isRedirect()
    {
        return false;
    }

    public function isCancelled()
    {
        return true;
    }

    public function getMessage()
    {
        return $this->data['message'];
    }

    public function getCode()
    {
        return -1;
    }

    public function getTransactionReference()
    {
        return $this->data['request_id'];
    }

    public function getData()
    {
        return $this->data;
    }
}