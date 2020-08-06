<?php

namespace App\Import;

use Illuminate\Support\Collection;

class Tradeflow
{
    /**
     * The name of the tradeflow.
     *
     * @var string
     */
    private string $name;

    /**
     * All shipment records for this tradeflow regardless their validity.
     *
     * @var ShipmentRecord[]
     */
    private array $shipmentRecords = [];

    /**
     * Shipment records that are valid and importable.
     *
     * @var ShipmentRecord[]
     */
    private array $importableShipmentRecords = [];

    /**
     * Shipment records that have invalid container references.
     *
     * @var ShipmentRecord[]
     */
    private array $shipmentRecordsWithInvalidContainerReference = [];

    public function __construct(string $name, array $shipmentRecords)
    {
        $this->name = $name;
        $this->shipmentRecords = $shipmentRecords;

        foreach($this->shipmentRecords as $shipmentRecord) {
            if ($shipmentRecord->isImportable()) {
                $this->importableShipmentRecords[] = $shipmentRecord;
                continue;
            }

            if (!$shipmentRecord->hasValidContainerRef()) {
                $this->shipmentRecordsWithInvalidContainerReference[] = $shipmentRecord;
            }
        }
    }

    /**
     * Returns the name of the tradeflow
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns all the shipment records provided for this tradeflow regardless their validity.
     *
     * @return ShipmentRecord[]
     */
    public function getShipmentRecords(): array
    {
        return $this->shipmentRecords;
    }

    /**
     * Returns if tradeflow has importabled shipment records.
     *
     * @return bool
     */
    public function hasImportableShipmentRecords(): bool
    {
        return count($this->importableShipmentRecords) > 0;
    }

    /**
     * Returns only the shipment records that are importable.
     *
     * @return ShipmentRecord[]
     */
    public function getImportableShipmentRecords(): array
    {
        return $this->importableShipmentRecords;
    }

    /**
     * Returns only the shipment records that has invalid container references.
     *
     * @return ShipmentRecord[]
     */
    public function getShipmentRecordsWithInvalidContainerReference(): array
    {
        return $this->shipmentRecordsWithInvalidContainerReference;
    }
}
