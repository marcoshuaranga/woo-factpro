<?php

namespace EBilling\InvoiceSender\Api;

use EBilling\Domain\Invoice;
use EBilling\InvoiceSender\InvoiceSender;

final class PseApi implements InvoiceSender
{
    private $url;

    private $token;

    private $requestDetails;

    public function __construct($url, $token)
    {
        $this->url = $url;
        $this->token = $token;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getRequestDetails()
    {
        return $this->requestDetails;
    }

    public function send(Invoice $invoice)
    {
        $json = json_encode($this->transformToBody($invoice));
        $handler = curl_init($this->url);

        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handler, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($handler, CURLOPT_POSTFIELDS, $json);
        curl_setopt($handler, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($json),
            'Authorization: Bearer ' . $this->token,
            'x-access-token: ' . $this->token,
        ]);

        $result = curl_exec($handler);

        curl_close($handler);
        
        $this->requestDetails = [
            'url' => $this->url,
            'token' => $this->token,
            'body' => $this->transformToBody($invoice)
        ];

        return $result;
    }

    private function transformToBody(Invoice $invoice)
    {
        return array_merge($invoice->getInvoiceSummary()->toArray(), [
            'serie_documento' => $invoice->getSerie(),
            'numero_documento' => $invoice->getNumber(),
            'fecha_de_emision' => $invoice->getDate()->format('Y-m-d'),
            'hora_de_emision' => $invoice->getDate()->format('H:i:s'),
            'codigo_tipo_operacion' => '0101',
            'codigo_tipo_documento' => $invoice->getDocumentType(),
            'codigo_tipo_moneda' => 'PEN',
            'fecha_de_vencimiento' => $invoice->getDueDate()->format('Y-m-d H:i:s'),
            'numero_orden_de_compra' => $invoice->getOrderId(),
            'nombre_almacen' => 'Almacén - Oficina Principal',
            'datos_del_emisor' => [
                'codigo_del_domicilio_fiscal' => '0000',
            ],
            'datos_del_cliente_o_receptor' => [
                'codigo_pais' => $invoice->getCustomer()->getCountryCode(),
                'codigo_tipo_documento_identidad' => $invoice->getCustomer()->getDocumentType(),
                'numero_documento' => $invoice->getCustomer()->getDocumentNumber(),
                'apellidos_y_nombres_o_razon_social' => $invoice->getCustomer()->getNameOrCompany(),
                'ubigeo' => $invoice->getCustomer()->getPostalCode() ?? '150101',
                'direccion' => $invoice->getCustomer()->getAddress(),
                'correo_electronico' => $invoice->getCustomer()->getEmail(),
                'telefono' => $invoice->getCustomer()->getPhoneNumber(),
            ],
            'metodo_de_pago' => 'Efectivo',
            'termino_de_pago' => [
                'descripcion' => 'Contado',
                'tipo' => '0'
            ],
            'items' => $invoice->getItemsCollection()->toArray(),
        ]);
    }
}