<?php

namespace Factpro\WP\AdminPanel;

final class WCSettingsTab
{
    public static function getTitle()
    {
        return __('Facturación Electrónica', 'woo-factpro');
    }

    public static function getSettings()
    {
        return [
            'section_title' => [
                'name'     => __('Configuración General', 'woo-factpro'),
                'type'     => 'title',
                'id'       => 'wc_settings_factpro_section_title'
            ],

            'url_api' => [
                'name' => __('URL', 'woo-factpro'),
                'type' => 'text',
                'id'   => 'wc_settings_factpro_url_api'
            ],

            'token' => [
                'name' => __('Token', 'woo-factpro'),
                'type' => 'text',
                'id'   => 'wc_settings_factpro_token'
            ],

            'siglafactura' => [
                'name' => __('Serie - Factura', 'woo-factpro'),
                'type' => 'text',
                'id'   => 'wc_settings_factpro_siglafactura'
            ],

            'nsiglafactura' => [
                'name' => __('Correlativo - Factura', 'woo-factpro'),
                'type' => 'text',
                'id'   => 'wc_settings_factpro_nsiglafactura'
            ],

            'bsiglafactura' => [
                'name' => __('Serie - Boleta', 'woo-factpro'),
                'type' => 'text',
                'id'   => 'wc_settings_factpro_bsiglafactura'
            ],

            'bnsiglafactura' => [
                'name' => __('Correlativo - Boleta', 'woo-factpro'),
                'type' => 'text',
                'id'   => 'wc_settings_factpro_bnsiglafactura'
            ],

            'invoice_is_mandatory' => [
                'name' => __('Comprobantes obligatorios', 'woo-factpro'),
                'type' => 'checkbox',
                'desc' => __('Todos los pedidos deben tener comprobantes electrónicos.', 'woo-factpro'),
                'id'   => 'wc_settings_factpro_invoice_is_mandatory'
            ],

            'testmode' => [
                'name' => __('Modo Test', 'woo-factpro'),
                'type' => 'checkbox',
                'desc' => __('Las boletas y facturas no se incrementan automáticamente', 'woo-factpro'),
                'id'   => 'wc_settings_factpro_testmode'
            ],

            'section_end' => [
                'type' => 'sectionend',
                'id' => 'wc_settings_factpro_section_end'
            ],

            'client_section_title' => [
                'name'     => __('API para la búsqueda de DNI/RUC', 'woo-factpro'),
                'type'     => 'title',
                'id'       => 'wc_settings_factpro_client_section_title'
            ],

            'client_types' => [
                'name' => __('Cliente API', 'woo-factpro'),
                'type' => 'select',
                'options' => [
                    'migo' => 'API MIGO (https://api.migo.pe)',
                    'perudev' => 'API PERU DEV (https://apiperu.dev)',
                ],
                'id'   => 'wc_settings_factpro_client_types'
            ],

            'client_token' => [
                'name' => __('Token', 'woo-factpro'),
                'type' => 'text',
                'id'   => 'wc_settings_factpro_client_token'
            ],

            'client_section_end' => [
                'type' => 'sectionend',
                'id' => 'wc_settings_factpro_client_section_end'
            ],
        ];
    }
}
