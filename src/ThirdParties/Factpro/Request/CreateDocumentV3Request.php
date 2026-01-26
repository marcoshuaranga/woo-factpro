<?php

namespace Factpro\ThirdParties\Factpro\Request;

use Factpro\Domain\Invoice;
use Factpro\Domain\InvoiceItem;
use Factpro\SunatCode\IdentityDocument;
use Factpro\ThirdParties\Factpro\FactproRequest;
use Factpro\ThirdParties\Factpro\Constants\TaxType;

final class CreateDocumentV3Request extends FactproRequest
{
    private Invoice $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function getEndpoint()
    {
        return 'https://api.factpro.la/api/v3/documentos';
    }

    public function toArray()
    {
        $invoiceItems = $this->invoice->getInvoiceItems();
        $hasDiscount = $invoiceItems->hasDiscount();
        $clientDocumentTypesv3 = $this->clientDocumentTypesv3();

        return array_merge([
            'serie' => $this->invoice->getSerie(),
            'numero' => $this->invoice->getNumber(),
            'tipo_operacion' => '1',
            'enviar_automaticamente_al_cliente' => $this->invoice->getOptions()->getSendEmailAutomatically(),
            'condicion_de_pago' => [
                ['tipo_de_condicion' => '0', 'forma_de_pago' => '0', 'monto' => 0],
            ],
            'cliente' => [
                'cliente_tipo_documento' => $clientDocumentTypesv3[$this->invoice->getCustomer()->getDocumentType()],
                'cliente_numero_documento' => $this->invoice->getCustomer()->getDocumentNumber(),
                'cliente_denominacion' => $this->invoice->getCustomer()->getNameOrCompany(),
                'codigo_pais' => 'PE',
                'cliente_direccion' => $this->invoice->getCustomer()->getAddress(),
                'cliente_email' => $this->invoice->getCustomer()->getEmail(),
                'cliente_telefono' => $this->invoice->getCustomer()->getPhoneNumber(),
            ],
            'items' => $this->formatItems($invoiceItems->getItems()),
            'numero_orden' => "{$this->invoice->getOrderId()}",
            'observaciones' => $this->invoice->getCustomer()->getCustomerNote(),
            'formato_pdf' => 'a4',
            'canal'=> 'woocommerce',
        ], $hasDiscount ? [
            'totales' => [
                'monto_descuento_global' => $invoiceItems->getTotalDiscount()
            ]
        ] : []);
    }

    private function clientDocumentTypesv3()
    {
        return [
            IdentityDocument::NO_IDENTITY_DOCUMENT => '1',
            IdentityDocument::DNI => '2',
            IdentityDocument::CARNET_EXTRANJERIA => '3',
            IdentityDocument::RUC => '4',
            IdentityDocument::PASAPORTE => '5',
        ];
    }

    private function formatItems(array $items)
    {
        return array_map(function (InvoiceItem $item) {
            return [
                'unidad' => $item->getUnitOfMeasure(),
                'codigo' => 'Order #' . $item->getId(),
                'descripcion' => $item->getDescription(),
                'cantidad' => $item->getQuantity(),
                'precio' => $item->getUnitPrice(),
                'tipo_tax' => $item->isGravado() ? TaxType::IGV : TaxType::EXONERATED,
                'descuento' => 0.00
            ];
        }, $items);
    }
}
