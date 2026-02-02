<?php

namespace Factpro\WP\AdminPanel;

defined('ABSPATH') || exit;

final class WCSettingsTab
{
    public static function getTitle()
    {
        return __('Facturación Electrónica', 'factpro-for-woocommerce');
    }

    public static function getSettings()
    {
        return [
            'section_title' => [
                'name'     => __('Configuración General', 'factpro-for-woocommerce'),
                'type'     => 'title',
                'id'       => 'wc_settings_factpro_section_title'
            ],

            // 'base_url' => [
            //     'name' => __('Factpro Base Url', 'factpro-for-woocommerce'),
            //     'type' => 'url',
            //     'id'   => 'wc_settings_factpro_base_url',
            //     'default' => 'https://dev.factpro.la',
            //     'custom_attributes' => [
            //         'readonly' => 'true',
            //     ],
            // ],

            'version' => [
                'name' => __('Factpro API Version', 'factpro-for-woocommerce'),
                'type' => 'select',
                'options' => [
                    'v2' => 'v2',
                    'v3' => 'v3',
                ],
                'id'   => 'wc_settings_factpro_api_version',
                'default' => 'v3'
            ],

            'token' => [
                'name' => __('Factpro Token', 'factpro-for-woocommerce'),
                'type' => 'text',
                'id'   => 'wc_settings_factpro_token'
            ],

            'siglafactura' => [
                'name' => __('Serie - Factura', 'factpro-for-woocommerce'),
                'type' => 'text',
                'id'   => 'wc_settings_factpro_siglafactura'
            ],

            'nsiglafactura' => [
                'name' => __('Correlativo - Factura', 'factpro-for-woocommerce'),
                'type' => 'text',
                'id'   => 'wc_settings_factpro_nsiglafactura'
            ],

            'bsiglafactura' => [
                'name' => __('Serie - Boleta', 'factpro-for-woocommerce'),
                'type' => 'text',
                'id'   => 'wc_settings_factpro_bsiglafactura'
            ],

            'bnsiglafactura' => [
                'name' => __('Correlativo - Boleta', 'factpro-for-woocommerce'),
                'type' => 'text',
                'id'   => 'wc_settings_factpro_bnsiglafactura'
            ],

            'invoice_is_mandatory' => [
                'name' => __('Comprobantes obligatorios', 'factpro-for-woocommerce'),
                'type' => 'checkbox',
                'desc' => __('Todos los pedidos deben tener comprobantes electrónicos.', 'factpro-for-woocommerce'),
                'id'   => 'wc_settings_factpro_invoice_is_mandatory'
            ],

            'send_email_automatically' => [
                'name' => __('Envío de correo electrónico', 'factpro-for-woocommerce'),
                'type' => 'checkbox',
                'desc' => __('Permite enviar automáticamente los comprobantes electrónicos a los clientes.', 'factpro-for-woocommerce'),
                'id'   => 'wc_settings_factpro_send_email_automatically',
                'default' => 'yes',
            ],

            'testmode' => [
                'name' => __('Modo Test', 'factpro-for-woocommerce'),
                'type' => 'checkbox',
                'desc' => __('Las boletas y facturas no se incrementan automáticamente', 'factpro-for-woocommerce'),
                'id'   => 'wc_settings_factpro_testmode'
            ],

            'order_note_as_attribute' => [
                'name' => __('Nota de Pedido', 'factpro-for-woocommerce'),
                'type' => 'checkbox',
                'desc' => __('Enviar la nota de pedido como observaciones en el comprobante', 'factpro-for-woocommerce'),
                'id'   => 'wc_settings_factpro_order_note_as_comment'
            ],

            'section_end' => [
                'type' => 'sectionend',
                'id' => 'wc_settings_factpro_section_end'
            ],

            'client_section_title' => [
                'name'     => __('API para la búsqueda de DNI/RUC', 'factpro-for-woocommerce'),
                'type'     => 'title',
                'id'       => 'wc_settings_factpro_client_section_title'
            ],

            'client_types' => [
                'name' => __('Cliente API', 'factpro-for-woocommerce'),
                'type' => 'select',
                'options' => [
                    'factpro' => 'Factpro API (https://docs.factpro.la/api-consulta-ruc-y-dni)',
                    'migo' => 'API MIGO (https://api.migo.pe)',
                    'perudev' => 'API PERU DEV (https://apiperu.dev)',
                ],
                'id'   => 'wc_settings_factpro_client_types'
            ],

            'client_token' => [
                'name' => __('Token', 'factpro-for-woocommerce'),
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
