<?php

namespace Factpro\InvoiceFormatter;

use Factpro\Domain\Discount;
use Factpro\Domain\Invoice;
use Factpro\Domain\InvoiceItem;

final class FactProFormatter
{
    private Invoice $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function toArray()
    {
        $invoiceItems = $this->invoice->getInvoiceItems();

        return [
            'tipo_documento' => $this->invoice->getDocumentType(),
            'serie' => $this->invoice->getSerie(),
            'numero' => $this->invoice->getNumber(),
            'tipo_operacion' => '0101',
            'fecha_de_emision' => $this->invoice->getDate()->format('Y-m-d'),
            'hora_de_emision' => $this->invoice->getDate()->format('H:i:s'),
            'moneda' => 'PEN',
            'porcentaje_de_venta' => 18.00,
            'fecha_de_vencimiento' => $this->invoice->getDueDate()->format('Y-m-d H:i:s'),
            'enviar_automaticamente_al_cliente' => false,
            'forma_de_pago' => '',
            'numero_orden' => "{$this->invoice->getOrderId()}",
            'codigo' => '',
            'datos_del_emisor' => [
                'codigo_establecimiento' => '0000',
            ],
            'cliente' => [
                'cliente_tipo_documento' => $this->invoice->getCustomer()->getDocumentType(),
                'cliente_numero_documento' => $this->invoice->getCustomer()->getDocumentNumber(),
                'cliente_denominacion' => $this->invoice->getCustomer()->getNameOrCompany(),
                'codigo_pais' => $this->invoice->getCustomer()->getCountryCode(),
                'ubigeo' => $this->invoice->getCustomer()->getPostalCode(),
                'cliente_direccion' => $this->invoice->getCustomer()->getAddress(),
                'cliente_email' => $this->invoice->getCustomer()->getEmail(),
                'cliente_telefono' => $this->invoice->getCustomer()->getPhoneNumber(),
            ],
            'totales' => [
                'total_exportacion' => round(0, 2),
                'total_gravadas' => round($invoiceItems->getSunatTotalGravado(), 2),
                'total_inafectas' => round(0, 2),
                'total_exoneradas' => round($invoiceItems->getSunatTotalExonerado(), 2),
                'total_gratuitas' => round(0, 2),
                'total_otros_cargos' => round(0, 2),
                'total_tax' => round($invoiceItems->getSunatTotalIgv(), 2),
                'total_venta' => round($invoiceItems->getTotal(), 2),
            ],
            'items' => array_map(function (InvoiceItem $item) {
                return [
                    'unidad' => $item->getUnitOfMeasure(),
                    'codigo' => $item->getId(),
                    'descripcion' => $item->getDescription(),
                    'codigo_producto_sunat' => '',
                    'codigo_producto_gsl' => '',
                    'cantidad' => $item->getQuantity(),
                    'valor_unitario' => $item->getUnitValue(),
                    'precio_unitario' => $item->getUnitPrice(),
                    'tipo_tax' => $item->isGravado() ? '10' : '20',
                    'total_base_tax' => round($item->getSubtotal(), 2),
                    'total_tax' => round($item->getTotalTax(), 2),
                    'total' => round($item->getTotal(), 2),
                ];
            }, $invoiceItems->getItems()),
            'acciones' => [
                'formato_pdf' => 'a4',
            ],
            'termino_de_pago' => [
                'descripcion' => 'Contado',
                'tipo' => '0',
            ],
            'metodo_de_pago' => $this->invoice->getPaymentMethod()->getTitle(),
            'canal_de_venta' => 'WooCommerce',
            'orden_de_compra' => $this->invoice->getOrderId(),
            'almacen' => '',
            'observaciones' => '',
        ];
    }

    public function toArrayOld()
    {
        $invoiceItems = $this->invoice->getInvoiceItems();

        return [
            'serie_documento' => $this->invoice->getSerie(),
            'numero_documento' => $this->invoice->getNumber(),
            'fecha_de_emision' => $this->invoice->getDate()->format('Y-m-d'),
            'hora_de_emision' => $this->invoice->getDate()->format('H:i:s'),
            'codigo_tipo_operacion' => '0101',
            'codigo_tipo_documento' => $this->invoice->getDocumentType(),
            'codigo_tipo_moneda' => 'PEN',
            'fecha_de_vencimiento' => $this->invoice->getDueDate()->format('Y-m-d H:i:s'),
            'numero_orden_de_compra' => "{$this->invoice->getOrderId()}",
            'nombre_almacen' => 'Almacen Virtual',
            'datos_del_emisor' => [
                'codigo_del_domicilio_fiscal' => '0000',
            ],
            'datos_del_cliente_o_receptor' => [
                'codigo_pais' => $this->invoice->getCustomer()->getCountryCode(),
                'codigo_tipo_documento_identidad' => $this->invoice->getCustomer()->getDocumentType(),
                'numero_documento' => $this->invoice->getCustomer()->getDocumentNumber(),
                'apellidos_y_nombres_o_razon_social' => $this->invoice->getCustomer()->getNameOrCompany(),
                'ubigeo' => $this->invoice->getCustomer()->getPostalCode(),
                'direccion' => $this->invoice->getCustomer()->getAddress(),
                'correo_electronico' => $this->invoice->getCustomer()->getEmail(),
                'telefono' => $this->invoice->getCustomer()->getPhoneNumber(),
            ],
            'items' => $this->formatItems($this->invoice->getInvoiceItems()->getItems()),
            'totales' => [
                'total_exportacion' => round(0, 2),
                'total_descuentos' => 0,
                'total_operaciones_gravadas' => round($invoiceItems->getSunatTotalGravado(), 2),
                'total_operaciones_inafectas' => round(0, 2),
                'total_operaciones_exoneradas' => round($invoiceItems->getSunatTotalExonerado(), 2),
                'total_operaciones_gratuitas' => round(0, 2),
                'total_igv' => round($invoiceItems->getSunatTotalIgv(), 2),
                'total_impuestos' => round($invoiceItems->getTotalTax(), 2),
                'total_valor' => round($invoiceItems->getSubtotal(), 2),
                'total_venta' => round($invoiceItems->getTotal(), 2),
            ],
            'descuentos' => $this->formatDiscounts($invoiceItems->getDiscounts()),
            'acciones' => [
                'formato_pdf' => 'a4'
            ],
            'additional_information' => '',
            'termino_de_pago' => [
                'descripcion' => 'Contado',
                'tipo' => '0'
            ],
            'metodo_de_pago' => '',
        ];
    }

    private function formatItems(array $items)
    {
        return array_map(function (InvoiceItem $item) {
            return [
                'codigo_interno' => $item->getId(),
                'descripcion' => $item->getDescription(),
                'codigo_producto_sunat' => '',
                'unidad_de_medida' => $item->getUnitOfMeasure(),
                'cantidad' => $item->getQuantity(),
                'valor_unitario' => $item->getUnitValue(),
                'codigo_tipo_precio' => '01',
                'precio_unitario' => $item->getUnitPrice(),
                'codigo_tipo_afectacion_igv' => $item->isGravado() ? '10' : '20',
                'total_base_igv' => round($item->getSubtotal(), 2),
                'porcentaje_igv' => ($item->isGravado()) ? 18 : 0,
                'total_igv' => round($item->getTotalTax(), 2),
                'total_impuestos' => round($item->getTotalTax(), 2),
                'total_valor_item' => round($item->getSubtotal(), 2),
                'total_item' => round($item->getTotal(), 2),
            ];
        }, $items);
    }

    private function formatDiscounts(array $discounts)
    {
        return array_map(function (Discount $item) {
            return [
                'codigo' => $item->getCode(),
                'descripcion' => $item->getCode(),
                'porcentaje' => 1,
                'monto' => round($item->getSubtotal(), 2),
                'base' => round($item->getSubtotal(), 2),
            ];
        }, $discounts);
    }
}
