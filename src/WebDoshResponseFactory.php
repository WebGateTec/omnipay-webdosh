<?php

namespace Omnipay\WebDosh;

use Omnipay\WebDosh\Exceptions\WebDoshGatewayException;

class WebDoshResponseFactory
{
    static public function parse($raw_response, WebDoshTransaction $request)
    {
        try {
            $response = json_decode($raw_response, true);
            if (is_null($response))
                throw new WebDoshGatewayException('Could not parse response', -6);
            if (!array_key_exists('request_id', $response) || !array_key_exists('status', $response)) {
                $class = 'WebDoshInvalidResponse';
                $data = $response;
            } elseif ($response['status'] == 'OK') {
                $class = WebDoshOKResponse::class;
                $data = $response['result'];
                $data['request_id'] = $response['request_id'];
            } elseif ($response['status'] == 'NOTOK') {
                $class = WebDoshNotOkResponse::class;
                $data = $response['errors'];
                $data['request_id'] = array_key_exists('request_id', $response) ? $response['request_id'] : '';
            } elseif ($response['status'] == 'ERROR') {
                $class = WebDoshErrorResponse::class;
                $data = $response['errors'];
                $data['request_id'] = array_key_exists('request_id', $response) ? $response['request_id'] : '';
            } else throw new WebDoshGatewayException('Unknown status [' . $response['status'] . ']');
            return new $class($request, $data);
        } catch (\Exception $e) {
            throw new WebDoshGatewayException($e->getMessage(), $e->getCode());
        }
    }
}