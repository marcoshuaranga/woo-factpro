<?php

namespace EBilling\InvoiceFormatter;

use EBilling\Domain\Invoice;
use EBilling\Domain\InvoiceItem;

final class PseFormatter
{
    private Invoice $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function toArray()
    {
        return array_merge($this->invoice->getInvoiceSummary()->toArray(), [
            'serie_documento' => $this->invoice->getSerie(),
            'numero_documento' => $this->invoice->getNumber(),
            'fecha_de_emision' => $this->invoice->getDate()->format('Y-m-d'),
            'hora_de_emision' => $this->invoice->getDate()->format('H:i:s'),
            'codigo_tipo_operacion' => '0101',
            'codigo_tipo_documento' => $this->invoice->getDocumentType(),
            'codigo_tipo_moneda' => 'PEN',
            'fecha_de_vencimiento' => $this->invoice->getDueDate()->format('Y-m-d H:i:s'),
            'numero_orden_de_compra' => $this->invoice->getOrderId(),
            'nombre_almacen' => 'Almacén - Oficina Principal',
            'datos_del_emisor' => [
                'codigo_del_domicilio_fiscal' => '0000',
            ],
            'datos_del_cliente_o_receptor' => [
                'codigo_pais' => $this->invoice->getCustomer()->getCountryCode(),
                'codigo_tipo_documento_identidad' => $this->invoice->getCustomer()->getDocumentType(),
                'numero_documento' => $this->invoice->getCustomer()->getDocumentNumber(),
                'apellidos_y_nombres_o_razon_social' => $this->invoice->getCustomer()->getNameOrCompany(),
                'ubigeo' => $this->invoice->getCustomer()->getPostalCode() ?? '150101',
                'direccion' => $this->invoice->getCustomer()->getAddress(),
                'correo_electronico' => $this->invoice->getCustomer()->getEmail(),
                'telefono' => $this->invoice->getCustomer()->getPhoneNumber(),
            ],
            'metodo_de_pago' => 'Efectivo',
            'termino_de_pago' => [
                'descripcion' => 'Contado',
                'tipo' => '0'
            ],
            'items' => $this->formatItems($this->invoice->getItemsCollection()->getItems()),
        ]);
    }

    private function formatItems(array $items)
    {
        return array_map(function (InvoiceItem $item) {
            return [
                'unidad_de_medida' => $item->getUnitOfMeasure(),
                'codigo_interno' => $item->getSku(),
                'descripcion' => $item->getDescription(),
                'codigo_producto_sunat' => '',
                'cantidad' => $item->getQuantity(),
                'valor_unitario' => $item->getUnitValue(),
                'codigo_tipo_precio' => '01',
                'precio_unitario' => $item->getUnitPrice(),
                'codigo_tipo_afectacion_igv' => $item->getTaxExemptionReasonCode(),
                'total_base_igv' => round($item->getSubtotal(), 2),
                'porcentaje_igv' => ($item->getTotalIgv() > 0) ? 18 : 0,
                'total_igv' => round($item->getTotalIgv(), 2),
                'total_impuestos' => round($item->getTotalImpuestos(), 2),
                'total_valor_item' => round($item->getTotalValorItem(), 2),
                'total_item' => round($item->getTotal(), 2),
            ];
        }, $items);
    }
}