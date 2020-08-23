<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function getStubPath(string $name)
    {
        return __DIR__ . "/stubs/{$name}";
    }
}
