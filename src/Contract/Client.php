<?php

namespace EBilling\Contract;

use EBilling\Response\DniResponse;
use EBilling\Response\RucResponse;
use GuzzleHttp\Client as GuzzleHttpClient;
use WP_Error;

interface Client {
    /**
     * @return DniResponse|WP_Error
     */
    public function findByDni($dni);

    /**
     * @return RucResponse|WP_Error
     */
    public function findByRuc($ruc);

    /**
     * @return GuzzleHttpClient
     */
    public function makeHttp();
}
