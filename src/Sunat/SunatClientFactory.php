<?php

namespace Factpro\Sunat;

use Factpro\Sunat\Client\FactproClient;
use Factpro\Sunat\Client\MigoApiClient;
use Factpro\Sunat\Client\PeruDevApiClient;

final class SunatClientFactory
{
    static $clientTypes = [
        'factpro' => FactproClient::class,
        'migo' => MigoApiClient::class,
        'perudev' => PeruDevApiClient::class,
    ];

    /**
     * @param string $client
     * 
     * @return SunatClient
     */
    public static function createClient($clientType)
    {
        $className = self::$clientTypes[$clientType];

        if (! isset($className)) {
            throw new \Exception('Client API not supported.');
        }

        return new $className();
    }
}
