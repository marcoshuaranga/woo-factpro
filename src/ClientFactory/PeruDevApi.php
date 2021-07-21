<?php

namespace EBilling\ClientFactory;

use EBilling\Contract\Client;
use EBilling\Response\DniResponse;
use EBilling\Response\RucResponse;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\RequestException;
use WP_Error;

final class PeruDevApi implements Client
{
    public function findByDni($dni)
    {
        try {
            $response = $this->makeHttp()->get('dni/' . $dni);
        } catch (RequestException $e) {
            return new WP_Error('guzzle_request_exception', $e->getMessage(), ['status' => $e->getResponse()->getStatusCode()]);
        }

        $data = json_decode($response->getBody()->getContents(), true);

        if (! $data['success']) {
            return new WP_Error('id_not_found', $data['message'], ['status' => 404]);
        }

        return new DniResponse(
            $data['data']['nombres'],
            $data['data']['apellido_paterno'],
            $data['data']['apellido_materno'],
        );
    }

    public function findByRuc($ruc)
    {
        try {
            $response = $this->makeHttp()->get('ruc/' . $ruc);
        } catch (RequestException $e) {
            return new WP_Error('guzzle_request_exception', $e->getMessage(), ['status' => $e->getResponse()->getStatusCode()]);
        }

        $data = json_decode($response->getBody()->getContents(), true);

        if (! $data['success']) {
            return new WP_Error('id_not_found', $data['message'], ['status' => 404]);
        }

        return new RucResponse($data['data']['nombre_o_razon_social'], $data['data']['direccion_completa']);
    }

    public function makeHttp()
    {
        return new GuzzleHttpClient([
            'base_uri' => 'https://apiperu.dev/api/',
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . get_option('wc_settings_ebilling_client_token'),
                'Content-Type' => 'application/json',
            ],
        ]);
    }
}
