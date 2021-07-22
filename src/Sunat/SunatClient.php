<?php

namespace EBilling\Sunat;

use EBilling\Sunat\Response\DniResponse;
use EBilling\Sunat\Response\RucResponse;

interface SunatClient {
    /**
     * @return DniResponse|\WP_Error
     */
    public function findPersonByDni($dni);

    /**
     * @return RucResponse|\WP_Error
     */
    public function findCompanyByRuc($ruc);
}
