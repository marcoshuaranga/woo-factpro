<?php

namespace Factpro\ThirdParties\Factpro\Request;

use Factpro\ThirdParties\Factpro\FactproRequest;

final class CancelDocumentRequest extends FactproRequest
{
  private $version;
  private $documentType;
  private $serie;
  private $number;
  private $reason;

  public function __construct(string $version, array $attributes)
  {
    $this->version = $version;
    $this->documentType = $attributes['documentType'];
    $this->serie = $attributes['serie'];
    $this->number = $attributes['number'];
    $this->reason = $attributes['reason'];
  }

  public function getEndpoint()
  {
    return $this->version === 'v2' ? 'https://dev.factpro.la/api/v2/anular' : 'https://api.factpro.la/api/v3/anular';
  }

  public function toArray()
  {
    return $this->version === 'v2' ? $this->toV2Array() : $this->toV3Array();
  }

  private function toV2Array()
  {
    return [
      'tipo_documento' => $this->documentType,
      'serie' => $this->serie,
      'numero' => $this->number,
      'motivo' => $this->reason,
    ];
  }

  private function toV3Array()
  {
    return [
      'serie' => $this->serie,
      'numero' => $this->number,
      'motivo' => $this->reason,
    ];
  }
}
