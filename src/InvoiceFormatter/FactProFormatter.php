<?php

namespace EBilling\InvoiceFormatter;

use EBilling\Domain\Discount;
use EBilling\Domain\Invoice;
use EBilling\Domain\InvoiceItem;

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
                'codigo_del_domicilio_fiscal' => '000',
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
            'totales' =>[
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
            'additional_information' => 'Compra:Online|Web',
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