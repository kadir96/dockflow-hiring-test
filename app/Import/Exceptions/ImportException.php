<?php

namespace App\Import\Exceptions;

use Exception;
use Throwable;

class ImportException extends Exception
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct("Import failed!", 0, $previous);
    }
}
