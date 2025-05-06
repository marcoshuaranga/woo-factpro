<?php

namespace Factpro\ThirdParties\Factpro\Response;

final class DocumentResponse
{
  private const STATE_TYPE_ID_ACCEPTED = '01';
  private const STATE_TYPE_ID_REGISTERED = '05';
  private const STATE_TYPE_ID_REJECTED = '09';
  private const STATE_TYPE_ID_CANCELED = '11';
  private const STATE_TYPE_ID_PENDING_TO_CANCEL = '13';
  private const STATE_TYPE_ID_NO_RESPONSE = '19';

  private array $data;

  public function __construct(string $jsonResponse)
  {
    $this->data = json_decode($jsonResponse, true);
  }

  public static function fromJson(string $jsonResponse): self
  {
    return new self($jsonResponse === '' ? '{}' : $jsonResponse);
  }

  public function isAccepted()
  {
    return $this->getStateTypeId() === self::STATE_TYPE_ID_ACCEPTED;
  }

  public function isRegistered()
  {
    return $this->getStateTypeId() === self::STATE_TYPE_ID_REGISTERED;
  }

  public function isRejected()
  {
    return $this->getStateTypeId() === self::STATE_TYPE_ID_REJECTED;
  }

  public function isCanceled()
  {
    return $this->getStateTypeId() === self::STATE_TYPE_ID_CANCELED;
  }

  public function isPendingToCancel()
  {
    return $this->getStateTypeId() === self::STATE_TYPE_ID_PENDING_TO_CANCEL;
  }

  public function isNoResponse()
  {
    return $this->getStateTypeId() === self::STATE_TYPE_ID_NO_RESPONSE;
  }

  public function isEmpty()
  {
    return empty($this->data);
  }

  public function getDownloadPdfUrl()
  {
    return $this->get('links.download_pdf');
  }


  public function getPdfUrl()
  {
    return $this->get('links.pdf');
  }

  public function getXmlUrl()
  {
    return $this->get('links.xml');
  }

  public function getSerialNumber()
  {
    return $this->get('data.number');
  }

  public function getStateTypeId()
  {
    return $this->get('data.state_type_id');
  }

  public function getStateDescription()
  {
    return $this->get('data.state_description');
  }

  public function get(string $path, $default = null)
  {
    return _wp_array_get($this->data, explode('.', $path), $default);
  }
}
