<?php

namespace Factpro;

use Factpro\Domain\Invoice;
use Factpro\InvoiceFormatter\FactProFormatter;

final class InvoiceFormatter
{
  private $formatter;

  public function __construct(Invoice $invoice)
  {
    $this->formatter = new FactProFormatter($invoice);
  }

  public function is($className)
  {
    return get_class($this->formatter) === $className;
  }

  public function toArray()
  {
    return $this->formatter->toArray();
  }
}
