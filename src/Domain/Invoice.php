<?php

namespace EBilling\Domain;

use EBilling\SunatCode\InvoiceType;
use WC_Order;

final class Invoice
{
    private $documentType;

    private $serie;

    private $number;

    private $date;

    private $dueDate;

    private $orderId;

    private $customer;

    private $itemsCollection;

    private $invoiceSummary;

    public function __construct(
        $documentType,
        $serie,
        $number,
        $orderId,
        Customer $customer,
        InvoiceItemsCollection $itemsCollection,
        InvoiceSummary $invoiceSummary
    ) {
        $this->documentType = $documentType;
        $this->serie = $serie;
        $this->number = $number;
        $this->date = new \DateTimeImmutable('now');
        $this->dueDate = $this->date->add(new \DateInterval('P10D')); //add 10 days
        $this->orderId = $orderId;
        $this->customer = $customer;
        $this->itemsCollection = $itemsCollection;
        $this->invoiceSummary = $invoiceSummary;
    }

    public static function createFromWooOrder($serie, $number, WC_Order $order, $includeTax)
    {
        $invoiceType = $order->get_meta('_ebilling_invoice_type');
        $nameOrCompany = "{$order->get_billing_first_name()} {$order->get_billing_last_name()}";
        $address = $order->get_billing_address_1();
        $ubigeo = null;

        if (InvoiceType::is_factura($invoiceType)) {
            $nameOrCompany = $order->get_meta('_ebilling_company_name');
            $address = $order->get_meta('_ebilling_company_address');
            $ubigeo = $order->get_meta('_ebilling_company_ubigeo');
        }

        $customer = new Customer(
            $order->get_meta('_ebilling_customer_document_type'),
            $order->get_meta('_ebilling_customer_document_number'),
            $nameOrCompany, 
            $address, 
            $order->get_billing_email(),
            $ubigeo,
            $order->get_billing_phone(),
        );

        $lineItems = $order->get_items('line_item');
        $feeItems = $order->get_items('fee');
        $shippingItems = $order->get_items('shipping');

        $collection = InvoiceItemsCollection::createFromWooItems($lineItems, $includeTax);
        $globalDiscount = new GlobalDiscount($order->get_items('coupon'));

        if (is_array($shippingItems) && count($shippingItems) > 0) {
            $collection->addShippingItems($shippingItems);
        }

        if (is_array($feeItems) && count($feeItems)) {
            (new FeeItemProcessor($feeItems))->process($collection, $globalDiscount);
        }

        $self = new self(
            $invoiceType,
            $serie,
            $number,
            $order->get_id(),
            $customer,
            $collection,
            $collection->createSummary($globalDiscount)
        );

        return $self;
    }

    public function toArray()
    {
        return [
            'serie_documento' => $this->serie,
            'numero_documento' => $this->number,
            'fecha_de_emision' => $this->date->format('Y-m-d'),
            'hora_de_emision' => $this->date->format('H:i:s'),
            'codigo_tipo_operacion' => '0101',
            'codigo_tipo_documento' => $this->documentType,
            'codigo_tipo_moneda' => 'PEN',
            'fecha_de_vencimiento' => $this->dueDate->format('Y-m-d H:i:s'),
            'numero_orden_de_compra' => $this->orderId,
            'nombre_almacen' => 'Almacen Virtual',
            'datos_del_emisor' => [
                'codigo_del_domicilio_fiscal' => '000',
            ],
            'datos_del_cliente_o_receptor' => $this->customer->toArray(),
            'items' => $this->itemsCollection->toArray(),
            'totales' => $this->invoiceSummary->toArray(),
            'descuentos' => $this->invoiceSummary->getDiscount()->toArray(),
            'additional_information' => 'Compra:Online|Web',
        ];
    }
}
