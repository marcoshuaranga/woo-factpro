jQuery(function($) {
    const FACTURA = '01';
    const BOLETA = '03';

    $('#has-invoice-address').on('change', 'input', function() {
        $('div.invoice_address').toggle(450);
    });

    $('#ebilling_invoice_type_field input[type="radio"]').change(function() {
        switch (this.value) {
            case BOLETA:
                $('#ebilling_customer_document_type_wrapper').show();
                $('#factura-fields').hide();
                break;
                
            case FACTURA:
                $('#ebilling_customer_document_type_wrapper').hide();
                $('#factura-fields').show();
                break;
        }
    });

    $('#find_apiperu').click(function () {
        var documentType = $('input[name="ebilling_invoice_type"]:checked').val();
        var documentNumber = $('#ebilling_customer_document_number').val();

        switch (documentType) {
            case BOLETA:
                documentNumber.length === 8 && findBy('dni', documentNumber, function (data) {
                    $('#billing_first_name').val(data.nombres);
                    $('#billing_last_name').val(`${data.apellido_paterno} ${data.apellido_materno}`);
                });
                break;
            case FACTURA:
                documentNumber.length === 11 && findBy('ruc', documentNumber, function (data) {
                    $('#ebilling_company_name').val(data.nombre_o_razon_social);
                    $('#ebilling_company_address').val(data.direccion_completa);
                    $('#ebilling_company_ubigeo').val(data.ubigeo);
                });
                break;
        }
    });

    const findBy = function (documentType, documentNumber, cb) {
        $('#find_apiperu').prop('disabled', true);

        $.get(`${ebillingSettings.root}/${documentType}/${documentNumber}`, cb).always(function () {
            $('#find_apiperu').prop('disabled', false);
        });
    }
});
