<?php

/**
 * Checkout billing information form
 * @global WC_Checkout $checkout
 */

defined('ABSPATH') || exit;
?>
<style>
   #factpro_invoice_type_field label.radio {
      display: inline !important;
      margin-left: 5px;
   }

   #factpro_invoice_type_field input[type="radio"] {
      margin-left: 15px;
   }
</style>
<div class="woocommerce-invoice-address-fields">
   <?php if (! $invoice_is_mandatory): ?>
      <h3 id="has-invoice-address">
         <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
            <input id="has-invoice-address-checkbox" type="checkbox" name="has_invoice_address"
               class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" />
            <span>¿Necesitas un comprobante electrónico?</span>
         </label>
      </h3>
   <?php else: ?>
      <h3>Detalles de Comprobante</h3>
   <?php endif; ?>

   <div class="invoice_address" style="display: <?= $invoice_is_mandatory ? 'block' : 'none' ?>;">

      <div class="woocommerce-invoice-address-fields__field-wrapper">
         <?php
         woocommerce_form_field('factpro_invoice_type', [
            'label' => '',
            'type' => 'radio',
            'options' => $invoices_types,
         ], $checkout->get_value('factpro_invoice_type'));
         ?>

         <div id="factpro_customer_document_type_wrapper" style="display: none;">
            <?php
            woocommerce_form_field('factpro_customer_document_type', [
               'label' => __('Tipo de Documento', 'woo-factpro'),
               'type' => 'select',
               'required' => true,
               'options' => $identity_documents,
            ], $checkout->get_value('factpro_customer_document_type'));
            ?>
         </div>
         <?php
         woocommerce_form_field('factpro_customer_document_number', [
            'label' => __('Número de Documento', 'woo-factpro'),
            'type' => 'text',
            'required' => true,
         ], $checkout->get_value('factpro_customer_document_number'));
         ?>
         <p class="form-row">
            <button type="button" id="find_apiperu">Buscar</button>
         </p>
         <div id="factura-fields" style="display: none;">
            <?php
            woocommerce_form_field('factpro_company_name', [
               'label' => __('Nombre razón social', 'woo-factpro'),
               'type' => 'text',
               'required' => true,
            ], $checkout->get_value('factpro_company_name'));

            woocommerce_form_field('factpro_company_address', [
               'label' => __('Domicilio Fiscal', 'woo-factpro'),
               'type' => 'text',
               'required' => true,
            ], $checkout->get_value('factpro_company_address'));

            woocommerce_form_field('factpro_company_ubigeo', [
               'type' => 'hidden',
               'required' => true,
            ], $checkout->get_value('factpro_company_ubigeo'));
            ?>
         </div>
      </div>
   </div>
</div>