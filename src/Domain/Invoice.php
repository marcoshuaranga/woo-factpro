<?php

namespace Factpro\Domain;

use Factpro\SunatCode\InvoiceType;

final class Invoice
{
    private $documentType;

    private $serie;

    private $number;

    private $date;

    private $dueDate;

    /** @var int */
    private $orderId;

    /** @var OrderSummary */
    private $orderSummary;

    private $paymentMethod;

    private $customer;

    private $invoiceItems;

    public function __construct(
        $documentType,
        $serie,
        $number,
        $orderId,
        OrderSummary $orderSummary,
        PaymentMethod $paymentMethod,
        Customer $customer,
        InvoiceItems $invoiceItems
    ) {
        $this->documentType = $documentType;
        $this->serie = $serie;
        $this->number = $number;
        $this->date = new \DateTimeImmutable('now');
        $this->dueDate = $this->date->add(new \DateInterval('P10D')); //add 10 days
        $this->orderId = $orderId;
        $this->orderSummary = $orderSummary;
        $this->paymentMethod = $paymentMethod;
        $this->customer = $customer;
        $this->invoiceItems = $invoiceItems;
    }

    public static function createFromWooOrder($serie, $number, \WC_Order $order, $includeTax)
    {
        $invoiceType = $order->get_meta('_factpro_invoice_type');
        $nameOrCompany = "{$order->get_billing_first_name()} {$order->get_billing_last_name()}";
        $address = $order->get_billing_address_1();
        $ubigeo = null;

        if (InvoiceType::is_factura($invoiceType)) {
            $nameOrCompany = $order->get_meta('_factpro_company_name');
            $address = $order->get_meta('_factpro_company_address');
            $ubigeo = $order->get_meta('_factpro_company_ubigeo');
        }

        $orderSummary = OrderSummary::createFromWooOrder($order);

        $paymentMethod = new PaymentMethod(
            $order->get_payment_method(),
            $order->get_payment_method_title()
        );

        $customer = new Customer(
            $order->get_meta('_factpro_customer_document_type'),
            $order->get_meta('_factpro_customer_document_number'),
            $nameOrCompany,
            $address,
            $order->get_billing_email(),
            $ubigeo,
            $order->get_billing_phone(),
        );

        $self = new self(
            $invoiceType,
            $serie,
            $number,
            $order->get_id(),
            $orderSummary,
            $paymentMethod,
            $customer,
            new InvoiceItems(
                $order->get_items(['line_item', 'shipping', 'fee']),
                $order->get_coupons(),
                $order->get_prices_include_tax()
            )
        );

        return $self;
    }

    public function getDocumentType()
    {
        return $this->documentType;
    }

    public function getSerie()
    {
        return $this->serie;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getDueDate()
    {
        return $this->dueDate;
    }

    public function getOrderId()
    {
        return $this->orderId;
    }

    public function getOrderSummary()
    {
        return $this->orderSummary;
    }

    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function getInvoiceItems()
    {
        return $this->invoiceItems;
    }
}
