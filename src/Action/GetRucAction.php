<?php

namespace Factpro\Action;

use Factpro\Sunat\SunatClientFactory;

final class GetRucAction
{
    public function __invoke(\WP_REST_Request $request)
    {
        $sunat = SunatClientFactory::createClient(
            get_option('wc_settings_factpro_client_types')
        );

        $response = $sunat->findCompanyByRuc($request->get_param('ruc'));

        if (is_wp_error($response)) {
            return $response;
        }

        return new \WP_REST_Response($response->toArray());
    }
}
