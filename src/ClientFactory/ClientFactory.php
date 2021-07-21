<?php

namespace EBilling\ClientFactory;

use EBilling\Contract\Client;

final class ClientFactory
{
    static $clientTypes = [
        'migo' => MigoApi::class,
        'perudev' => PeruDevApi::class,
    ];

    /**
     * @param string $client
     * 
     * @return Client
     */
    public static function makeClient($client)
    {
        if (! isset(self::$clientTypes[$client])) {
            throw new \Exception('Client API not supported.');
        }

        return new self::$clientTypes[$client];
    }
}
