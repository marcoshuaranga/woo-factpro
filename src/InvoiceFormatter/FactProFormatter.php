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
        $discounts = $invoiceItems->getDiscounts();

        return array_merge([
            'tipo_documento' => $this->invoice->getDocumentType(),
            'serie' => $this->invoice->getSerie(),
            'numero' => $this->invoice->getNumber(),
            'tipo_operacion' => '0101',
            'fecha_de_emision' => $this->invoice->getDate()->format('Y-m-d'),
            'hora_de_emision' => $this->invoice->getDate()->format('H:i:s'),
            'fecha_de_vencimiento' => $this->invoice->getDueDate()->format('Y-m-d'),
            'moneda' => 'PEN',
            'porcentaje_de_venta' => 18.00,
            'forma_de_pago' => '',
            'numero_orden' => "{$this->invoice->getOrderId()}",
            'codigo' => '',
            'enviar_automaticamente_al_cliente' => false,
            'datos_del_emisor' => [
                'codigo_establecimiento' => '0000',
            ],
            'cliente' => [
                'cliente_tipo_documento' => $this->invoice->getCustomer()->getDocumentType(),
                'cliente_numero_documento' => $this->invoice->getCustomer()->getDocumentNumber(),
                'cliente_denominacion' => $this->invoice->getCustomer()->getNameOrCompany(),
                'codigo_pais' => '',
                'ubigeo' => '',
                'cliente_direccion' => $this->invoice->getCustomer()->getAddress(),
                'cliente_email' => $this->invoice->getCustomer()->getEmail(),
                'cliente_telefono' => $this->invoice->getCustomer()->getPhoneNumber(),
            ],
            'totales' => count($discounts) ? [
                'total_exportacion' => round(0, 2),
                'total_gravadas' => round($invoiceItems->getSunatTotalGravadas() - $this->invoice->getOrderSummary()->getTotalDiscountForSunat(), 2),
                'total_inafectas' => round(0, 2),
                'total_exoneradas' => round($invoiceItems->getSunatTotalExonerado(), 2),
                'total_gratuitas' => round(0, 2),
                'total_otros_cargos' => round(0, 2),
                'total_tax' => round($invoiceItems->getSunatTotalTax() - $this->invoice->getOrderSummary()->getTotalDiscountForSunat() * 0.18, 2),
                'total_venta' => round($this->invoice->getOrderSummary()->getTotalPaid(), 2),
                'descuentos' => [
                    'codigo' => '02',
                    'descripcion' => sprintf("Descuentos (%s)", implode(', ', array_map(function (Discount $discount) {
                        return $discount->getCode();
                    }, $discounts))),
                    'porcentaje' => round($this->invoice->getOrderSummary()->getPercentDiscountForSunat(), 2),
                    'monto' => round($this->invoice->getOrderSummary()->getTotalDiscountForSunat(), 2),
                    'base' => round($invoiceItems->getSunatTotalGravadas(), 2),
                ],
            ] : [
                'total_exportacion' => round(0, 2),
                'total_gravadas' => round($invoiceItems->getSunatTotalGravadas(), 2),
                'total_inafectas' => round(0, 2),
                'total_exoneradas' => round($invoiceItems->getSunatTotalExonerado(), 2),
                'total_gratuitas' => round(0, 2),
                'total_otros_cargos' => round(0, 2),
                'total_tax' => round($invoiceItems->getSunatTotalTax(), 2),
                'total_venta' => round($invoiceItems->getTotal(), 2),
            ],
            'items' => $this->formatItems($invoiceItems->getItems()),
            'acciones' => [
                'formato_pdf' => 'a4',
            ],
            'termino_de_pago' => [
                'descripcion' => 'Contado',
                'tipo' => '0',
            ],
            'metodo_de_pago' => $this->invoice->getPaymentMethod()->getTitle(),
            'canal_de_venta' => 'WooCommerce',
            'orden_de_compra' => '',
            'almacen' => '',
            'observaciones' => '',
        ],);
    }

    private function formatItems(array $items)
    {
        return array_map(function (InvoiceItem $item) {
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
        }, $items);
    }
}
