<?php

namespace Factpro\ThirdParties\Factpro;

abstract class FactproRequest
{
  abstract function getPath();

  /**
   * @return array
   */
  abstract function toArray();
}
