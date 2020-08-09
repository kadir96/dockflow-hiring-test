<?php

namespace Tests\Unit;

use App\Import\ShipmentRecord;
use App\Import\Tradeflow;
use PHPUnit\Framework\TestCase;
use Mockery;

class TradeflowTest extends TestCase
{
    public function test_it_has_a_name()
    {
        $tradeflow = new Tradeflow('A Tradeflow Name', []);
        $this->assertEquals('A Tradeflow Name', $tradeflow->getName());
    }

    public function test_it_has_shipment_records()
    {
        $validShipmentRecordMock = Mockery::mock(ShipmentRecord::class);
        $validShipmentRecordMock->shouldReceive('isImportable', 'hasValidContainerRef')->andReturn(true);

        $invalidShipmentRecordMock = Mockery::mock(ShipmentRecord::class);
        $invalidShipmentRecordMock->shouldReceive('isImportable', 'hasValidContainerRef')->andReturn(false);

        $tradeflow = new Tradeflow('A Tradeflow Name', [$validShipmentRecordMock, $invalidShipmentRecordMock]);

        $this->assertIsArray($tradeflow->getShipmentRecords());
        $this->assertCount(2, $tradeflow->getShipmentRecords());
        $this->assertSame($validShipmentRecordMock, $tradeflow->getShipmentRecords()[0]);
        $this->assertSame($invalidShipmentRecordMock, $tradeflow->getShipmentRecords()[1]);
    }

    public function test_hasImportableShipmentRecords_returns_true_if_it_has_importable_records()
    {
        $validShipmentRecordMock = Mockery::mock(ShipmentRecord::class);
        $validShipmentRecordMock->shouldReceive('isImportable', 'hasValidContainerRef')->andReturn(true);

        $invalidShipmentRecordMock = Mockery::mock(ShipmentRecord::class);
        $invalidShipmentRecordMock->shouldReceive('isImportable', 'hasValidContainerRef')->andReturn(false);

        $tradeflow = new Tradeflow('A Tradeflow Name', [$validShipmentRecordMock, $invalidShipmentRecordMock]);

        $this->assertTrue($tradeflow->hasImportableShipmentRecords());
    }

    public function test_hasImportableShipmentRecords_returns_false_if_it_does_not_have_importable_records()
    {
        $invalidShipmentRecordMock = Mockery::mock(ShipmentRecord::class);
        $invalidShipmentRecordMock->shouldReceive('isImportable', 'hasValidContainerRef')->andReturn(false);

        $tradeflow = new Tradeflow('A Tradeflow Name', [$invalidShipmentRecordMock]);

        $this->assertFalse($tradeflow->hasImportableShipmentRecords());
    }

    public function test_getImportableShipmentRecords_only_returns_importable_records()
    {
        $validShipmentRecordMock = Mockery::mock(ShipmentRecord::class);
        $validShipmentRecordMock->shouldReceive('isImportable', 'hasValidContainerRef')->andReturn(true);

        $invalidShipmentRecordMock = Mockery::mock(ShipmentRecord::class);
        $invalidShipmentRecordMock->shouldReceive('isImportable', 'hasValidContainerRef')->andReturn(false);

        $tradeflow = new Tradeflow('A Tradeflow Name', [$validShipmentRecordMock, $invalidShipmentRecordMock]);

        $this->assertCount(1, $tradeflow->getImportableShipmentRecords());
        $this->assertSame($validShipmentRecordMock, $tradeflow->getImportableShipmentRecords()[0]);
    }

    public function test_getShipmentRecordsWithInvalidContainerReference_returns_records_with_invalid_container_reference()
    {
        $validShipmentRecordMock = Mockery::mock(ShipmentRecord::class);
        $validShipmentRecordMock->shouldReceive('isImportable', 'hasValidContainerRef')->andReturn(true);

        $invalidShipmentRecordMock1 = Mockery::mock(ShipmentRecord::class);
        $invalidShipmentRecordMock1->shouldReceive('isImportable', 'hasValidContainerRef')->andReturn(false);

        $invalidShipmentRecordMock2 = Mockery::mock(ShipmentRecord::class);
        $invalidShipmentRecordMock2->shouldReceive('isImportable', 'hasValidContainerRef')->andReturn(false);

        $tradeflow = new Tradeflow('A Tradeflow Name', [$validShipmentRecordMock, $invalidShipmentRecordMock1, $invalidShipmentRecordMock2]);

        $this->assertCount(2, $tradeflow->getShipmentRecordsWithInvalidContainerReference());
        $this->assertSame($invalidShipmentRecordMock1, $tradeflow->getShipmentRecordsWithInvalidContainerReference()[0]);
        $this->assertSame($invalidShipmentRecordMock2, $tradeflow->getShipmentRecordsWithInvalidContainerReference()[1]);
    }
}
