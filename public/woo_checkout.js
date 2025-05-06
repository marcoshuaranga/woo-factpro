jQuery(function ($) {
    const DNI = '1';
    const FACTURA = '01';
    const BOLETA = '03';

    let factproContainerFields = $('.woocommerce-invoice-address-fields__field-wrapper');
    let factproInvoiceTypeField = $('#factpro_invoice_type_field');
    let factproHasInvoiceAddress = $('#has-invoice-address');
    let factproCustomerDocumentType = $('#factpro_customer_document_type');
    let factproCustomerDocumentNumber = $('#factpro_customer_document_number');
    let factproCompanyRuc = $('#factpro_company_ruc');
    let factproCompanyName = $('#factpro_company_name');
    let factproCompanyAddress = $('#factpro_company_address');
    let factproCompanyUbigeo = $('#factpro_company_ubigeo');

    factproHasInvoiceAddress.on('change', 'input', function () {
        $('div.invoice_address').toggle(450);
    });

    factproInvoiceTypeField.on('change', 'input[type="radio"]', function () {
        switch ($(this).val()) {
            case BOLETA:
                $('#factpro_customer_document_type_wrapper').show();
                $('#factura-fields').hide();
                break;

            case FACTURA:
                $('#factpro_customer_document_type_wrapper').hide();
                $('#factura-fields').show();
                break;
        }
    });

    factproCustomerDocumentNumber.on('input', function () {
        $(this).val($(this).val().replace(/[^0-9]/g, ''));

        if (factproCustomerDocumentType.val() !== DNI) {
            return;
        }

        $(this).val().length === 8 && findBy('dni', $(this).val(), function (data) {
            $('#billing_first_name').val(data.nombres);
            $('#billing_last_name').val(`${data.apellido_paterno} ${data.apellido_materno}`);
        });
    });

    factproCompanyRuc.on('input', function () {
        $(this).val($(this).val().replace(/[^0-9]/g, ''));

        $(this).val().length === 11 && findBy('ruc', $(this).val(), function (data) {
            factproCompanyName.val(data.nombre_o_razon_social);
            factproCompanyAddress.val(data.direccion_completa);
            factproCompanyUbigeo.val(data.ubigeo);
        });
    });

    const findBy = function (documentType, documentNumber, cb) {
        factproContainerFields.block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });

        $.get(`${factproSettings.root}/${documentType}/${documentNumber}`, cb).always(function () {
            factproContainerFields.unblock();
        });
    }
});
