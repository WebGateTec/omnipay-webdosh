<?php

namespace Omnipay\WebDosh;

use Omnipay\WebDosh\Exceptions\WebDoshGatewayException;

class WebDoshClient
{
    static public $currencies = WebDoshConstants::CURRENCIES;

    static public function process(WebDoshTransaction $transaction)
    {
        try {
            $gateway=$transaction->getGateway();
            $parameters=$gateway->getParameters();
            if (parse_url($parameters['endpoint']) === false) {
                throw new WebDoshGatewayException('Malformed URL.');
            }
            if (!self::endsWith($parameters['endpoint'], '/'))
                $parameters['endpoint'] .= '/';
            return self::transmit(
                self::secure(
                    array_merge(
                        $transaction->getPayload(), [
                            'merchant_id' => $parameters['merchant_id']
                        ]
                    ),
                    $transaction->securityFieldList(),
                    $parameters['secret']
                ),
                self::buildURL($parameters['endpoint'],$transaction->getPath()),
                $transaction
            );
        } catch (\Exception $e) {
            throw new WebDoshGatewayException($e->getMessage(), $e->getCode());
        }
    }

    static private function secure(array $payload, array $security_field_list,string $secret)
    {
        $security_string = '';
        foreach ($security_field_list as $field_name)
            $security_string .= $payload[$field_name];
        $payload['security'] = hash('sha512', $security_string . $secret);
        return $payload;
    }

    static private function buildURL(string $endpoint,string $path)
    {
        return sprintf('%s%s', $endpoint, self::startsWith($path, '/') ? substr($path, 1) : $path);
    }

    static private function transmit(array $payload, $url, WebDoshTransaction $request)
    {
        try {
            $payload = http_build_query($payload);
            $raw_response = file_get_contents(
                $url,
                false,
                stream_context_create([
                    'http' => [
                        'ignore_errors' => true,
                        'method'        => 'POST',
                        'header'        => "Content-type: application/x-www-form-urlencoded\r\n"
                            . "Content-Length: " . strlen($payload) . "\r\n",
                        'content'       => $payload
                    ]
                ])
            );

            $response_code = array_reduce($http_response_header, function ($carry, $header) {
                if (substr($header, 0, 4) === 'HTTP')
                    $carry = (int)(explode(' ', $header)[1]);
                return $carry;
            });
            switch ($response_code) {
                case 200:
                case 422:
                case 419:
                    return WebDoshResponseFactory::parse($raw_response,$request);
                    break;
                default:
                    throw new \ErrorException($raw_response, $response_code);
            }
        } catch (\Exception $e) {
            throw new WebDoshGatewayException($e->getMessage(), $e->getCode());
        }
    }

    static private function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    static private function endsWith($haystack, $needle)
    {
        $length = strlen($needle);

        return $length === 0 ||
            (substr($haystack, -$length) === $needle);
    }
}