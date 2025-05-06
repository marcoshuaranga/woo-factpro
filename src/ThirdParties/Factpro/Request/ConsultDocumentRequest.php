<?php

namespace Factpro\ThirdParties\Factpro\Request;

use Factpro\ThirdParties\Factpro\FactproRequest;

final class ConsultDocumentRequest extends FactproRequest
{
  private $documentType;
  private $serie;
  private $number;

  public function __construct(array $attributes)
  {
    $this->documentType = $attributes['documentType'];
    $this->serie = $attributes['serie'];
    $this->number = $attributes['number'];
  }

  public function getPath()
  {
    return '/api/v2/consulta';
  }

  public function toArray()
  {
    return [
      'tipo_documento' => $this->documentType,
      'serie' => $this->serie,
      'numero' => $this->number,
    ];
  }
}
