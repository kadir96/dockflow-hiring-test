<?php

namespace Tests\Feature;

use App\Container;
use App\Import\Exceptions\FileNotFoundException;
use App\Import\Exceptions\UnsupportedFileTypeException;
use App\Import\Importer;
use App\Import\ShipmentRecord;
use App\Tradeflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImporterTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_successfully()
    {
        $this->assertEquals(0, Tradeflow::count());
        $this->assertEquals(0, Container::count());

        $importer = new Importer([
            new ShipmentRecord(373223, 'CFL MULTIMODAL SA', 'FCIU 658783-1', 5700696000),
            new ShipmentRecord(373500, 'CONTAINER TERMINAL UTRECHT bv', 'MRKU 871936-0', 5700697251),
        ]);

        $importResult = $importer->import();

        $this->assertEquals(2, Tradeflow::count());
        $this->assertEquals(2, Container::count());
        $this->assertEquals('5700696000', $importResult->getTradeflows()[0]->name);
        $this->assertEquals('5700697251', $importResult->getTradeflows()[1]->name);
        $this->assertEquals('FCIU 658783-1', $importResult->getTradeflows()[0]->containers[0]->reference);
        $this->assertEquals('MRKU 871936-0', $importResult->getTradeflows()[1]->containers[0]->reference);
        $this->assertEmpty($importResult->getTradeflowsWithoutContainers());
        $this->assertEmpty($importResult->getInvalidContainerReferences());
    }

    public function test_tradeflows_and_containers_are_not_duplicated_if_same_records_imported_multiple_times()
    {
        $importer = new Importer([
            new ShipmentRecord(373223, 'CFL MULTIMODAL SA', 'FCIU 658783-1', 5700696000),
            new ShipmentRecord(373500, 'CONTAINER TERMINAL UTRECHT bv', 'MRKU 871936-0', 5700697251),
        ]);

        $importer->import();
        $importer->import();

        $this->assertEquals(2, Tradeflow::count());
        $this->assertEquals(2, Container::count());
    }

    public function test_import_tradeflow_with_multiple_containers_associated()
    {
        $importer = new Importer([
            new ShipmentRecord(373507, 'CONTAINER TERMINAL UTRECHT bv', 'GLDU 543261-0', 5700697255),
            new ShipmentRecord(373507, 'CONTAINER TERMINAL UTRECHT bv', 'HAMU 102064-4', 5700697255),
            new ShipmentRecord(373507, 'CONTAINER TERMINAL UTRECHT bv', 'TCKU 279837-5', 5700697255),
        ]);

        $importResult = $importer->import();

        $this->assertEquals(['GLDU 543261-0', 'HAMU 102064-4', 'TCKU 279837-5'], $importResult->getTradeflows()[0]->containers->pluck('reference')->toArray());
    }

    public function test_import_container_belongs_to_multiple_tradeflows()
    {
        $importer = new Importer([
            new ShipmentRecord(373668, 'CFL MULTIMODAL SA', 'FCIU 660212-9', 5700697633),
            new ShipmentRecord(373712, 'CFL MULTIMODAL SA', 'FCIU 660212-9', 5700697690),
        ]);

        $importResult = $importer->import();

        $this->assertEquals('FCIU 660212-9', $importResult->getTradeflows()[0]->containers->first()->reference);
        $this->assertEquals('FCIU 660212-9', $importResult->getTradeflows()[1]->containers->first()->reference);
    }

    public function test_records_with_invalid_container_references_are_not_imported()
    {
        $importer = new Importer([
            new ShipmentRecord(373223, 'CFL MULTIMODAL SA', 'FCIU 658783-1', 5700696000),
            new ShipmentRecord(373500, 'CONTAINER TERMINAL UTRECHT bv', 'not-valid-ref', 5700697251),
        ]);

        $importResult = $importer->import();

        $this->assertEquals(1, Tradeflow::count());
        $this->assertCount(1, $importResult->getTradeflows());
        $this->assertEquals('5700696000', $importResult->getTradeflows()[0]->name);
        $this->assertEquals(['not-valid-ref'], $importResult->getInvalidContainerReferences());
    }

    public function test_tradeflows_without_any_valid_container_references_are_not_imported()
    {
        $importer = new Importer([
            new ShipmentRecord(373223, 'CFL MULTIMODAL SA', 'not-valid-ref-1', 5700696000),
            new ShipmentRecord(373223, 'CFL MULTIMODAL SA', 'not-valid-ref-2', 5700696000),
        ]);

        $importResult = $importer->import();

        $this->assertEquals(0, Tradeflow::count());
        $this->assertCount(0, $importResult->getTradeflows());
        $this->assertEquals(['5700696000'], $importResult->getTradeflowsWithoutContainers());
    }

    public function test_import_from_path_successfully()
    {
        $importer = Importer::forFile($this->getStubPath('3valid.xlsx'));

        $importer->import();

        $this->assertEquals(3, Tradeflow::count());
        $this->assertEquals(3, Container::count());
        $this->assertEquals(['5700696000', '5700697633', '5700697684'], Tradeflow::pluck('name')->toArray());
        $this->assertEquals(['FCIU 658783-1', 'FCIU 660212-9', 'HASU 129740-9'], Container::pluck('reference')->toArray());
    }

    public function test_import_from_non_existing_path()
    {
        $this->expectException(FileNotFoundException::class);

        Importer::forFile('../not-existing-path.xlsx');
    }

    public function test_import_from_not_supported_file_format()
    {
        $this->expectException(UnsupportedFileTypeException::class);

        Importer::forFile($this->getStubPath('text-file'));
    }
}
