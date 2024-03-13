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
        $token = get_option('wc_settings_ebilling_client_token');
        $url = 'https://api.migo.pe/api/v1/dni';

        $response = wp_remote_post($url, [
            'body' => json_encode([
                'token' => $token,
                'dni' => $dni,
            ]),
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $statusCode = wp_remote_retrieve_response_code($response);
        $jsonResponse = wp_remote_retrieve_body($response);

        $data = json_decode($jsonResponse, true);

        if (! $data['success']) {
            return new \WP_Error('id_not_found', $data['message'], ['status' => $statusCode]);
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
        $token = get_option('wc_settings_ebilling_client_token');
        $url = 'https://api.migo.pe/api/v1/ruc';

        $response = wp_remote_post($url, [
            'body' => json_encode([
                'token' => $token,
                'ruc' => $ruc,
            ]),
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $statusCode = wp_remote_retrieve_response_code($response);
        $jsonResponse = wp_remote_retrieve_body($response);

        $data = json_decode($jsonResponse, true);

        if (! $data['success']) {
            return new \WP_Error('id_not_found', $data['message'], ['status' => $statusCode]);
        }

        return new RucResponse($data['nombre_o_razon_social'], $data['direccion_simple'], $data['ubigeo']);
    }
}
