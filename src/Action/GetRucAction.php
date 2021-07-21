<?php

namespace EBilling\Action;

use EBilling\ClientFactory\ClientFactory;
use WP_REST_Request;
use WP_REST_Response;

final class GetRucAction
{
    public function __invoke(WP_REST_Request $request)
    {
        $client = get_option('wc_settings_ebilling_client_types');
        $response = ClientFactory::makeClient($client)->findByRuc($request->get_param('ruc'));

        if (is_wp_error($response)) {
            return $response;
        }

        return new WP_REST_Response($response->toArray());
    }
}
