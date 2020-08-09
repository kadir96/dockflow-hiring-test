<?php

namespace Tests\Unit;

use App\Import\ShipmentRecord;
use PHPUnit\Framework\TestCase;

class ShipmentRecordTest extends TestCase
{
    /**
     * @var ShipmentRecord
     */
    private $record;

    protected function setUp(): void
    {
        parent::setUp();

        // A totally valid record.
        $this->record = new ShipmentRecord(1234, 'ABC Transport', 'FCIU 658783-1', 123456789);
    }

    public function test_purchase_order_is_used_as_tradeflow_name_if_provided()
    {
        $this->assertEquals(123456789, $this->record->getTradeflowName());
    }

    public function test_order_no_and_transporter_concatenated_as_tradeflow_name_if_purchase_order_is_missing()
    {
        $record = new ShipmentRecord(1234, 'ABC Transport', 'FCIU 658783-1');
        $this->assertEquals("1234 ABC Transport", $record->getTradeflowName());
    }

    public function test_it_has_an_order_no()
    {
        $this->assertEquals(1234, $this->record->getOrderNo());
    }

    public function test_it_has_a_transporter()
    {
        $this->assertEquals('ABC Transport', $this->record->getTransporter());
    }

    public function test_it_has_a_container_reference()
    {
        $this->assertEquals('FCIU 658783-1', $this->record->getContainerReference());
    }

    public function test_it_may_have_a_purchase_order()
    {
        $this->assertEquals(123456789, $this->record->getPurchaseOrder());
    }

    public function test_it_may_not_have_a_purchase_order()
    {
        $record = new ShipmentRecord(1234, 'ABC Transport', 'FCIU 658783-1');

        $this->assertNull($record->getPurchaseOrder());
    }

    public function test_hasValidContainerRef_returns_true_if_container_reference_is_valid()
    {
        $this->assertTrue($this->record->hasValidContainerRef());
    }

    public function test_hasValidContainerRef_returns_false_if_container_reference_is_invalid()
    {
        $invalidContainerReferences = [
            '123',
            'ASD1234',
            'ASDF 13458904-4',
            'ASD 12345678-5',
            'AASDF 123456-4BCF'
        ];

        foreach ($invalidContainerReferences as $reference) {
            $record = new ShipmentRecord(1234, 'ABC Transport', $reference, 123456789);

            $this->assertFalse($record->hasValidContainerRef(), "$reference should be invalid!");
        }
    }

    public function test_isImportable_returns_true_if_record_is_importable()
    {
        $this->assertTrue($this->record->isImportable());
    }

    public function test_isImportable_returns_false_if_record_is_not_importable()
    {
        // If container reference is not valid, the record should not be importable.
        $notImportableRecord = new ShipmentRecord(1234, 'ABC Transport', 'not-valid-ref', 123456789);
        $this->assertFalse($notImportableRecord->isImportable());
    }
}
