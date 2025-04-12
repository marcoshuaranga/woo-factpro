jQuery(function ($) {
    const DNI = '1';
    const RUC = '6';

    $('.factpro_company_name_field').fadeOut();
    $('.factpro_company_address_field').fadeOut();
    $('.factpro_company_ubigeo_field').fadeOut();

    if ($('#factpro_customer_document_type').val() === RUC) {
        $('.factpro_company_name_field').fadeIn();
        $('.factpro_company_address_field').fadeIn();
        $('.factpro_company_ubigeo_field').fadeIn();
    }

    $('#factpro_customer_document_number').change(function(){
        var documentType = $('#factpro_customer_document_type').val();
        var documentNumber = $('#factpro_customer_document_number').val();

        switch (documentType) {
            case DNI:
                documentNumber.length === 8 && findBy('dni', documentNumber, function (data) {
                    $('#_billing_first_name').val(data.nombres);
                    $('#_billing_last_name').val(`${data.apellido_paterno} ${data.apellido_materno}`);
                });
                break;
            case RUC:
                documentNumber.length === 11 && findBy('ruc', documentNumber, function (data) {
                    $('#factpro_company_name').val(data.nombre_o_razon_social);
                    $('#factpro_company_address').val(data.direccion_completa);
                    $('#factpro_company_ubigeo').val(data.ubigeo);
                });
                break;
            default:
                console.log('No option selected.')
        }
    });

    $('#factpro_customer_document_type').change(function(){
        switch (this.value) {
            case RUC:
                $('.factpro_company_name_field').fadeIn();
                $('.factpro_company_address_field').fadeIn();
                $('.factpro_company_ubigeo_field').fadeIn();
                break;
            default:
                $('.factpro_company_name_field').fadeOut();
                $('.factpro_company_address_field').fadeOut();
                $('.factpro_company_ubigeo_field').fadeOut();
        }
    });

    const findBy = function (documentType, documentNumber, cb) {
        $.get(`${factproSettings.root}/${documentType}/${documentNumber}`, cb);
    }
});
