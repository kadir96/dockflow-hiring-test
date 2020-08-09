<?php

namespace App\Import\Exceptions;

use Throwable;

class ImportFailedException extends ImportException
{
    public function __construct(Throwable $previous)
    {
        parent::__construct("Import failed!", 0, $previous);
    }
}
