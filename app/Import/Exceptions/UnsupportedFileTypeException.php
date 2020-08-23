<?php

namespace App\Import\Exceptions;

class UnsupportedFileTypeException extends ImportException
{
    public function __construct(string $mimeType)
    {
        parent::__construct("Import from file of type $mimeType is not supported!");
    }
}
