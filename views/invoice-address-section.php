<?php

/**
 * Checkout billing information form
 * @global WC_Checkout $checkout
 */

use Factpro\SunatCode\InvoiceType;

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

   <div class="invoice_address" style="display: <?php echo esc_attr($invoice_is_mandatory ? 'block' : 'none'); ?>;">

      <div class="woocommerce-invoice-address-fields__field-wrapper">
         <?php
         woocommerce_form_field('factpro_invoice_type', [
            'default' =>  $invoice_is_mandatory ? InvoiceType::BOLETA : '',
            'label' => '',
            'type' => 'radio',
            'required' => $invoice_is_mandatory,
            'options' => $invoices_types,
         ], $checkout->get_value('factpro_invoice_type'));
         ?>

         <div id="factpro_customer_document_type_wrapper" style="display: <?php echo esc_attr($invoice_is_mandatory ? 'block' : 'none'); ?>;">
            <?php
            woocommerce_form_field('factpro_customer_document_type', [
               'class' => [],
               'label' => __('Tipo de Documento', 'factpro-for-woocommerce'),
               'type' => 'select',
               'required' => true,
               'options' => $identity_documents,
            ], $checkout->get_value('factpro_customer_document_type'));

            woocommerce_form_field('factpro_customer_document_number', [
               'class' => [],
               'label' => __('Número de Documento', 'factpro-for-woocommerce'),
               'type' => 'text',
               'required' => true,
               'minlength' => 8,
               'maxlength' => 16,
            ], $checkout->get_value('factpro_customer_document_number'));
            ?>
         </div>

         <div id="factura-fields" style="display: none;">
            <?php
            woocommerce_form_field('factpro_company_ruc', [
               'label' => __('RUC de la empresa', 'factpro-for-woocommerce'),
               'placeholder' => __('RUC de la empresa', 'factpro-for-woocommerce'),
               'type' => 'text',
               'required' => true,
               'minlength' => 11,
               'maxlength' => 11,
            ], $checkout->get_value('factpro_company_ruc'));

            woocommerce_form_field('factpro_company_name', [
               'label' => __('Nombre de razón social', 'factpro-for-woocommerce'),
               'type' => 'text',
               'required' => true,
            ], $checkout->get_value('factpro_company_name'));

            woocommerce_form_field('factpro_company_address', [
               'label' => __('Domicilio Fiscal', 'factpro-for-woocommerce'),
               'type' => 'text',
               'required' => true,
            ], $checkout->get_value('factpro_company_address'));

            woocommerce_form_field('factpro_company_ubigeo', [
               'type' => 'hidden',
               'default' => '',
               'required' => false,
            ], $checkout->get_value('factpro_company_ubigeo'));
            ?>
         </div>
      </div>
   </div>
</div>