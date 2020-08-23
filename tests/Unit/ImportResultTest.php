<?php

namespace Tests\Unit;

use App\Import\ImportResult;
use App\Tradeflow;
use PHPUnit\Framework\TestCase;

class ImportResultTest extends TestCase
{
    public function test_it_has_tradeflows()
    {
        $tradeflow = new Tradeflow();

        $importResult = new ImportResult([$tradeflow], [], []);

        $this->assertSame($tradeflow, $importResult->getTradeflows()[0]);
    }

    public function test_it_has_invalid_container_references()
    {
        $importResult = new ImportResult([], ['invalid-ref'], []);

        $this->assertSame('invalid-ref', $importResult->getInvalidContainerReferences()[0]);
    }

    public function test_it_has_tradeflows_without_containers()
    {
        $importResult = new ImportResult([], [], ['A Tradeflow without container']);

        $this->assertSame('A Tradeflow without container', $importResult->getTradeflowsWithoutContainers()[0]);
    }
}
