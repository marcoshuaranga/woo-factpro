<?php

namespace EBilling\Sunat;

use EBilling\Sunat\Client\MigoApiClient;
use EBilling\Sunat\Client\PeruDevApiClient;

final class SunatClientFactory
{
    static $clientTypes = [
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
