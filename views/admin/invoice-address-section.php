<div class="address">
  <?php if ($ebilling_invoice_was_generated): ?>
		<p>
      <strong>Comprobante electrónico:</strong>
      <a href="<?=$ebilling_invoice_pdf_url?>" target="_blank">Descargar comprobante</a>
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
			'id' => 'ebilling_customer_document_type',
      'label' =>__('Tipo Documento', 'woo-ebilling'),
      'value' => $order->get_meta('_ebilling_customer_document_type'),
			'options' => $identity_documents,
    ]);

    woocommerce_wp_text_input([
      'id' => 'ebilling_customer_document_number',
      'label' => __('Número de Documento', 'woo-ebilling'),
      'value' => $order->get_meta('_ebilling_customer_document_number'),
      'wrapper_class' => 'last'
    ]);

    woocommerce_wp_text_input([
      'id' => 'ebilling_company_name',
      'label' => __('Razón Social', 'woo-ebilling'),
      'value' => $order->get_meta('_ebilling_company_name'),
      'wrapper_class' => 'form-field-wide',
    ]);

    woocommerce_wp_text_input([
      'id' => 'ebilling_company_address',
      'label' => __('Domicilio fiscal', 'woo-ebilling'),
      'value' => $order->get_meta('_ebilling_company_address'),
      'wrapper_class' => 'form-field-wide',
    ]);

    woocommerce_wp_text_input([
      'id' => 'ebilling_company_ubigeo',
      'label' => __('Ubigeo', 'woo-ebilling'),
      'value' => $order->get_meta('_ebilling_company_ubigeo'),
      'wrapper_class' => 'form-field-wide',
    ]);
  ?>
</div>
