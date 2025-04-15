<?php

namespace Factpro\Sunat\Client;

use Factpro\Sunat\Response\DniResponse;
use Factpro\Sunat\Response\RucResponse;
use Factpro\Sunat\SunatClient;

final class FactproClient implements SunatClient
{
    private string $baseUrl = 'https://consultas.factpro.la/api/v1';
    private string $token;

    public function __construct()
    {
        $this->token = get_option('wc_settings_factpro_client_token');
    }

    public function findPersonByDni(string $dni)
    {
        $response = wp_remote_get($this->baseUrl . "/dni/$dni", [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ]
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $statusCode = wp_remote_retrieve_response_code($response);
        $jsonResponse = wp_remote_retrieve_body($response);

        $data = json_decode($jsonResponse, true);

        if ($statusCode >= 400) {
            return new \WP_Error('id_not_found', $data['detail'], ['status' => $statusCode]);
        }

        $lastnames_and_firstnames = explode(' ', $data['nombres']);

        return new DniResponse(
            implode(' ', array_slice($lastnames_and_firstnames, 2)),
            $lastnames_and_firstnames[0],
            $lastnames_and_firstnames[1]
        );
    }

    public function findCompanyByRuc(string $ruc)
    {
        $response = wp_remote_get($this->baseUrl . "/ruc/$ruc", [
            'headers' => [
                'Accept' => "application/json",
                'Authorization' => "Bearer $this->token",
                'Content-Type' => "application/json",
            ]
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $statusCode = wp_remote_retrieve_response_code($response);
        $jsonResponse = wp_remote_retrieve_body($response);

        $data = json_decode($jsonResponse, true);

        if ($statusCode >= 400) {
            return new \WP_Error('id_not_found', $data['detail'], ['status' => $statusCode]);
        }

        return new RucResponse(
            $data['nombre'],
            $data['direccion_completa'],
            $data['ubigeo']
        );
    }
}
