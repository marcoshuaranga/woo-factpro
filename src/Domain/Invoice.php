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

    private InvoiceOptions $options;

    private PaymentMethod $paymentMethod;

    private Customer $customer;

    private InvoiceItems $invoiceItems;

    public function __construct(
        $documentType,
        $serie,
        $number,
        $orderId,
        InvoiceOptions $options,
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
        $this->options = $options;
        $this->paymentMethod = $paymentMethod;
        $this->customer = $customer;
        $this->invoiceItems = $invoiceItems;
    }

    public static function createFromWooOrder($serie, $number, \WC_Order $order, $includeTax)
    {
        $invoiceType = $order->get_meta('_factpro_invoice_type');
        $nameOrCompany = "{$order->get_billing_first_name()} {$order->get_billing_last_name()}";
        $address = sprintf(
            '%s, %s, %s',
            $order->get_billing_address_1(),
            $order->get_billing_city(),
            $order->get_billing_state()
        );
        $ubigeo = '';

        if (InvoiceType::is_factura($invoiceType)) {
            $nameOrCompany = $order->get_meta('_factpro_company_name');
            $address = $order->get_meta('_factpro_company_address');
            $ubigeo = $order->get_meta('_factpro_company_ubigeo');
        }

        $invoiceOptions = new InvoiceOptions(
            get_option('wc_settings_factpro_send_email_automatically', 'yes') === 'yes'
        );

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
            $order->get_customer_note(),
        );

        $self = new self(
            $invoiceType,
            $serie,
            $number,
            $order->get_id(),
            $invoiceOptions,
            $paymentMethod,
            $customer,
            InvoiceItems::createFromWooOrder($order)
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

    public function getOptions()
    {
        return $this->options;
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
