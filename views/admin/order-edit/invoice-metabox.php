<?php

declare(strict_types=1);

defined('ABSPATH') || exit;
?>
<style>
  .woo-factpro {
    .button-delete {
      background: #dc3232;
      border-color: #dc3232;
      color: #fff;
      vertical-align: top;
    }

    .button-delete:hover {
      background: #a00;
      border-color: #a00;
      color: #fff;
      vertical-align: top;
    }

    .mb-3 {
      margin-bottom: 1rem;
    }

    .mb-0 {
      margin-bottom: 0;
    }

    .mb-2 {
      margin-bottom: 0.5rem;
    }

    .mt-0 {
      margin-top: 0;
    }

    .row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 14px;
    }

    .column {
      flex: 1;
      padding: 0 7px;
    }

    .column h4 {
      font-size: 14px;
    }

    .pill-tag {
      display: inline-block;
      padding: 5px 10px;
      font-size: 12px;
      font-weight: bold;
      color: #fff;
      background-color: #0073aa;
      border-radius: 50px;
      text-align: center;
      text-transform: uppercase;
      white-space: nowrap;
    }

    .pill-tag.pending {
      background-color: #ffb900;
    }

    .pill-tag.completed {
      background-color: #46b450;
    }

    .pill-tag.cancelled {
      background-color: #dc3232;
    }
  }
</style>
<div class="order-invoice-metabox woo-factpro" style="padding-top: 1rem;">
  <div class="row">
    <div class="column">
      <h4 class="mt-0 mb-0"><?php esc_html_e('F001-01011', 'woo-factpro'); ?></h4>
    </div>
  </div>
  <div class="row">
    <div class="column">
      <h4 class="mt-0 mb-0"><?= esc_html('Estado', 'woo-factpro') ?> </h4>
    </div>
    <div class="column">
      <span class="pill-tag completed">
        Aceptado
      </span>
    </div>
  </div>
  <div class="row">
    <div class="column">
      <h4 class="mt-0 mb-2"><?= esc_html('Descarga', 'woo-factpro') ?></h4>
      <div class="woocommerce-order-invoice-download-container">
        <div class="button-group">
          <a class="button pdf" href="https://cpe.factpro.la/documents/6626e11153067206c2d3bc26/print/pdf/67ff598ab1182a005dfa7cbf?type=a4" aria-label="PDF" title="PDF">
            <span class="dashicons dashicons-download" style="vertical-align: middle;"></span> PDF </a>
          <a class="button xml" href="https://factpro.pe/downloads/document/20603060046/xml/ea05cd7f-dac0-49e8-be34-f45486026d15" aria-label="XML" title="XML">
            <span class="dashicons dashicons-download" style="vertical-align: middle;"></span> XML </a>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="column">
      <h4 class="mt-0 mb-2"><?= esc_html('Acción', 'woo-factpro') ?></h4>
      <div class="woocommerce-order-invoice-action-container">
        <div class="mb-2">
          <a class="button pdf" href="https://cpe.factpro.la/documents/6626e11153067206c2d3bc26/print/pdf/67ff598ab1182a005dfa7cbf?type=a4" aria-label="PDF" title="PDF">
            Generar comprobante
          </a>
        </div>
        <div class="mb-2">
          <a class="button pdf" href="https://cpe.factpro.la/documents/6626e11153067206c2d3bc26/print/pdf/67ff598ab1182a005dfa7cbf?type=a4" aria-label="PDF" title="PDF">
            Consultar estado
          </a>
        </div>
        <div class="mb-2">
          <button class="button wc-reload">
            <span><?php esc_html_e('Apply', 'woocommerce'); ?></span>
          </button>
        </div>
        <div>
          <a class="button button-delete" href="https://cpe.factpro.la/documents/6626e11153067206c2d3bc26/print/pdf/67ff598ab1182a005dfa7cbf?type=a4" aria-label="PDF" title="PDF">
            Anular
          </a>
        </div>
      </div>
    </div>
  </div>




  <!-- <div class="woocommerce-order-invoice-serie-container">
    <div class="pill-tag completed">
      Aceptado
    </div>
    <div class="pill-tag pending">
      Pendiente
    </div>
    <div class="pill-tag cancelled">
      Rechazado
    </div>
  </div> -->
</div>