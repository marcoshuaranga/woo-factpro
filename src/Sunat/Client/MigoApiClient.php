<?php

namespace EBilling\Sunat\Client;

use EBilling\Sunat\Response\DniResponse;
use EBilling\Sunat\Response\RucResponse;
use EBilling\Sunat\SunatClient;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\RequestException;

final class MigoApiClient implements SunatClient
{
    private $http;

    public function __construct()
    {
        $this->http = new GuzzleHttpClient([
            'base_uri' => 'https://api.migo.pe/api/v1/',
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function findPersonByDni($dni)
    {
        try {
            $response = $this->http->post('dni', [
                'json' => [
                    'token' => get_option('wc_settings_ebilling_client_token'),
                    'dni' => $dni,
                ]
            ]);
        } catch (RequestException $e) {
            return new \WP_Error('guzzle_request_exception', $e->getMessage(), ['status' => $e->getResponse()->getStatusCode()]);
        }

        $data = json_decode($response->getBody()->getContents(), true);

        if (! $data['success']) {
            return new \WP_Error('id_not_found', $data['message'], ['status' => 404]);
        }

        $first_and_last_name = explode(' ', $data['nombre']);

        return new DniResponse(
            implode(' ', array_slice($first_and_last_name, 2)),
            $first_and_last_name[0],
            $first_and_last_name[1]
        );
    }

    public function findCompanyByRuc($ruc)
    {
        try {
            $response = $this->http->post('ruc', [
                'json' => [
                    'token' => get_option('wc_settings_ebilling_client_token'),
                    'ruc' => $ruc,
                ]
            ]);
        } catch (RequestException $e) {
            return new \WP_Error('guzzle_request_exception', $e->getMessage(), ['status' => $e->getResponse()->getStatusCode()]);
        }

        $data = json_decode($response->getBody()->getContents(), true);

        if (! $data['success']) {
            return new \WP_Error('id_not_found', $data['message'], ['status' => 404]);
        }

        return new RucResponse($data['nombre_o_razon_social'], $data['direccion_simple'], $data['ubigeo']);
    }
}
