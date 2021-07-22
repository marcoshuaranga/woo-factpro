<?php

namespace EBilling\Action;

use EBilling\Sunat\SunatClientFactory;

final class GetDniAction
{
    public function __invoke(\WP_REST_Request $request)
    {
        $sunat = SunatClientFactory::createClient(
            get_option('wc_settings_ebilling_client_types')
        );

        $response = $sunat->findPersonByDni($request->get_param('dni'));

        if (is_wp_error($response)) {
            return $response;
        }

        return new \WP_REST_Response($response->toArray());
    }
}
