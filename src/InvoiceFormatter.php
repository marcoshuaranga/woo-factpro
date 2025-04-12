<?php

namespace Factpro;

use Factpro\Domain\Invoice;
use Factpro\InvoiceFormatter\FactProFormatter;
use Factpro\InvoiceFormatter\OldPseFormatter;

final class InvoiceFormatter
{
  private $formatter;

  public function __construct(Invoice $invoice, string $apiUrl)
  {
    if ($this->isFactPro($apiUrl)) {
      $this->formatter = new FactProFormatter($invoice);
    } else {
      $this->formatter = new OldPseFormatter($invoice);
    }
  }

  public function is($className)
  {
    return get_class($this->formatter) === $className;
  }

  public function toArray()
  {
    return $this->formatter->toArray();
  }

  private function isFactPro(string $apiUrl)
  {
    return \str_contains($apiUrl, 'factpro') || \str_contains($apiUrl, 'factpse');
  }
}
