<?php

declare(strict_types=1);

use Factpro\ThirdParties\Factpro\Response\DocumentResponse;

defined('ABSPATH') || exit;

/**
 * @var DocumentResponse $documentResponse
 */
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
      color: #666666;
      background-color: #cccccc;
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
      <h4 class="mt-0 mb-0">
        No tiene comprobante
      </h4>
    </div>
  </div>
  <div class="row">
    <div class="column">
      <h4 class="mt-0 mb-0"><?php echo esc_html__('Estado', 'woo-factpro'); ?> </h4>
    </div>
    <div class="column">
      <span class="pill-tag">
        Ninguno
      </span>
    </div>
  </div>
  <div class="row">
    <div class="column">
      <h4 class="mt-0 mb-2"><?php echo esc_html__('Acción', 'woo-factpro'); ?></h4>
      <div class="woocommerce-order-invoice-action-container">
        <div class="mb-2">
          <button class="button" id="factpro-invoice-create-btn">
            Generar comprobante
          </button>
        </div>
        <div class="mb-2">
          <button class="button" id="factpro-invoice-status-btn" disabled>
            Consultar estado
          </button>
        </div>
        <div>
          <button class="button button-delete" id="factpro-invoice-cancel-btn" disabled>
            Anular
          </button>
        </div>
      </div>
    </div>
  </div>
</div>