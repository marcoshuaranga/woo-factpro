<?php

namespace Factpro\ThirdParties\Factpro\Request;

use Factpro\ThirdParties\Factpro\FactproRequest;

final class ConsultDocumentV3Request extends FactproRequest
{
  private $serie;
  private $number;

  public function __construct(array $attributes)
  {
    $this->serie = $attributes['serie'];
    $this->number = $attributes['number'];
  }

  public function getEndpoint()
  {
    return 'https://api.factpro.la/api/v3/consulta';
  }

  public function toArray()
  {
    return [
      'serie' => $this->serie,
      'numero' => $this->number,
    ];
  }
}
