<?php

namespace Factpro\WP;

defined('ABSPATH') || exit;

use Factpro\Action\GetDniAction;
use Factpro\Action\GetRucAction;

final class RestApiHooks
{
    public static function init()
    {
        add_action('rest_api_init', function () {
            register_rest_route('factpro-for-woocommerce/v1', '/dni/(?P<dni>\d+)', [
                'methods'  => 'GET',
                'callback' => new GetDniAction(),
                'permission_callback' => '__return_true',
                'args' => [
                    'dni' => ['validate_callback' => function ($value) {
                        return is_numeric($value);
                    }],
                ],
            ]);

            register_rest_route('factpro-for-woocommerce/v1', '/ruc/(?P<ruc>\d+)', [
                'methods'  => 'GET',
                'callback' => new GetRucAction(),
                'permission_callback' => '__return_true',
                'args' => [
                    'ruc' => ['validate_callback' => function ($value) {
                        return is_numeric($value);
                    }],
                ],
            ]);
        });
    }
}
