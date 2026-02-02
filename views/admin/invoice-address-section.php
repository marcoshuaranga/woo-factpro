<?php

defined('ABSPATH') || exit;

?>

<div class="address">
  <?php if ($factpro_invoice_was_generated): ?>
    <p>
      <strong>Comprobante electrónico:</strong>
      <a href="<?php echo esc_url($factpro_invoice_pdf_url); ?>" target="_blank">Descargar comprobante</a>
    </p>
  <?php else: ?>
    <p class="none_set">
      <strong>Comprobante electrónico:</strong> No tiene comprobante.
    </p>
  <?php endif; ?>
</div>
<div class="edit_address">
  <?php
  woocommerce_wp_select([
    'id' => 'factpro_customer_document_type',
    'label' => __('Tipo Documento', 'factpro-for-woocommerce'),
    'value' => $order->get_meta('_factpro_customer_document_type'),
    'options' => $identity_documents,
  ]);

  woocommerce_wp_text_input([
    'id' => 'factpro_customer_document_number',
    'label' => __('Número de Documento', 'factpro-for-woocommerce'),
    'value' => $order->get_meta('_factpro_customer_document_number'),
    'wrapper_class' => 'last'
  ]);

  woocommerce_wp_text_input([
    'id' => 'factpro_company_name',
    'label' => __('Razón Social', 'factpro-for-woocommerce'),
    'value' => $order->get_meta('_factpro_company_name'),
    'wrapper_class' => 'form-field-wide',
  ]);

  woocommerce_wp_text_input([
    'id' => 'factpro_company_address',
    'label' => __('Dirección', 'factpro-for-woocommerce'),
    'value' => $order->get_meta('_factpro_company_address'),
    'wrapper_class' => 'form-field-wide',
  ]);

  woocommerce_wp_text_input([
    'id' => 'factpro_company_ubigeo',
    'label' => __('Ubigeo', 'factpro-for-woocommerce'),
    'value' => $order->get_meta('_factpro_company_ubigeo'),
    'wrapper_class' => 'form-field-wide',
  ]);
  ?>
</div>