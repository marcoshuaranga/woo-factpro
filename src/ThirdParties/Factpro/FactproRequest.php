<?php

namespace Factpro\ThirdParties\Factpro;

abstract class FactproRequest
{
  /**
   * @return string|null
   */
  abstract function getEndpoint();

  abstract function getPath();

  /**
   * @return array
   */
  abstract function toArray();
}
