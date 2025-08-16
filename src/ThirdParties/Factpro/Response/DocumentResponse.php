<?php

namespace Factpro\ThirdParties\Factpro\Response;

final class DocumentResponse
{
  public static function fromJson(string $version, string $jsonResponse)
  {
    switch ($version) {
      case 'v2':
        return new DocumentV2Response($jsonResponse);
      case 'v3':
        return new DocumentV3Response($jsonResponse);
      default:
        throw new \InvalidArgumentException("Unsupported version: $version");
    }
  }

  public static function fromJsonV2(string $jsonResponse)
  {
    return new DocumentV2Response($jsonResponse);
  }

  public static function fromJsonV3(string $jsonResponse)
  {
    return new DocumentV3Response($jsonResponse);
  }
}
