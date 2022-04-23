<?php

namespace EBilling\Admin;

final class WCSettingsTab
{
    public static function getTitle()
    {
       return __( 'Facturación Electrónica', 'woo-ebilling');
    }

    public static function getSettings()
    {
        return [
            'section_title' => [
                'name'     => __( 'Configuración General', 'woo-ebilling'),
                'type'     => 'title',
                'id'       => 'wc_settings_ebilling_section_title'
            ],

            'url_api' => [
                'name' => __('URL', 'woo-ebilling'),
                'type' => 'text',
                'id'   => 'wc_settings_ebilling_url_api'
            ],

            'token' => [
                'name' => __('Token', 'woo-ebilling'),
                'type' => 'text',
                'id'   => 'wc_settings_ebilling_token'
            ],

            'siglafactura' => [
                'name' => __('Serie - Factura', 'woo-ebilling'),
                'type' => 'text',
                'id'   => 'wc_settings_ebilling_siglafactura'
            ],

            'nsiglafactura' => [
                'name' => __('Correlativo - Factura', 'woo-ebilling'),
                'type' => 'text',
                'id'   => 'wc_settings_ebilling_nsiglafactura'
            ],

            'bsiglafactura' => [
                'name' => __('Serie - Boleta', 'woo-ebilling'),
                'type' => 'text',
                'id'   => 'wc_settings_ebilling_bsiglafactura'
            ],

            'bnsiglafactura' => [
                'name' => __('Correlativo - Boleta', 'woo-ebilling'),
                'type' => 'text',
                'id'   => 'wc_settings_ebilling_bnsiglafactura'
            ],

            'invoice_is_mandatory' => [
                'name' => __('Comprobantes obligatorios', 'woo-ebilling'),
                'type' => 'checkbox',
                'desc' => __('Todos los pedidos deben tener comprobantes electrónicos.', 'woo-ebilling'),
                'id'   => 'wc_settings_ebilling_invoice_is_mandatory'
            ],

            'testmode' => [
                'name' => __('Modo Test', 'woo-ebilling'),
                'type' => 'checkbox',
                'desc' => __('Las boletas y facturas no se incrementan automáticamente', 'woo-ebilling'),
                'id'   => 'wc_settings_ebilling_testmode'
            ],

            'section_end' => [
                'type' => 'sectionend',
                'id' => 'wc_settings_ebilling_section_end'
            ],

            'client_section_title' => [
                'name'     => __('API para la búsqueda de DNI/RUC', 'woo-ebilling'),
                'type'     => 'title',
                'id'       => 'wc_settings_ebilling_client_section_title'
            ],

            'client_types' => [
                'name' => __('Cliente API', 'woo-ebilling'),
                'type' => 'select',
                'options' => [
                    'migo' => 'API MIGO (https://api.migo.pe)',
                    'perudev' => 'API PERU DEV (https://apiperu.dev)',
                ],
                'id'   => 'wc_settings_ebilling_client_types'
            ],

            'client_token' => [
                'name' => __('Token', 'woo-ebilling'),
                'type' => 'text',
                'id'   => 'wc_settings_ebilling_client_token'
            ],

            'client_section_end' => [
                'type' => 'sectionend',
                'id' => 'wc_settings_ebilling_client_section_end'
            ],
        ];
    }
}
