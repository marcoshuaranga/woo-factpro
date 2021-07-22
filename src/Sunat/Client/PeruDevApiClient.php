<?php

namespace EBilling\Sunat\Client;

use EBilling\Sunat\Response\DniResponse;
use EBilling\Sunat\Response\RucResponse;
use EBilling\Sunat\SunatClient;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\RequestException;

final class PeruDevApiClient implements SunatClient
{
    private $http;

    public function __construct()
    {
        $this->http = new GuzzleHttpClient([
            'base_uri' => 'https://apiperu.dev/api/',
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . get_option('wc_settings_ebilling_client_token'),
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function findPersonByDni($dni)
    {
        try {
            $response = $this->http->get('dni/' . $dni);
        } catch (RequestException $e) {
            return new \WP_Error('guzzle_request_exception', $e->getMessage(), ['status' => $e->getResponse()->getStatusCode()]);
        }

        $data = json_decode($response->getBody()->getContents(), true);

        if (! $data['success']) {
            return new \WP_Error('id_not_found', $data['message'], ['status' => 404]);
        }

        return new DniResponse(
            $data['data']['nombres'],
            $data['data']['apellido_paterno'],
            $data['data']['apellido_materno'],
        );
    }

    public function findCompanyByRuc($ruc)
    {
        try {
            $response = $this->http->get('ruc/' . $ruc);
        } catch (RequestException $e) {
            return new \WP_Error('guzzle_request_exception', $e->getMessage(), ['status' => $e->getResponse()->getStatusCode()]);
        }

        $data = json_decode($response->getBody()->getContents(), true);

        if (! $data['success']) {
            return new \WP_Error('id_not_found', $data['message'], ['status' => 404]);
        }

        $ubigeo = $data['data']['ubigeo'];

        return new RucResponse(
            $data['data']['nombre_o_razon_social'],
            $data['data']['direccion_completa'],
            is_array($ubigeo) ? $ubigeo[count($ubigeo) - 1] : null,
        );
    }
}
