<?php

namespace EBilling\Sunat\Client;

use EBilling\Sunat\Response\DniResponse;
use EBilling\Sunat\Response\RucResponse;
use EBilling\Sunat\SunatClient;

final class PeruDevApiClient implements SunatClient
{
    public function findPersonByDni($dni)
    {
        $token = get_option('wc_settings_ebilling_client_token');
        $url = sprintf('https://apiperu.dev/api/dni/%s', $dni);

        $response = wp_remote_get($url, [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
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

        return new DniResponse(
            $data['data']['nombres'],
            $data['data']['apellido_paterno'],
            $data['data']['apellido_materno'],
        );
    }

    public function findCompanyByRuc($ruc)
    {
        $token = get_option('wc_settings_ebilling_client_token');
        $url = sprintf('https://apiperu.dev/api/ruc/%s', $ruc);

        $response = wp_remote_get($url, [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
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

        $ubigeo = $data['data']['ubigeo'];

        return new RucResponse(
            $data['data']['nombre_o_razon_social'],
            $data['data']['direccion'],
            is_array($ubigeo) ? $ubigeo[count($ubigeo) - 1] : null,
        );
    }
}
