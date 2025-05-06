<?php

namespace Factpro\ThirdParties\Factpro;

use WC_Logger;

final class FactproApi
{
  private $baseUrl;

  private $token;

  /** @var WC_Logger */
  private $logger;

  public function __construct($baseUrl, $token, WC_Logger $logger)
  {
    $this->baseUrl = $baseUrl;
    $this->token = $token;
    $this->logger = $logger;
  }

  public function send(FactproRequest $request)
  {
    return $this->httpPost($request);
  }

  private function httpPost(FactproRequest $request)
  {
    $data = $request->toArray();
    $url = $this->baseUrl . $request->getPath();

    $response = wp_remote_post($url, [
      'headers' => [
        'Authorization' => 'Bearer ' . $this->token,
        'Accept' => 'application/json',
        'Content-Type' => 'application/json; charset=utf-8',
      ],
      'body' => json_encode($data, true)
    ]);

    if (is_wp_error($response)) {
      throw new \Exception($response->get_error_message());
    }

    $statusCode = wp_remote_retrieve_response_code($response);
    $jsonResponse = wp_remote_retrieve_body($response);

    $this->logger->info(join(PHP_EOL, [
      'Request to ' . $url,
      'Status Code: ' . $statusCode,
      'Response: ' . $jsonResponse,
    ]), [
      'source' => 'woo-factpro',
    ]);

    return $jsonResponse;
  }
}
