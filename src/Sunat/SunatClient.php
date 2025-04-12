<?php

namespace Factpro\Sunat;

use Factpro\Sunat\Response\DniResponse;
use Factpro\Sunat\Response\RucResponse;

interface SunatClient
{
    /**
     * @return DniResponse|\WP_Error
     */
    public function findPersonByDni($dni);

    /**
     * @return RucResponse|\WP_Error
     */
    public function findCompanyByRuc($ruc);
}
