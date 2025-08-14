<?php

namespace Factpro\ThirdParties\Factpro\Request;

use Factpro\ThirdParties\Factpro\FactproRequest;

final class CancelDocumentRequest extends FactproRequest
{
  private $documentType;
  private $serie;
  private $number;
  private $reason;

  public function __construct(array $attributes)
  {
    $this->documentType = $attributes['documentType'];
    $this->serie = $attributes['serie'];
    $this->number = $attributes['number'];
    $this->reason = $attributes['reason'];
  }

  public function getEndpoint()
  {
    return null;
  }

  public function getPath()
  {
    return '/api/v2/anular';
  }

  public function toArray()
  {
    return [
      'tipo_documento' => $this->documentType,
      'serie' => $this->serie,
      'numero' => $this->number,
      'motivo' => $this->reason,
    ];
  }
}
