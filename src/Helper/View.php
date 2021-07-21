<?php

namespace EBilling\Helper;

final class View
{
    private $baseDir;

    public function __construct($baseDir)
    {
        $this->baseDir = $baseDir;        
    }

    public static function make($baseDir)
    {
        return new self($baseDir);
    }

    public function render($view, array $context = [])
    {
        extract($context);
        ob_start();
        include("{$this->baseDir}/$view.php");
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }
}
