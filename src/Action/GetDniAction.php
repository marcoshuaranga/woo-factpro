<?php

namespace EBilling\Action;

use EBilling\ClientFactory\ClientFactory;
use WP_REST_Request;
use WP_REST_Response;

final class GetDniAction
{
    public function __invoke(WP_REST_Request $request)
    {
        $client = get_option('wc_settings_ebilling_client_types');
        $response = ClientFactory::makeClient($client)->findByDni($request->get_param('dni'));

        if (is_wp_error($response)) {
            return $response;
        }

        return new WP_REST_Response($response->toArray());
    }
}
