<?php

namespace Factpro\Domain;

final class InvoiceOptions
{
  private $sendEmailAutomatically;

  public function __construct(bool $sendEmailAutomatically)
  {
    $this->sendEmailAutomatically = $sendEmailAutomatically;
  }

  public function getSendEmailAutomatically()
  {
    return $this->sendEmailAutomatically;
  }
}
