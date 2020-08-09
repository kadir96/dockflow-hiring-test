<?php

namespace App\Import\Exceptions;

class FileNotFoundException extends ImportException
{
    public function __construct(string $path)
    {
        parent::__construct("File $path does not exist!");
    }
}
