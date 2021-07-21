<?php

namespace EBilling;

final class InvoiceApi
{
    private $url;

    private $token;

    private $requestDetails;

    public function __construct($url, $token)
    {
        $this->url = $url;
        $this->token = $token;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getRequestDetails()
    {
        return $this->requestDetails;
    }

    public function send(array $data)
    {
        $json = json_encode($data);
        $handler = curl_init($this->url);

        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handler, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($handler, CURLOPT_POSTFIELDS, $json);
        curl_setopt($handler, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($json),
            'Authorization: Bearer ' . $this->token
        ]);

        $result = curl_exec($handler);

        curl_close($handler);
        
        $this->requestDetails = [
            'url' => $this->url,
            'token' => $this->token,
            'body' => $data
        ];

        return $result;
    }
}
