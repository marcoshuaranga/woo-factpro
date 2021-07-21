jQuery(function ($) {
    const DNI = '1';
    const RUC = '6';

    $('.ebilling_company_name_field').fadeOut();
    $('.ebilling_company_address_field').fadeOut();

    if ($('#ebilling_customer_document_type').val() === RUC) {
        $('.ebilling_company_name_field').fadeIn();
        $('.ebilling_company_address_field').fadeIn();
    }

    $('#ebilling_customer_document_number').change(function(){
        var documentType = $('#ebilling_customer_document_type').val();
        var documentNumber = $('#ebilling_customer_document_number').val();

        switch (documentType) {
            case DNI:
                documentNumber.length === 8 && findBy('dni', documentNumber, function (data) {
                    $('#_billing_first_name').val(data.nombres);
                    $('#_billing_last_name').val(`${data.apellido_paterno} ${data.apellido_materno}`);
                });
                break;
            case RUC:
                documentNumber.length === 11 && findBy('ruc', documentNumber, function (data) {
                    $('#ebilling_company_name').val(data.nombre_o_razon_social);
                    $('#ebilling_company_address').val(data.direccion_completa);
                });
                break;
            default:
                console.log('no selected option.')
        }
    });

    $('#ebilling_customer_document_type').change(function(){
        switch (this.value) {
            case RUC:
                $('.ebilling_company_name_field').fadeIn();
                $('.ebilling_company_address_field').fadeIn();
                break;
            default:
                $('.ebilling_company_name_field').fadeOut();
                $('.ebilling_company_address_field').fadeOut();
        }
    });

    const findBy = function (documentType, documentNumber, cb) {
        $.get(`${ebillingSettings.root}/${documentType}/${documentNumber}`, cb);
    }
});
