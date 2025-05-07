<?php

declare(strict_types=1);

use Factpro\ThirdParties\Factpro\Response\DocumentResponse;

defined('ABSPATH') || exit;

/**
 * @var DocumentResponse $documentResponse
 */

$state_classes = [
  '01' => 'success', // Aceptado
  '05' => 'success', // Registrado
  '09' => 'danger', // Rechazado
  '11' => 'danger', // Anulado
  '13' => 'warning', // Por anular
  '19' => '', // sin respuesta
];

$status_class = isset($state_classes[$documentResponse->getStateTypeId()]) ?
  $state_classes[$documentResponse->getStateTypeId()] :
  'warning';
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

    .pill-tag.warning {
      background-color: #ffb900;
    }

    .pill-tag.success {
      background-color: #46b450;
    }

    .pill-tag.danger {
      background-color: #dc3232;
    }
  }
</style>
<div class="order-invoice-metabox woo-factpro" style="padding-top: 1rem;">
  <div class="row">
    <div class="column">
      <h4 class="mt-0 mb-0">
        <?php esc_html_e($documentResponse->getSerialNumber(), 'woo-factpro'); ?>
      </h4>
    </div>
  </div>
  <div class="row">
    <div class="column">
      <h4 class="mt-0 mb-0"><?= esc_html('Estado', 'woo-factpro') ?> </h4>
    </div>
    <div class="column">
      <span class="pill-tag <?= $status_class ?>">
        <?= esc_html($documentResponse->getStateDescription(), 'woo-factpro') ?>
      </span>
    </div>
  </div>
  <div class="row">
    <div class="column">
      <h4 class="mt-0 mb-2"><?= esc_html('Descarga', 'woo-factpro') ?></h4>
      <div class="woocommerce-order-invoice-download-container">
        <div class="button-group">
          <a class="button" href="<?= $documentResponse->getPdfUrl() ?>" target="_blank" aria-label="PDF" title="PDF">
            <span class="dashicons dashicons-download" style="vertical-align: middle;"></span> PDF </a>
          <a class="button" href="<?= $documentResponse->getXmlUrl() ?>" target="_blank" aria-label="XML" title="XML">
            <span class="dashicons dashicons-download" style="vertical-align: middle;"></span> XML </a>
        </div>
      </div>
    </div>
  </div>
  <?php if (! $documentResponse->isCanceled()): ?>
    <div class="row">
      <div class="column">
        <h4 class="mt-0 mb-2"><?= esc_html('Acción', 'woo-factpro') ?></h4>
        <div class="woocommerce-order-invoice-action-container">
          <div class="mb-2">
            <button class="button" id="factpro-invoice-create-btn" <?= $documentResponse->isAcceptedOrRegistered() ? 'disabled' : '' ?>>
              Generar comprobante
            </button>
          </div>
          <div class="mb-2">
            <button class="button" id="factpro-invoice-status-btn">
              Consultar estado
            </button>
          </div>
          <div>
            <button class="button button-delete" id="factpro-invoice-cancel-btn">
              Anular
            </button>
          </div>
        </div>
      </div>
    </div>
  <?php endif ?>
</div>