<?php

namespace Factpro\ThirdParties\Factpro;

abstract class FactproRequest
{
  /**
   * @return string
   */
  abstract function getEndpoint();

  /**
   * @return array
   */
  abstract function toArray();
}
