<?php

namespace App\Import;

use App\Import\Exceptions\FileNotFoundException;
use App\Import\Exceptions\ImportException;
use App\Import\Exceptions\ImportFailedException;
use App\Import\Exceptions\UnsupportedFileTypeException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Tradeflow as TradeflowModel;
use App\Container as ContainerModel;
use Throwable;

class Importer
{
    const SUPPORTED_MIME_TYPES = [
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-excel'
    ];

    /**
     * The shipment records to be imported.
     *
     * @var ShipmentRecord[]|Collection
     */
    private Collection $records;

    public function __construct(array $records)
    {
        $this->records = collect($records);
    }

    /**
     * Create importer for file in given path.
     *
     * @param string $path
     * @return static
     * @throws FileNotFoundException|UnsupportedFileTypeException
     */
    public static function forFile(string $path): self
    {
        if (!is_file($path)) {
            throw new FileNotFoundException($path);
        }

        $mimeType = mime_content_type($path);

        if (!in_array($mimeType, static::SUPPORTED_MIME_TYPES)) {
            throw new UnsupportedFileTypeException($mimeType);
        }

        $spreadsheet = IOFactory::load($path);
        $rows = $spreadsheet->getActiveSheet()->toArray();

        // Remove first row as it contains column names
        array_shift($rows);

        return new self(array_map(fn($data) => new ShipmentRecord(...$data), $rows));
    }

    /**
     * Processes and imports the given shipment records.
     *
     * @return ImportResult
     * @throws ImportException
     */
    public function import(): ImportResult
    {
        $tradeflows = $this->groupRecordsIntoTradeflows();

        $tradeflowsWithoutContainers = collect();
        $invalidContainerReferences = collect();
        $persistedTradeflows = collect();

        // Since we will be creating multiple records
        // We need to use transaction so that we can rollback all changes
        // to prevent any data consistency problems if an exception occurs along the way
        DB::beginTransaction();

        try {
            foreach ($tradeflows as $tradeflow) {
                foreach ($tradeflow->getShipmentRecordsWithInvalidContainerReference() as $invalidShipmentRecord) {
                    $invalidContainerReferences->add($invalidShipmentRecord->getContainerReference());
                }

                if (!$tradeflow->hasImportableShipmentRecords()) {
                    $tradeflowsWithoutContainers->add($tradeflow->getName());
                    continue;
                }

                $persistedTradeflows->add($this->persistTradeflow($tradeflow));
            }
        } catch (Throwable $e) {
            DB::rollBack();

            throw new ImportFailedException($e);
        }

        DB::commit();

        return new ImportResult(
            $persistedTradeflows->values()->all(),
            $invalidContainerReferences->all(),
            $tradeflowsWithoutContainers->all(),
        );
    }

    /**
     * Persist given tradeflow with its containers
     *
     * @param Tradeflow $tradeflow
     * @return TradeflowModel
     */
    private function persistTradeflow(Tradeflow $tradeflow): TradeflowModel
    {
        // Do not create new tradeflow in case the same data is imported multiple times
        $persistedTradeflow = TradeflowModel::firstOrCreate(['name' => $tradeflow->getName()]);

        foreach ($tradeflow->getImportableShipmentRecords() as $importableShipmentRecord) {
            // Do not create new container in case the same data is imported multiple times
            $container = ContainerModel::firstOrCreate([
                'reference' => $importableShipmentRecord->getContainerReference(),
            ]);

            if (!$persistedTradeflow->containers()->whereId($container->id)->exists()) {
                $persistedTradeflow->containers()->attach($container->id);
            }
        }

        return $persistedTradeflow;
    }

    /**
     * Groups records into tradeflows by their order no.
     *
     * @return Tradeflow[]|Collection
     */
    private function groupRecordsIntoTradeflows(): Collection
    {
        return $this->records
            ->groupBy(fn(ShipmentRecord $record) => $record->getOrderNo())
            ->map(function (Collection $recordsInSameOrder) {
                return new Tradeflow($recordsInSameOrder->first()->getTradeflowName(), $recordsInSameOrder->toArray());
            });
    }
}
